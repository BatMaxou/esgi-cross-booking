<?php

namespace App\Controller\Page;

use App\Enum\SiteMessagePlaceEnum;
use App\Enum\VoterRoleEnum;
use App\Repository\SiteMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(VoterRoleEnum::BANNED->value)]
class BanController extends AbstractController
{
    #[Route(path: '/ban', name: 'ban')]
    public function ban(SiteMessageRepository $siteMessageRepository): Response
    {
        return $this->render('page/ban/index.html.twig', [
            'message' => $siteMessageRepository->findByPlace(SiteMessagePlaceEnum::BAN)?->getContent() ?? '',
        ]);
    }
}
