<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Contracts\Translation\TranslatorInterface;

class TeamCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Team::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, $this->translator->trans('team.pageTitle.index'))
            ->setPageTitle(Crud::PAGE_NEW, $this->translator->trans('team.pageTitle.new'))
            ->setPageTitle(Crud::PAGE_DETAIL, fn (Team $team) => $team->getName())
            ->setPageTitle(Crud::PAGE_EDIT, $this->translator->trans('team.pageTitle.edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', $this->translator->trans('team.field.name.label'));
        yield AssociationField::new('creator', $this->translator->trans('team.field.creator.label'));
        yield AssociationField::new('members', $this->translator->trans('team.field.members.label'))
            ->setFormTypeOption('choice_label', 'firstName')
            ->setFormTypeOption('by_reference', false)
            ->hideOnIndex()
            ->formatValue(function ($value, $entity) {
                return implode(', ', $entity->getMembers()->map(function ($member) {
                    return sprintf('%s %s', $member->getFirstName(), $member->getLastName());
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
                fn (Action $action) => $action->setLabel($this->translator->trans('team.action.new'))
            );
    }
}
