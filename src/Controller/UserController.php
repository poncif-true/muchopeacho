<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile()
    {
        return $this->render('user/profile.html.twig', []);
    }

    // /**
    //  * @Route("/login", name="login")
    //  */
    // public function login()
    // {
    //     return $this->render('auth/login.html.twig', [
    //         'controller_name' => 'AuthController',
    //     ]);
    // }
    //
    // /**
    //  * @Route("/logout", name="logout")
    //  */
    // public function logout()
    // {
    //     return $this->redirectToRoute('home', [
    //         'controller_name' => 'salputtachattachatt',
    //     ]);
    // }
}
