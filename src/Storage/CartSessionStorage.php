<?php

namespace App\Storage;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class CartSessionStorage
{

    private SessionInterface $session;
    private OrderRepository $cartRepository;
    const CART_KEY_NAME = 'cart_id';


    public function __construct(
        SessionInterface $session,
        OrderRepository $cartRepository
    ) {
        $this->session = $session;
        $this->cartRepository = $cartRepository;
    }


    public function getCart(): ?Order
    {
        return $this->cartRepository->findOneBy([
            'id' => $this->getCartId(),
            'status' => Order::STATUS_CART
        ]);
    }


    public function setCart(Order $cart): void
    {
        $this->session->set(self::CART_KEY_NAME, $cart->getId());
    }


    private function getCartId(): ?int
    {
        return $this->session->get(self::CART_KEY_NAME);
    }
}

