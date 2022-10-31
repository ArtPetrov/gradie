<?php

declare(strict_types=1);

namespace App\Security\Voter\Dealer;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Salon\Entity\Salon;
use App\Model\Salon\Repository\ModerationSalonRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SalonVoter extends Voter
{
    private $moderations;

    public function __construct(ModerationSalonRepository $moderations)
    {
        $this->moderations = $moderations;
    }

    protected function supports($attribute, $subject)
    {
        return ('SALON_OWNER' === $attribute || 'SALON_NONE_PROCESS_DELETE' === $attribute) && $subject instanceof Salon;
    }

    /**
     * @param string $attribute
     * @param Salon $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $dealer = $token->getUser();

        if (!$dealer instanceof Dealer) {
            return false;
        }

        if ('SALON_OWNER' === $attribute) {
            return $subject->existOwner($dealer);
        }

        if ('SALON_NONE_PROCESS_DELETE' === $attribute) {
            return !$this->moderations->isActualRequestForRemove($subject);
        }

        return false;
    }
}
