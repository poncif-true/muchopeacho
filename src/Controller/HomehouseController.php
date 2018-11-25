<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomehouseController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     * @Route("/", name="index")
     */
    public function index()
    {
        

        return $this->render('homehouse/index.html.twig', [
            'controller_name' => 'HomehouseController',
        ]);
    }
}
