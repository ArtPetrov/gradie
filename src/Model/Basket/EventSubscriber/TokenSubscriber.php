<?php

declare(strict_types=1);

namespace App\Model\Basket\EventSubscriber;

use App\Model\Basket\Entity\BasketToken;
use App\Model\Basket\Service\BasketTokenizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TokenSubscriber implements EventSubscriberInterface
{
    private $tokenizer;
    private $token = null;

    public function __construct(BasketTokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    public function checkBasketToken(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$firewall = $request->attributes->get('_firewall_context')) {
            return null;
        };

        $params = explode(".", $firewall);
        if ("buyer" !== end($params)) {
            return null;
        };

        $token = $request->cookies->get(BasketToken::COOKIE_NAME);

        if (!$this->tokenizer->isValid($token)) {
            $this->token = $this->tokenizer->generate()->getToken();
            $request->cookies->add([BasketToken::COOKIE_NAME => $this->token]);
        }
    }

    public function injectBasketToken(ResponseEvent $event)
    {
        if ($this->token) {
            $response = $event->getResponse();
            $response->headers->setCookie(new Cookie(BasketToken::COOKIE_NAME, $this->token, time() + 60 * 60 * 24 * 3000));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['checkBasketToken', 0],
            KernelEvents::RESPONSE => 'injectBasketToken'
        ];
    }
}
