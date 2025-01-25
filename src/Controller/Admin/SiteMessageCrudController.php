<?php

namespace App\Controller\Admin;

use App\Entity\SiteMessage;
use App\Enum\SiteMessagePlaceEnum;
use App\Enum\VoterRoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(VoterRoleEnum::ADMIN->value)]
class SiteMessageCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return SiteMessage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, $this->translator->trans('siteMessage.pageTitle.index'))
            ->setPageTitle(Crud::PAGE_DETAIL, fn (SiteMessage $siteMessage) => $this->translator->trans($siteMessage->getPlace()->name))
            ->setPageTitle(Crud::PAGE_EDIT, $this->translator->trans('siteMessage.pageTitle.edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('place', $this->translator->trans('siteMessage.field.place.label'))
            ->formatValue(fn (SiteMessagePlaceEnum $enum) => $this->translator->trans($enum->name))
            ->setDisabled(true)
            ->hideOnDetail();
        yield TextareaField::new('content', $this->translator->trans('siteMessage.field.content.label'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }
}
