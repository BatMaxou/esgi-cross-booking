<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Enum\VoterRoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[IsGranted(VoterRoleEnum::ADMIN->value)]
class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly Security $security,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function detail(AdminContext $context): KeyValueStore|Response
    {
        $entityInstance = $context->getEntity()->getInstance();

        if ($this->getUser() === $entityInstance) {
            $this->activateMyAccountMenuItem($context);
        }

        return parent::detail($context);
    }

    public function createEntity(string $entityFqcn): User
    {
        return (new User())
            ->addRole(RoleEnum::ADMIN);
    }

    public function debanUser(EntityManagerInterface $entityManager, AdminContext $adminContext): Response
    {
        $entityInstance = $adminContext->getEntity()->getInstance();

        if (!$entityInstance instanceof User) {
            throw new \RuntimeException(sprintf('Invalid entity instance, expected: %s', User::class));
        }

        $entityInstance->removeRole(RoleEnum::BANNED);
        $entityManager->flush();

        return $this->redirectToIndex();
    }

    public function banUser(EntityManagerInterface $entityManager, AdminContext $adminContext): Response
    {
        $entityInstance = $adminContext->getEntity()->getInstance();

        if (!$entityInstance instanceof User) {
            throw new \RuntimeException(sprintf('Invalid entity instance, expected: %s', User::class));
        }

        $entityInstance->addRole(RoleEnum::BANNED);
        $entityManager->flush();

        return $this->redirectToIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, $this->translator->trans('user.pageTitle.index'))
            ->setPageTitle(Crud::PAGE_NEW, $this->translator->trans('user.pageTitle.new'))
            ->setPageTitle(Crud::PAGE_DETAIL, fn (User $user) => sprintf('%s %s', $user->getFirstName(), $user->getLastName()))
            ->setPageTitle(Crud::PAGE_EDIT, $this->translator->trans('user.pageTitle.edit'));
    }

    public function configureFields(string $pageName): iterable
    {
        $roleMap = [
            RoleEnum::ADMIN->value => 'warning',
            RoleEnum::USER->value => 'info',
            RoleEnum::BANNED->value => 'danger',
        ];

        yield TextField::new('firstName', $this->translator->trans('user.field.firstName.label'));
        yield TextField::new('lastName', $this->translator->trans('user.field.lastName.label'));
        yield EmailField::new('email', $this->translator->trans('user.field.email.label'))
            ->setFormTypeOptions([
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('user.field.email.error.notBlank'),
                    ]),
                    new Email([
                        'message' => $this->translator->trans('user.field.email.error.email'),
                    ]),
                ],
            ]);
        yield TextField::new('password')
            ->setFormType(PasswordType::class)
            ->setFormTypeOptions([
                'label' => $this->translator->trans('user.field.password.label'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('user.field.password.error.notBlank'),
                    ]),
                ],
            ])
            ->setRequired(Crud::PAGE_NEW === $pageName)
            ->onlyOnForms();
        yield ChoiceField::new('roles', $this->translator->trans('user.field.role.label'))
            ->formatValue(function ($value, ?User $entity) use ($roleMap) {
                if (null === $entity) {
                    return '';
                }

                $roles = $entity->getRoles();
                $render = '';

                foreach ($roles as $role) {
                    $render .= sprintf(
                        '<span class="badge badge-pill badge-%s">%s</span>',
                        $roleMap[$role],
                        $this->translator->trans($role),
                    );
                }

                return $render;
            })
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $debanAction = $this->getDebanAction();
        $banAction = $this->getBanAction();

        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $debanAction)
            ->add(Crud::PAGE_INDEX, $banAction)
            ->add(Crud::PAGE_DETAIL, $debanAction)
            ->add(Crud::PAGE_DETAIL, $banAction)
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel($this->translator->trans('user.action.new'))
            )
            ->update(
                Crud::PAGE_INDEX,
                'debanUser',
                fn (Action $action) => $action->addCssClass('text-success')
            )
            ->update(
                Crud::PAGE_INDEX,
                'banUser',
                fn (Action $action) => $action->addCssClass('text-danger')
            )
            ->update(
                Crud::PAGE_DETAIL,
                'debanUser',
                fn (Action $action) => $action
                    ->addCssClass('btn btn-success')
            )
            ->update(
                Crud::PAGE_DETAIL,
                'banUser',
                fn (Action $action) => $action
                    ->addCssClass('btn btn-danger')
            );
    }

    private function activateMyAccountMenuItem(AdminContext $context): void
    {
        $menu = $context->getMainMenu()->getItems();

        foreach ($menu as $item) {
            if ('my_account' === $item->getRouteName()) {
                $item->setSelected(true);
            } else {
                $item->setSelected(false);
            }
        }
    }

    private function getDebanAction(): Action
    {
        return Action::new('debanUser', $this->translator->trans('user.action.deban'))
            ->linkToCrudAction('debanUser')
            ->displayIf(fn (User $entity) => $entity->isBanned());
    }

    private function getBanAction(): Action
    {
        return Action::new('banUser', $this->translator->trans('user.action.ban'))
            ->linkToCrudAction('banUser')
            ->displayIf(fn (User $entity) => !$entity->isBanned());
    }

    private function redirectToIndex(): Response
    {
        return $this->redirect(
            $this->adminUrlGenerator->setController(UserCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }
}
