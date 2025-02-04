<?php

namespace App\Controller\Page;

use App\Entity\User;
use App\Enum\VoterRoleEnum;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(VoterRoleEnum::UNBANED->value)]
class AccountController extends AbstractController
{
    #[Route(path: '/account', name: 'account')]
    public function account(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);

        try {
            $form->handleRequest($request);
        } catch (\Exception) {
            $this->addFlash('danger', 'Une erreur est survenue lors de la modification de votre compte.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Votre compte a bien été modifié.');
        }

        return $this->render('page/account/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/teams', name: 'my_teams')]
    public function teams(): Response
    {
        return $this->render('page/team/list.html.twig');
    }

    #[Route(path: '/reservations', name: 'my_reservations')]
    public function reservations(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('Entity User not found');
        }

        $passedReservations = [];
        $reservations = [];

        foreach ($user->getSimpleReservations() as $reservation) {
            $date = $reservation->getCrossing()?->getDate() ?? null;
            if (null === $date) {
                continue;
            }

            if ($date < new \DateTime()) {
                $passedReservations[] = $reservation;
            } else {
                $reservations[] = $reservation;
            }
        }

        return $this->render('page/reservation/list.html.twig', [
            'reservations' => $reservations,
            'passedReservations' => $passedReservations,
        ]);
    }
}
