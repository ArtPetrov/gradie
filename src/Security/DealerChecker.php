<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\Dealer\Entity\Dealer;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DealerChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $identity): void
    {
        /** @var Dealer $identity */
        if (!$identity instanceof UserInterface) {
            return;
        }

        if ($identity->moderation()->isBlocked()) {
            $exception = new DisabledException('dealer.moderation.blocked');
            $exception->setUser($identity);
            throw $exception;
        }

    }

    public function checkPostAuth(UserInterface $identity): void
    {
        if (!$identity instanceof UserInterface) {
            return;
        }
    }
}
