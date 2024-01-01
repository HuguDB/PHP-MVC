<?php

namespace App\Controller;

use App\Routing\Attribute\Route;
use App\Utils\SessionManager;
use Doctrine\ORM\EntityManager;

class IndexController extends AbstractController
{
    #[Route('/', 'home')]
    public function home(): string
    {
        return $this->twig->render('index/home.html.twig');
    }

    #[Route('/contact', 'contact')]
    public function contact(): string
    {
        return $this->twig->render('index/contact.html.twig');
    }

    #[Route('/about', 'about')]
    public function about(EntityManager $em): string
    {

        return $this->twig->render('index/about.html.twig');
    }




}
