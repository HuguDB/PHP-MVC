<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Routing\Attribute\Route;
use Doctrine\ORM\EntityManager;

class LoginController extends AbstractController
{
    #[Route('/login', 'login')]
    public function loginForm(): string
    {
        return $this->twig->render('security/login.html.twig');
    }

    #[Route('/login', 'login', 'POST')]
    public function login(): string
    {
        // Vérifiez les informations d'authentification ici
        $username = $_POST['_email'];
        $password = $_POST['_password'];

        if ($username === 'utilisateur' && $password === 'motdepasse') {
            $sessionManager = new RemoveUnusedSessionMarshallingHandlerPassagernageranager();
            $sessionManager->set('user', $username);

            // Redirige vers la page d'accueil si authentification réussie
            return $this->twig->render('security/loginConfirm.html.twig');
        } else {
            // si échec d'authentification
            return $this->twig->render('security/login.html.twig', ['error' => true]);
        }
    }
    public function logout(): void
    {
        $this->redirect('/login');
    }

    #[Route('/login/confirm', 'login_confirm')]
    public function confirm(): string
    {
        return $this->twig->render('security/loginConfirm.html.twig');
    }
}
