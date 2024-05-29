<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\{ HeaderUtils, Request, Response, ResponseHeaderBag, RedirectResponse };
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class HomeRowAdminController extends CRUDController
{
    private $serializer;
    private $filesystem;

    public function __construct(SerializerInterface $serializer, Filesystem $filesystem)
    {
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
    }

    public function reorder(Request $request, $id): Response
    {
        $object = $this->admin->getSubject();
        $direction = $request->get('direction');

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $qb = $this->admin->getModelManager()->getEntityManager('App:HomeRow')
            ->createQueryBuilder()
            ->addSelect('hr')
            ->from('App:HomeRow', 'hr');
            ;

        if ($direction === 'down') {
            $qb->where('hr.sortIndex >= :thisSort')
                ->add('orderBy', 'hr.sortIndex ASC');
        } elseif ($direction === 'up') {
            $qb->where('hr.sortIndex <= :thisSort')
                ->add('orderBy', 'hr.sortIndex DESC');
        }
        $qb->setParameter('thisSort', $object->getSortIndex());

        $rows = $qb->getQuery()->getResult();

        if (count($rows) > 1) {

            foreach ($rows as $row) {
                if ($row->getId() === $object->getId()) {
                    $current = $row->getSortIndex();
                } elseif (isset($current) && $row->getId() !== $object->getId()) {
                    $object->setSortIndex($row->getSortIndex());
                    $this->admin->getModelManager()->update($object);

                    $row->setSortIndex($current);
                    $this->admin->getModelManager()->update($row);
                    $this->addFlash('sonata_flash_success', "Swapped position for rows ".$object->getTitle()." and ".$row->getTitle());
                    break;
                }

            }
        } else {
            $this->addFlash('sonata_flash_error', "Moving ".$object->getTitle()." that way is not possible.");
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
        );
    }

    public function importForm()
    {
        return $this->render('admin/import_form.html.twig');
    }

    public function import(Request $request)
    {
        $this->admin->checkAccess('create');
        $file = $request->files->get('import');

        $archive = new \ZipArchive();
        $archive->open($file);

        if ($archive) {
            try {
                for ( $i = 0; $i < $archive->numFiles; $i++ ) {
                    $json = $archive->getFromIndex($i);
                    $row = $this->serializer->deserialize($json, $this->admin->getClass(), 'json');
                    $row->setIsPublished(FALSE);
                    $row->setPartner(NULL);
                    $this->admin->getModelManager()->create($row);
                }

                $archive->close();
                $this->addFlash('sonata_flash_success', "Successfully imported $i home rows.");
            } catch (\Exception $e) {
                $this->addFlash('sonata_flash_error', 'Couldn\'t import Home Row file');

                return new RedirectResponse(
                    $this->admin->generateUrl('list', [
                        'filter' => $this->admin->getFilterParameters()
                    ])
                );
            }
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ])
        );
    }

    public function batchActionExport(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('list');
        $selectedModels = $selectedModelQuery->execute();

        $archive = new \ZipArchive();
        $filename = $this->filesystem->tempnam(sys_get_temp_dir(), 'export_');

        try {
            $archive->open($filename, \ZipArchive::CREATE);

            foreach ($selectedModels as $selectedModel) {
                $json = $this->serializer->serialize($selectedModel, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['partner', 'items']]);
                $archive->addFromString($selectedModel.'-'.$selectedModel->getId().'.json', $json);
            }

            $archive->close();

        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'Couldn\'t create Zip file for export');

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $response = new BinaryFileResponse($filename);
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'gamersx-home-row-export-'.time().'.zip'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->deleteFileAfterSend(TRUE);
        return $response;
    }
}
