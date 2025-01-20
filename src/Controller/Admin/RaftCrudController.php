<?php

namespace App\Controller\Admin;

use App\Entity\Raft;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Contracts\Translation\TranslatorInterface;

class RaftCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Raft::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, $this->translator->trans('raft.pageTitle.index'))
            ->setPageTitle(Crud::PAGE_NEW, $this->translator->trans('raft.pageTitle.new'))
            ->setPageTitle(Crud::PAGE_DETAIL, fn(Raft $raft) => $raft->getName())
            ->setPageTitle(Crud::PAGE_EDIT, $this->translator->trans('raft.pageTitle.edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', $this->translator->trans('raft.field.name.label'));
        yield NumberField::new('places', $this->translator->trans('raft.field.places.label'));
        yield AssociationField::new('company', $this->translator->trans('raft.field.company.label'));
        yield ImageField::new('image', $this->translator->trans('raft.field.image.label'))
            ->setBasePath('')
            ->setUploadDir('public/images/rafts')
            ->setUploadedFileNamePattern('images/rafts/[randomhash].[extension]')
            ->formatValue(function ($value, ?Raft $entity) {
                if (null === $entity) {
                    return '';
                }

                return str_replace('public/', '', $value);
            })
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->hideOnIndex();
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
                fn(Action $action) => $action->setLabel($this->translator->trans('raft.action.new'))
            );
    }
}
