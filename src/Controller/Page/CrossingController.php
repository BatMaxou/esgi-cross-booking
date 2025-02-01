<?php

namespace App\Controller\Page;

use App\Entity\Crossing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CrossingController extends AbstractController
{
    #[Route(path: '/crossings', name: 'crossings')]
    public function crossings(): Response
    {
        return $this->render('page/crossing/list.html.twig', [
            'crossings' => [],
        ]);
    }

    #[Route(path: '/crossings/{id}', name: 'crossing')]
    public function crossing(Crossing $crossing): Response
    {
        return $this->render('page/crossing/index.html.twig', [
            'crossing' => $crossing,
        ]);
    }
}
