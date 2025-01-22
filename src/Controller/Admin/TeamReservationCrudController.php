<?php

namespace App\Controller\Admin;

use App\Entity\Reservation\TeamReservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Contracts\Translation\TranslatorInterface;

class TeamReservationCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return TeamReservation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, $this->translator->trans('reservation.pageTitle.index'))
            ->setPageTitle(Crud::PAGE_NEW, $this->translator->trans('reservation.pageTitle.new'))
            ->setPageTitle(Crud::PAGE_DETAIL, fn (TeamReservation $reservation) => sprintf(
                'Reservation du groupe %s - %s -> %s / %s | id: %d',
                $reservation->getTeam()->getName(),
                $reservation->getCrossing()->getRoute()->getFromPort()->getName(),
                $reservation->getCrossing()->getRoute()->getToPort()->getName(),
                $reservation->getCrossing()->getDate()->format('Y-m-d H:i'),
                $reservation->getId(),
            ))
            ->setPageTitle(Crud::PAGE_EDIT, $this->translator->trans('reservation.pageTitle.edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('team', $this->translator->trans('reservation.field.team.label'));
        yield AssociationField::new('crossing', $this->translator->trans('reservation.field.crossing.label'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel($this->translator->trans('reservation.action.new'))
            );
    }
}
