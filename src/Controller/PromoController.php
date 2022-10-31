<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Basket\Entity\Item;
use App\Model\Basket\Repository\BasketRepository;
use App\Model\Basket\Service\ReadBasketToken;
use App\Model\Order\Service\CurrentOrder;
use App\Model\Order\UseCase\Promocode\Update;
use App\Model\Order\UseCase\Promocode\Remove;
use App\Model\Promocode\Entity\Promocode;
use App\Model\Promocode\Repository\PromocodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PromoController extends AbstractController
{
    /**
     * @Route("/promo/check",name="promo.check", methods={"POST"})
     */
    public function check(
        Request $request,
        PromocodeRepository $promocodes,
        ReadBasketToken $token,
        BasketRepository $basket,
        TranslatorInterface $translator,
        CurrentOrder $currentOrder,
        Update\Handler $promoUpdate,
        Remove\Handler $promoRemove
    )
    {
        $code = $request->request->get('promocode') ?? $request->getSession()->get(Promocode::NAME_SESSION);
        $order = $currentOrder->getOrder();

        if ($code) {
            $request->getSession()->set(Promocode::NAME_SESSION, $code);
        } elseif ($order->hasPromocode()) {
            $code = $order->getPromocode()->getCode();
        } else {
            return $this->json([], 204);
        }

        try {
            $promo = $promocodes->getByCode($code);
            $products = $basket->findAllByToken($token->getToken());

            $total = array_reduce($products, static function (float $carry, Item $item) {
                return $carry + $item->getCount() * $item->getProduct()->getFinishPrice();
            }, 0.00);

            if ($order && $order->getPromocode()->getCode() !== $code) {
                $promoUpdate->handle(Update\Command::fromPromo($order, $code));
            }

            return $this->json([
                'promocode' => $code,
                'discount' => $promo->getDiscount($total)
            ], 200);
        } catch (\DomainException $exception) {
            if ($order) {
                $promoRemove->handle(Remove\Command::fromPromo($order));
            }

            $request->getSession()->set(Promocode::NAME_SESSION, null);

            return $this->json([
                'promocode' => $code,
                'message' => $translator->trans($exception->getMessage(), [], 'cpanel')
            ], 406);
        }
    }
}
