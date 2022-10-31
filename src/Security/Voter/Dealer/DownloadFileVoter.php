<?php

declare(strict_types=1);

namespace App\Security\Voter\Dealer;

use App\Model\File\Entity\File;
use App\Model\File\Repository\FileCategoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DownloadFileVoter extends Voter
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var FileCategoryRepository
     */
    private $fileCategoryRepository;

    public function __construct(Security $security, FileCategoryRepository $fileCategoryRepository)
    {

        $this->security = $security;
        $this->fileCategoryRepository = $fileCategoryRepository;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['DOWNLOAD'])
            && $subject instanceof File;
    }

    /**
     * @param string $attribute
     * @param File $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'DOWNLOAD':

                if ($this->fileCategoryRepository->findOneBy(['file' => $subject, 'category' => $user->getCategory()])) {
                    return true;
                }
                break;

        }

        return false;
    }
}