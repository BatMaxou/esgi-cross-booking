<?php

namespace App\Controller\Page;

use App\Entity\Team;
use App\Entity\User;
use App\Enum\VoterRoleEnum;
use App\Form\MembersType;
use App\Form\TeamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(VoterRoleEnum::UNBANED->value)]
class TeamController extends AbstractController
{
    #[Route(path: '/teams/create', name: 'team_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);

        try {
            $form->handleRequest($request);
        } catch (\Exception) {
            $this->addFlash('danger', 'Une erreur est survenue lors de la création de votre équipe.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user instanceof User) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la création de votre équipe.');

                return $this->redirectToRoute('team_create');
            }

            $team->setCreator($user);

            $em->persist($team);
            $em->flush();

            $this->addFlash('success', 'Votre équipe a bien été créée.');

            return $this->redirectToRoute('team', ['id' => $team->getId()]);
        }

        return $this->render('page/team/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/teams/{id}', name: 'team')]
    public function team(
        Request $request,
        Team $team,
        EntityManagerInterface $em,
    ): Response {
        $form = null;
        $user = $this->getUser();
        if ($user instanceof User && $team->getCreator() === $user) {
            $form = $this->createForm(MembersType::class, $team);

            try {
                $form->handleRequest($request);
            } catch (\Exception) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la modification de votre équipe.');
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

                $this->addFlash('success', 'Votre équipe a bien été modifiée.');
            }

            $form = $form->createView();
        }

        $passedReservations = [];
        $reservations = [];

        foreach ($team->getTeamReservations() as $reservation) {
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

        return $this->render('page/team/index.html.twig', [
            'team' => $team,
            'passedReservations' => $passedReservations,
            'reservations' => $reservations,
            'form' => $form,
        ]);
    }

    // A faire si le temps le permet
    #[Route(path: '/teams/{id}/join/{token}', name: 'team_join')]
    public function join(
        // Team $team,
        // string $token,
    ): Response {
        return new Response('TODO');

        // return $this->render('page/team/join.html.twig', [
        // 'team' => $team,
        // 'token' => $token,
        // ]);
    }
}
