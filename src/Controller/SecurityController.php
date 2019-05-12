<?php

namespace App\Controller;

use App\Entity\PasswordToken;
use App\Entity\Peacher\Peacher;
use App\Exception\SecurityException;
use App\Form\ConfirmEmailForm;
use App\Form\ResetPasswordForm;
use App\Service\Security\SecurityService;
use App\Service\TokenService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
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
     *
     * @return RedirectResponse|Response
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
                $peacher = $securityService->addUser($form->getData());
                $securityService->sendSignUpConfirmation($peacher->getEmail());
                $this->addFlash('success', 'You are successfully registered');
                $this->addFlash('info', 'You will soon receive an email to confirm your account creation');

                return $this->redirectToRoute('login');
            } catch (SecurityException $securityException) {
                $logger->error($securityException->getMessage());
                $this->addFlash('danger', 'Error: ' . $securityException->getMessage());
            } catch (\Exception $e) {
                $logger->error($e->getMessage());
                $this->addFlash('danger', 'An unexpected error occurred');
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

    /**
     * @Route("/confirm/sign-up/{tokenValue?}", name="confirm_sign_up")
     *
     * @param TokenService $tokenService
     * @param SecurityService $securityService
     * @param Request $request
     * @param string|null $tokenValue
     *
     * @return RedirectResponse|Response
     */
    public function confirmSignUp(
        TokenService $tokenService,
        SecurityService $securityService,
        Request $request,
        string $tokenValue = null
    ): Response {
        try {
            $token = $tokenService->getToken($tokenValue);

            $peacher = ($token) ? $token->getUser() : new Peacher();

            $form = $this->createForm(ConfirmEmailForm::class, $peacher);
            $form->handleRequest($request);

            if ($token && $token->getUser()->isActive()) {
                $this->addFlash('info', 'Your account has already been activated, you can login');
            } elseif ($form->isSubmitted() && $form->isValid()) {
                // Send another confirmation email
                $securityService->sendSignUpConfirmation($form->getData()->getEmail());
                $this->addFlash('info', 'You will soon receive an email to confirm your account creation');
            } elseif ($token) {
                $securityService->confirmSignUp($token);
                $this->addFlash('success', 'Your account has been activated, you can now login');
            } else {
                throw new \OutOfBoundsException('Something went wrong...');
            }

            return $this->redirectToRoute('login');
        } catch (\Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->render('security/confirm-sign-up.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/reset-password", name="reset_password")
     *
     * @param Request $request
     * @param SecurityService $securityService
     *
     * @return Response
     */
    public function resetPassword(Request $request, SecurityService $securityService)
    {
        $form = $this->createForm(ConfirmEmailForm::class, null, [
            'label' => 'Please your email to reset your password',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $securityService->sendResetPasswordEmail($data['email']);
                $this->addFlash('info', 'You will soon receive an email with a link to reset your password');

                return $this->redirectToRoute('login');
            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }
        }

        return $this->render('security/reset-password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/confirm/new-password/{tokenValue?}", name="confirm_new_password")
     *
     * @param TokenService $tokenService
     * @param SecurityService $securityService
     * @param Request $request
     * @param string|null $tokenValue
     *
     * @return RedirectResponse|Response
     */
    public function confirmNewPassword(
        TokenService $tokenService,
        SecurityService $securityService,
        Request $request,
        string $tokenValue = null
    ): Response {
        // Retrieve token
        $token = $tokenService->getToken($tokenValue);

        // Cannot access to reset page if token is not valid
        if (!$token instanceof PasswordToken || $token->isExpired() || $token->isAcquitted()) {
            $this->addFlash('danger', 'Invalid Token');

            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(ResetPasswordForm::class, $token->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Save user
                // no need to fetch user from form, as it is passed by reference (default Doctrine objects behavior)
                $securityService->resetPassword($token);
                $this->addFlash('success', 'Your password has been changed, you can now login');

                return $this->redirectToRoute('login');
            } catch (SecurityException $exception) {
                $this->addFlash('warning', $exception->getMessage());
            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }
        }

        return $this->render(
            'security/confirm-new-password.html.twig',
            ['form' => $form->createView()]
        );
    }
}
