<?php

namespace App\Controller\Page;

use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
    #[Route(path: '/teams/create', name: 'team_create')]
    public function create(): Response
    {
        return $this->render('page/team/create.html.twig');
    }

    #[Route(path: '/teams/{id}', name: 'team')]
    public function team(Team $team): Response
    {
        return $this->render('page/team/index.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route(path: '/teams/{id}/join/{token}', name: 'team_join')]
    public function join(Team $team, string $token): Response
    {
        return $this->render('page/team/join.html.twig', [
            'team' => $team,
            'token' => $token,
        ]);
    }
}
