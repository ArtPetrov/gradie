<?php

declare(strict_types=1);

namespace App\Security\OAuth;

use App\Model\Buyer\UseCase\Network\Auth\Command;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class VkAuthenticator extends BaseAuthenticator
{
    protected $providerName = 'vk';
    protected $networkName = 'vk';

    public function getCommand(ResourceOwnerInterface $user): Command
    {
        $command = new Command($this->networkName, (string)$user->getId());
        $command->name = implode(' ', [$user->getFirstName(), $user->getLastName()]);
        return $command;
    }
}
