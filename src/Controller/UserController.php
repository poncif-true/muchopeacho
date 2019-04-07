<?php

namespace App\Controller;

use App\Entity\Peacher\Peacher;
use App\Exception\FileExtensionException;
use App\Form\UserForm;
use App\Service\AvatarService;
use App\Service\Tools\RandomProfile\RandomizerInterface;
use App\Service\Tools\RandomProfile\UsernameRandomizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * User's profile
     *
     * @Route("/profile", name="profile")
     *
     * @param Request $request
     * @param AvatarService $avatarService
     * @return RedirectResponse|Response
     */
    public function profile(Request $request, AvatarService $avatarService)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Peacher $peacher */
        $peacher = $this->getUser();
        if (is_null($peacher->getDisplayUsername())) {
            return $this->redirectToRoute('user_select_username');
        }

        // Get user's avatar
        $avatar = $peacher->getAvatar();

        // Create update form
        $userForm = $this->createForm(UserForm::class, $peacher);

        // avatar form
        $avatarForm = $this->createFormBuilder()
            ->add('attachment', FileType::class, [
                'label' => 'Import your own avatar',
                'constraints' => [new File(['maxSize' => '1024k'])]
            ])
            ->getForm();

        // handle request
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $em->flush();
        }

        // handle request
        $avatarForm->handleRequest($request);
        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            try {
                $avatarService->saveUploadedFile($avatar, $avatarForm['attachment']->getData());
            } catch (FileExtensionException $exception) {
                $this->addFlash('danger', 'Invalid file extension, please upload PNG or JPEG files');
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'An error occured while uploading file');
            }
        }

        $params = [
            'userForm'   => $userForm->createView(),
            'avatarForm' => $avatarForm->createView(),
            'avatar'     => $avatar,
        ];

        return $this->render('user/profile.html.twig', $params);
    }

    /**
     * For its first connection user see this page, which provides a random username and user can choose a style
     *
     * @Route("/select-username", name="select_username")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @param RandomizerInterface $randomizer
     * @return Response
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
        $form = $this->createForm(UserForm::class, $peacher);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            // This will ask AvatarEventSubscriber to generate avatar
            $request->attributes->set('must_generate_avatar', true);
            $this->addFlash('success', 'Thank you, and welcome ' . $peacher->getDisplayUsername());
            return $this->redirectToRoute('battle_start');
        }

        return $this->render('user/select_username.html.twig', ['form' => $form->createView()]);
    }
}
