<?php

namespace App\Controller;

use App\Entity\Peacher\Avatar;
use App\Entity\Peacher\Peacher;
use App\Exception\FileExtensionException;
use App\Service\Tools\RandomProfile\AvatarFinder;
use App\Service\Tools\RandomProfile\RandomizerInterface;
use App\Service\Tools\RandomProfile\UsernameRandomizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     *
     * @param Request $request
     * @param AvatarFinder $avatarFinder
     * @return RedirectResponse|Response
     */
    public function profile(Request $request, AvatarFinder $avatarFinder)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Peacher $peacher */
        $peacher = $this->getUser();
        if (is_null($peacher->getDisplayUsername())) {
            return $this->redirectToRoute('user_select_username');
        }

        // Find user's avatar
        $avatar = $peacher->getAvatar();

        if (!$avatar instanceof Avatar) {
            $content = $avatarFinder->find(['username' => $peacher->getDisplayUsername()]);
            $filename = $this->getParameter('avatar.asset_dir_path') . $peacher->getUsername() . '.png';
            file_put_contents($filename, $content);

            $avatar = new Avatar();
            $avatar->setFilename($filename)->setPeacher($peacher);
            $em->persist($avatar);
            $em->flush();
        }

        // Create update form
        $userForm = $this->createFormBuilder($peacher)
            ->add('displayUsername', TextType::class, [
                'label' => 'The username that will be used for battles'
            ])
            ->add('style', TextType::class, [
                'label' => 'Choose a style that defines you (optional)',
                'required' => false,
            ])
            ->add('save', SubmitType::class, array('label' => 'Update !'))
            ->getForm();

        // handle request

        $avatarForm = $this->createFormBuilder()
            ->add('attachment', FileType::class, [
                'label' => 'Import your own avatar'
            ])
            ->getForm();

        // handle request
        $avatarForm->handleRequest($request);
        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            try {
                $filename = $avatar->getFilename();
                /** @var UploadedFile $file */
                $file = $avatarForm['attachment']->getData();
                $extension = $file->guessExtension();

                if (!$extension || !in_array($extension, ['png', 'jpg', 'jpeg'])) {
                    $this->addFlash('danger', 'Invalid file extension, please upload PNG or JPEG files');
                    throw new FileExtensionException(FileExtensionException::EXTENSION_NOT_MATCHING);
                }

                if (!preg_match('/(' . $extension . ')$/', $filename)) {
                    $filename = preg_replace('/(jpe*g)|(png)$/', $extension, $filename);
                }
                $file->move(dirname($filename), basename($filename));

                $avatar->setFilename($filename);
                $em->persist($avatar);
                $em->flush();
            } catch (\Exception $exception) {
                if (!$this->get('session')->getFlashBag()->has('danger')) {
                    $this->addFlash('danger', 'An error occured while uploading file');
                }
            }
        }


        return $this->render('user/profile.html.twig', [
            'userForm' => $userForm->createView(),
            'avatarForm' => $avatarForm->createView(),
            'avatar' => $avatar,
        ]);
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
        $form = $this->createFormBuilder($peacher)
            ->add('displayUsername', TextType::class, [
                'label' => 'If too lazy, here\'s a nickname that you can keep. Or choose yours.'
            ])
            ->add('style', TextType::class, [
                'label' => 'Choose a style that defines you (optional)',
                'required' => false,
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
