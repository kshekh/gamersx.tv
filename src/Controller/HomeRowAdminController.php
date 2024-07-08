<?php

namespace App\Controller;

use App\Entity\HomeRow;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\{ HeaderUtils, Request, Response, ResponseHeaderBag, RedirectResponse };
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ZipArchive;

class HomeRowAdminController extends CRUDController
{
    private SerializerInterface $serializer;
    private Filesystem $filesystem;
    private $logger;
    private $validator;
    private EntityManagerInterface $entityManager;

    public function __construct(SerializerInterface $serializer, Filesystem $filesystem,  LoggerInterface $logger,ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    public function reorderAction(Request $request, $id): Response
    {
        $object = $this->admin->getSubject();
        $direction = $request->get('direction');

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $qb = $this->admin->getModelManager()->getEntityManager(HomeRow::class)
            ->createQueryBuilder()
            ->addSelect('hr')
            ->from('App:HomeRow', 'hr');

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
//                    $this->addFlash('sonata_flash_success', "Swapped position for rows ".$object->getTitle()." and ".$row->getTitle());
                    break;
                }

            }
        } else {
//            $this->addFlash('sonata_flash_error', "Moving ".$object->getTitle()." that way is not possible.");
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
        );
    }

    public function importFormAction(): Response
    {
        return $this->render('admin/import_form.html.twig');
    }

    public function importAction(Request $request, SerializerInterface $serializer, LoggerInterface $logger, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
    {
        $file = $request->files->get('import');

        if ($file === null) {
            $this->addFlash('error', 'No file was uploaded.');
            return $this->redirectToRoute('admin_app_homerow_list');
        }

        $archive = new \ZipArchive();
        $result = $archive->open($file->getRealPath());

        if ($result !== true) {
            $this->addFlash('error', "Couldn't import Home Row file.");
            return $this->redirectToRoute('admin_app_homerow_list');
        }

        try {
            for ($i = 0; $i < $archive->numFiles; $i++) {
                $json = $archive->getFromIndex($i);
                if ($json === false) {
                    throw new \Exception('Failed to extract file from the archive.');
                }

                try {
                    // Deserialize JSON data into HomeRow entity
                    $homeRowData = json_decode($json, true); // Decode JSON to associative array
                    foreach ($homeRowData as $rowData) {
                        $homeRow = new HomeRow();
                        $homeRow->setTitle($rowData['Title']);
                        $homeRow->setSortIndex($rowData['Sort Index']);
                        $homeRow->setLayout($rowData['Layout']);
                        $homeRow->setOptions(json_decode($rowData['Options'], true)); // If 'Options' is a JSON string, decode it
                        $homeRow->setIsPublished($rowData['Is Published']);
                        $homeRow->setIsGlowStyling($rowData['Is Glow Styling']);
                        $homeRow->setIsCornerCut($rowData['Is Corner Cut']);
                        $homeRow->setTimezone($rowData['Timezone']);
                        $homeRow->setIsPublishedStart($rowData['Is Published Start']);
                        $homeRow->setIsPublishedEnd($rowData['Is Published End']);
                        $homeRow->setOnGamersXtv($rowData['On Gamers Xtv']);
                        $homeRow->setRowPaddingTop($rowData['Row Padding Top']);
                        $homeRow->setRowPaddingBottom($rowData['Row Padding Bottom']);

                        // Persist each HomeRow entity
                        $entityManager->persist($homeRow);
                    }

                    $entityManager->flush();
                } catch (\Exception $e) {
                    // Log the error and continue with the next row
                    $logger->error('Failed to import HomeRow: ' . $e->getMessage());
                    continue;
                }
            }

            $archive->close();
            $this->addFlash('success', "Successfully imported home rows.");
        } catch (\Exception $e) {
            $archive->close();
            $this->addFlash('error', 'Could not import Home Row file: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_app_homerow_list');
    }


    public function batchActionExport(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('list');
        $selectedModels = $selectedModelQuery->execute();

        $archive = new ZipArchive();
        $filename = $this->filesystem->tempnam(sys_get_temp_dir(), 'export_');

        try {
            $archive->open($filename, ZipArchive::CREATE);

            foreach ($selectedModels as $selectedModel) {
                $json = $this->serializer->serialize($selectedModel, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['partner', 'items']]);
                $archive->addFromString($selectedModel.'-'.$selectedModel->getId().'.json', $json);
            }

            $archive->close();

        } catch (Exception $e) {
//            $this->addFlash('sonata_flash_error', 'Couldn\'t create Zip file for export');

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
