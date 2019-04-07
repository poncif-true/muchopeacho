<?php


namespace App\Service;


use App\Entity\Peacher\Avatar;
use App\Entity\Peacher\Peacher;
use App\Exception\FileExtensionException;
use App\Service\Tools\RandomProfile\AvatarFinder;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AvatarService
 * @package App\Service
 */
class AvatarService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var AvatarFinder */
    private $avatarFinder;

    /** @var string */
    private $dirPath;

    /** @var LoggerInterface */
    private $logger;

    /**
     * AvatarService constructor.
     * @param EntityManagerInterface $em
     * @param AvatarFinder $avatarFinder
     * @param string $dirPath
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManagerInterface $em,
        AvatarFinder $avatarFinder,
        string $dirPath,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->avatarFinder = $avatarFinder;
        $this->dirPath = $dirPath;
        $this->logger = $logger;
    }

    /**
     * @param Peacher $peacher
     */
    public function generateAvatar(Peacher $peacher)
    {
        if ($peacher->getAvatar() instanceof Avatar) {
            return;
        }
        if (empty($peacher->getDisplayUsername())) {
            $this->logger->warning(
                'Trying to generate avatar for ' . $peacher->getUsername() . ' while display username is empty'
            );
            return;
        }

        try {
            $content = $this->avatarFinder->find(['username' => $peacher->getDisplayUsername()]);
            $filename = $this->dirPath . $peacher->getUsername() . '.png';
            file_put_contents($filename, $content);

            $avatar = new Avatar();
            $avatar->setFilename($filename)->setPeacher($peacher);
            $this->em->persist($avatar);
            $this->em->flush();
        } catch (\Exception $exception) {
            $this->logger->error('Unable to generate avatar: ' . $exception->getMessage());
        }
    }

    /**
     * @param Avatar $avatar
     * @param UploadedFile $file
     */
    public function saveUploadedFile(Avatar $avatar, UploadedFile $file)
    {
        $filename = $avatar->getFilename();
        $extension = $file->guessExtension();

        // Check extension
        if (!$extension || !in_array($extension, ['png', 'jpg', 'jpeg'])) {
            throw new FileExtensionException(FileExtensionException::EXTENSION_NOT_MATCHING);
        }
        // Replace extension by file's one if it is different from actual file
        if (!preg_match('/(' . $extension . ')$/', $filename)) {
            $filename = preg_replace('/(jpe*g)|(png)$/', $extension, $filename);
        }

        $file->move(dirname($filename), basename($filename));

        $avatar->setFilename($filename);
        $this->em->persist($avatar);
        $this->em->flush();
    }
}
