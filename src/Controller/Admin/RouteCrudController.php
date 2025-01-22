<?php

namespace App\Controller\Admin;

use App\Entity\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Contracts\Translation\TranslatorInterface;

class RouteCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Route::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, $this->translator->trans('route.pageTitle.index'))
            ->setPageTitle(Crud::PAGE_NEW, $this->translator->trans('route.pageTitle.new'))
            ->setPageTitle(Crud::PAGE_DETAIL, fn (Route $route) => sprintf('%s %s', $route->getFromPort()->getName(), $route->getToPort()->getName()))
            ->setPageTitle(Crud::PAGE_EDIT, $this->translator->trans('route.pageTitle.edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('fromPort', $this->translator->trans('route.field.fromPort.label'));
        yield AssociationField::new('toPort', $this->translator->trans('route.field.toPort.label'));
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
                fn (Action $action) => $action->setLabel($this->translator->trans('route.action.new'))
            );
    }
}
