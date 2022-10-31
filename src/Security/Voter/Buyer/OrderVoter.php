<?php

declare(strict_types=1);

namespace App\Security\Voter\Buyer;

use App\Model\Buyer\Entity\Buyer;
use App\Model\Order\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrderVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return 'ORDER_OWNER' === $attribute && $subject instanceof Order;
    }

    /**
     * @param string $attribute
     * @param Order $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $buyer = $token->getUser();

        if (!$buyer instanceof Buyer) {
            return false;
        }

        if ('ORDER_OWNER' === $attribute) {
            return $subject->getBasket()->getToken() === $buyer->getBasketToken()->getToken();
        }

        return false;
    }
}
