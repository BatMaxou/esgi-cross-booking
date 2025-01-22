<?php

namespace App\Controller\Admin;

use App\Entity\Crossing;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Contracts\Translation\TranslatorInterface;

class CrossingCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Crossing::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, $this->translator->trans('crossing.pageTitle.index'))
            ->setPageTitle(Crud::PAGE_NEW, $this->translator->trans('crossing.pageTitle.new'))
            ->setPageTitle(Crud::PAGE_DETAIL, fn (Crossing $crossing) => sprintf(
                '%s / %s - %s',
                $crossing->getRoute()->getFromPort()->getName(),
                $crossing->getRoute()->getToPort()->getName(),
                $crossing->getDate()->format('Y-m-d H:i')
            ))
            ->setPageTitle(Crud::PAGE_EDIT, $this->translator->trans('crossing.pageTitle.edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('date', $this->translator->trans('crossing.field.date.label'));
        yield AssociationField::new('route', $this->translator->trans('crossing.field.route.label'));
        yield AssociationField::new('rafts', $this->translator->trans('crossing.field.rafts.label'))
            ->setFormTypeOption('choice_label', 'name')
            ->setFormTypeOption('by_reference', false)
            ->hideOnIndex()
            ->formatValue(function ($value, $entity) {
                return implode(', ', $entity->getRafts()->map(function ($raft) {
                    return $raft->getName();
                })->toArray());
            });
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
                fn (Action $action) => $action->setLabel($this->translator->trans('crossing.action.new'))
            );
    }
}
