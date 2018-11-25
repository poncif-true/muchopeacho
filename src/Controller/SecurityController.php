<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Translation\TranslatorInterface;
use App\Entity\Peacher;
use App\Service\Security\SecurityService;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
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
     */
    public function signUp(
        Request $request,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        SecurityService $securityService
    ) {
        /** AbstractType $form */
        $form = $this->createFormBuilder(new Peacher())
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class, array('label' => 'Sign up !'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                // throw new \Exception("Error Processing Tarass", 1);
                $peacher = $form->getData();
                $peacher->setActive(true);
                $password = $securityService->encodePassword($peacher, $peacher->getPassword());
                $peacher->setPassword($password);
                $em->persist($peacher);
                $em->flush();

                $logger->info('new Peacher created: ' . $peacher->getUsername());
                $this->addFlash('success', $translator->trans('app.sign_up.success'));

                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                $logger->error($e->getMessage());
                $this->addFlash('danger', $translator->trans('app.sign_up.error'));
            }
        }

        return $this->render('security/sign-up.html.twig', [
            'form' => $form->createView(),
            'peacher' => $peacher ?? new Peacher,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): Response
    {
        // Nothing to do here ;)
        throw new \LogicException('Did you forgot to configure security.yaml ?');
    }
}
