<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReviewCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Review::class;
    }

    public function createEntity(string $entityFqcn): Review
    {
        return (new Review())
            ->setAuthor($this->getUser());
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, $this->translator->trans('review.pageTitle.index'))
            ->setPageTitle(Crud::PAGE_NEW, $this->translator->trans('review.pageTitle.new'))
            ->setPageTitle(Crud::PAGE_DETAIL, fn(Review $review) => sprintf(
                'Review de %s %s - %s -> %s / %s | id: %d',
                $review->getAuthor()->getFirstName(),
                $review->getAuthor()->getLastName(),
                $review->getCrossing()->getRoute()->getFromPort()->getName(),
                $review->getCrossing()->getRoute()->getToPort()->getName(),
                $review->getCrossing()->getDate()->format('Y-m-d H:i'),
                $review->getId(),
            ))
            ->setPageTitle(Crud::PAGE_EDIT, $this->translator->trans('review.pageTitle.edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('author', $this->translator->trans('review.field.author.label'))
            ->hideOnForm();
        yield AssociationField::new('crossing', $this->translator->trans('review.field.crossing.label'));
        yield TextareaField::new('content', $this->translator->trans('review.field.content.label'));
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
                fn(Action $action) => $action->setLabel($this->translator->trans('review.action.new'))
            );
    }
}
