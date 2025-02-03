<?php

namespace App\Controller\Auth;

use App\Domain\SendForgotPasswordEmailCommand;
use App\Domain\SendWelcomeEmailCommand;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;

class SecurityController extends AbstractController
{
    private string $domain;

    public function __construct(
        string $domain,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly Validator $validator,
        private readonly MessageBusInterface $bus,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
        $this->domain = empty($domain)
            ? substr($urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL), 0, -1)
            : $domain;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            $this->addFlash('error', 'Identifiants incorrects');
        }

        if ($user = $this->getUser()) {
            if (!$user instanceof User) {
                throw new \LogicException('Entity User not found');
            }

            return $this->redirectToRoute($user->isAdmin() ? 'dashboard' : 'home');
        }

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        $email = $request->request->get('email', null);
        $phone = $request->request->get('phone', null);
        $firstName = $request->request->get('firstName', null);
        $lastName = $request->request->get('lastName', null);

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password', null);
            $confirm = $request->request->get('confirm', null);

            if (
                is_string($password)
                && is_string($confirm)
                && is_string($email)
                && is_string($phone)
                && is_string($firstName)
                && is_string($lastName)
                && $this->validator->validatePassword($password, $confirm)
                && $this->validator->validateEmail($email)
            ) {
                $user = (new User())
                    ->setFirstName($firstName)
                    ->setLastName($lastName)
                    ->setEmail($email)
                    ->setPhone($phone)
                    ->setPassword($password);

                $this->em->persist($user);
                $this->em->flush();

                $this->addFlash('success', 'Compte créé');

                $this->bus->dispatch(new SendWelcomeEmailCommand(
                    $user,
                    sprintf('%s%s', $this->domain, $this->urlGenerator->generate('app_login'))
                ));

                return $this->redirectToRoute('confirm');
            }

            $this->addFlash('error', 'Erreur lors de la création du compte, veuillez réessayer');
        }

        return $this->render('auth/register.html.twig', [
            'email' => $email,
            'phone' => $phone,
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);
    }

    #[Route('/forgot-password', name: 'forgot')]
    public function forgot(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email', null);
            $error = true;

            if ($email) {
                $user = $this->userRepository->findOneBy(['email' => $email]);
                if ($user) {
                    $token = Uuid::v4();
                    $user->setResetToken($token);
                    $this->em->flush();

                    $this->bus->dispatch(new SendForgotPasswordEmailCommand(
                        $user,
                        sprintf('%s%s', $this->domain, $this->urlGenerator->generate('reset', ['token' => $token])),
                        $token,
                    ));

                    $this->addFlash('info', 'Un email vous a été envoyé');
                    $error = false;
                }
            }

            if ($error) {
                $this->addFlash('error', 'Utilisateur introuvable');
            }
        }

        return $this->render('auth/forgot.html.twig');
    }

    #[Route('/reset', name: 'reset')]
    public function reset(Request $request): Response
    {
        $token = $request->query->get('token', null);
        if (!$token) {
            return $this->redirectToRoute('home');
        }

        $user = $this->userRepository->findOneBy(['resetToken' => $token]);
        if (!$user) {
            $this->addFlash('error', 'Token invalide');

            return $this->redirectToRoute('home');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password', null);
            $confirm = $request->request->get('confirm', null);

            if (
                is_string($password)
                && is_string($confirm)
                && $this->validator->validatePassword($password, $confirm)
            ) {
                $user->setPassword($password);
                $user->setResetToken(null);
                $this->em->flush();

                $this->addFlash('info', 'Mot de passe mis à jour');

                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Les mots de passe ne correspondent pas');
        }

        return $this->render('auth/reset.html.twig', [
            'email' => $user->getEmail(),
        ]);
    }

    #[Route('/confirm', name: 'confirm')]
    public function confirm(): Response
    {
        return $this->render('auth/confirm.html.twig');
    }
}
