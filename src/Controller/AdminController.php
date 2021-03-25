<?php

namespace App\Controller;

use App\Service\RowSettings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/settings/upload", name="settings_upload")
     */
    public function index(Request $request, EntityManagerInterface $em, RowSettings $settingsService): Response
    {
        try {
            $settingsFile = $request->files->get('settings');
            $settings = $settingsService->getSettingsFromJsonFile($settingsFile);
            $rows = $settingsService->toEntities($settings);
            foreach ($rows as $row) {
                $em->persist($row);
                foreach ($row->getItems() as $item) {
                    $item->setHomeRow($row);
                    $em->persist($item);
                }
            }
            $em->flush();
            $settingsService->writeSettingsToJsonFile($settings);
        } catch (\Exception $ex) {
            $this->addFlash('error', 'Settings have not been saved: '.$ex->getMessage());
            return $this->redirectToRoute('sonata_admin_dashboard');
        }
        $this->addFlash('success', 'Settings have been saved!');
        return $this->redirectToRoute('sonata_admin_dashboard');
    }
}
