<?php

declare(strict_types=1);

namespace App\Security\OAuth;

use App\Model\Buyer\UseCase\Network\Auth\Command;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class YandexAuthenticator extends BaseAuthenticator
{
    protected $providerName = 'yandex';
    protected $networkName = 'yandex';

    public function getCommand(ResourceOwnerInterface $user): Command
    {
        $command = new Command($this->networkName, (string)$user->getId());
        $command->name = implode(' ', [$user->getFirstName(), $user->getLastName()]);
        return $command;
    }
}
