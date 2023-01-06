<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home_scrutin');
    }

    #[Route('/scrutin/{path<.+>}', name: 'app_home_scrutin')]
    public function scrutin($path = ''): Response
    {
        return $this->render('main/index.html.twig');
    }
}
