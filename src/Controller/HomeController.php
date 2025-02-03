<?php

namespace App\Controller;

use App\Enum\SiteMessagePlaceEnum;
use App\Repository\CrossingRepository;
use App\Repository\SiteMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'home')]
    public function index(
        SiteMessageRepository $siteMessageRepository,
        CrossingRepository $crossingRepository,
    ): Response {
        return $this->render('index.html.twig', [
            'bannerMessage' => $siteMessageRepository->findByPlace(SiteMessagePlaceEnum::HOME)?->getContent() ?? '',
            'crossings' => $crossingRepository->findLastCrossings(8),
        ]);
    }
}
