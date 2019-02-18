<?php

namespace App\Controller;

use App\Entity\Peacher\Peacher;
use App\Exception\SecurityException;
use App\Service\Security\SecurityService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/sign-up", name="sign_up")
     *
     * @param Request $request
     * @param LoggerInterface $logger
     * @param SecurityService $securityService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function signUp(
        Request $request,
        LoggerInterface $logger,
        SecurityService $securityService
    ) {
        /** AbstractType $form */
        $form = $this->createFormBuilder(new Peacher())
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class)
            ->add('save', SubmitType::class, array('label' => 'Sign up !'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $securityService->addUser($form->getData());
                $this->addFlash('success', 'You are succesfully registered');

                return $this->redirectToRoute('login');
            } catch (SecurityException $securityException) {
                $logger->error($securityException->getMessage());
                $this->addFlash('danger', 'Error: ' . $securityException->getMessage());
            } catch (\Exception $e) {
                $logger->error($e->getMessage());
                $this->addFlash('danger', 'An unexpected error occured');
            }
        }

        return $this->render('security/sign-up.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {
        // Nothing to do here ;)
        throw new \LogicException('Did you forgot to configure security.yaml ?');
    }
}
