<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('app_home_scrutin');
         }

        return $this->render('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/admin/login', name: 'admin_login')]
    public function adminLogin(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('admin_index');
         }

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
//            'page_title' => Mixins::getDashboardTitle($this->manifestFilePath, $this->decoder),
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('admin_index'),
            'username_label' => 'Adresse mail',
            'password_label' => 'Mot de passe',
            'sign_in_label' => 'Se connecter',
//            'username_parameter' => 'email',
//            'password_parameter' => 'password',
//            'forgot_password_enabled' => true,
//            'forgot_password_label' => 'Mot de passe oublié ?',
//            'forgot_password_path' => $this->generateUrl('app_admin_password_forgotten'),
//            'remember_me_enabled' => true,
//            'remember_me_parameter' => '_remember_me',
//            'remember_me_checked' => true,
//            'remember_me_label' => 'Resté connecté',
        ]);
    }

    #[Route(path: '/admin/logout', name: 'admin_logout')]
    public function adminLogout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
