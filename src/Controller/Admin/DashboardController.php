<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Crossing;
use App\Entity\Port;
use App\Entity\Raft;
use App\Entity\Reservation\SimpleReservation;
use App\Entity\Reservation\TeamReservation;
use App\Entity\Review;
use App\Entity\Route as EntityRoute;
use App\Entity\SiteMessage;
use App\Entity\Team;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/admin', name: 'my_account')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user || !$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        /** @var AdminUrlGenerator */
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect(
            $adminUrlGenerator->setController(UserCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($user->getId())
                ->generateUrl()
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->translator->trans('dashboard.title'));
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute($this->translator->trans('dashboard.menu.account'), 'fas fa-user', 'my_account');
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.users'), 'fas fa-users', User::class);
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.companies'), 'fas fa-building', Company::class);
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.ports'), 'fas fa-anchor', Port::class);
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.routes'), 'fas fa-arrow-right-arrow-left', EntityRoute::class);
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.teams'), 'fas fa-people-group', Team::class);
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.rafts'), 'fas fa-ship', Raft::class);
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.crossings'), 'fas fa-map-location-dot', Crossing::class);
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.reviews'), 'fas fa-comments', Review::class);
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.reservations.simple'), 'fas fa-id-card', SimpleReservation::class);
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.reservations.team'), 'fas fa-users-between-lines', TeamReservation::class);
        yield MenuItem::linkToCrud($this->translator->trans('dashboard.menu.siteMessages'), 'fas fa-quote-left', SiteMessage::class);
    }
}
