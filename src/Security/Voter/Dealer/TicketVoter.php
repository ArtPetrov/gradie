<?php

declare(strict_types=1);

namespace App\Security\Voter\Dealer;

use App\Model\Ticket\Entity\Message\Files;
use App\Model\Ticket\Entity\Ticket\Ticket;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TicketVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return (in_array($attribute, ['TICKET_INTERACT'])
                && $subject instanceof Ticket)
            || (in_array($attribute, ['TICKET_DOWNLOAD'])
                && $subject instanceof Files);
    }

    /**
     * @param string $attribute
     * @param Ticket|Files $subject
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
            case 'TICKET_INTERACT':
                /** @var Ticket $subject */
                return $subject->getAuthor() === $user;
                break;
            case 'TICKET_DOWNLOAD':
                /** @var Files $subject */
                return $subject->getMessage()->getTicket()->getAuthor() === $user;
                break;
            default:
                return false;
        }

    }
}