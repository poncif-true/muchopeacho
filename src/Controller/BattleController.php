<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BattleController
 * @package App\Controller
 *
 * @Route("/battle", name="battle_")
 * @IsGranted("ROLE_USER")
 */
class BattleController extends AbstractController
{
    /**
     * @Route("/start", name="start")
     */
    public function start()
    {
        

        return $this->render('battle/start.html.twig', [
        ]);
    }
}
