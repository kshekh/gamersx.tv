<?php

namespace App\Controller\Admin;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use App\Entity\HomeRowItemOperation;
use App\Entity\Partner;
use App\Entity\PartnerRole;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/admin/dashboard', name: 'admin')]
    public function index(): Response
    {
        return $this->render('dashboard.html.twig');

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('GamersX Admin')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToRoute('Home', 'fa fa-home', $this->generateUrl('home')),
            MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard'),
            MenuItem::linkToCrud('Containers', 'fas fa-grip', HomeRow::class),
            MenuItem::linkToCrud('Home Row Items', 'fas fa-box-open', HomeRowItem::class),
            MenuItem::linkToCrud('Item Operations', 'fas fa-list', HomeRowItemOperation::class),
            MenuItem::linkToCrud('Partners', 'fas fa-handshake', Partner::class),
            MenuItem::linkToCrud('Partner Roles', 'fas fa-user-tie', PartnerRole::class),
            MenuItem::linkToCrud('Users', 'fas fa-users', User::class),
        ];
    }
}
