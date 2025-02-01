<?php

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    #[Route(path: '/account', name: 'account')]
    public function account(): Response
    {
        return $this->render('page/account/index.html.twig');
    }
}
