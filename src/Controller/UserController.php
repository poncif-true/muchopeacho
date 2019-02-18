<?php

namespace App\Controller;

use App\Entity\Peacher\Peacher;
use App\Service\Tools\Randomizer\RandomizerInterface;
use App\Service\Tools\Randomizer\UsernameRandomizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * User's profile
     *
     * @Route("/profile", name="profile")
     */
    public function profile()
    {
        $peacher = $this->getUser();
        if (is_null($peacher->getDisplayUsername())) {
            return $this->redirectToRoute('user_select_username');
        }

        return $this->render('user/profile.html.twig', []);
    }

    /**
     * For its first connection user see this page, which provides a random username and user can choose a style
     *
     * @Route("/select-username", name="select_username")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @param RandomizerInterface $randomizer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectUsername(Request $request, RandomizerInterface $randomizer)
    {
        // Retrieve current user
        $em = $this->getDoctrine()->getManager();
        $currentUserEmail = $this->getUser()->getEmail();
        $peacher = $em->getRepository(Peacher::class)->findOneBy(['email' => $currentUserEmail]);

        // If not a new user go to profile
        if (!is_null($peacher->getDisplayUsername())) {
            return $this->redirectToRoute('user_profile');
        }

        // Get a random username
        /** @var UsernameRandomizer $randomizer */
        $randomUsername = $randomizer->generate();
        $peacher->setDisplayUsername($randomUsername);

        // Create update form
        $form = $this->createFormBuilder($peacher)
            ->add('displayUsername', TextType::class, [
                'label' => 'If too lazy, here\'s a nickname that you can keep. Or choose yours.'
            ])
            ->add('style', TextType::class, [
                'label' => 'Choose a style that defines you (optional)'
            ])
            ->add('save', SubmitType::class, array('label' => 'Go !'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Thank you, and welcome ' . $peacher->getDisplayUsername());
            $this->addFlash('info', 'You may now want to start a new battle');
            return $this->redirectToRoute('battle_start');
        }

        return $this->render('user/select_username.html.twig', ['form' => $form->createView()]);
    }
}
