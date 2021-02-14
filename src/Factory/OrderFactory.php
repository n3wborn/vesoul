<?php


namespace App\Factory;


use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Book;

/**
 * Class OrderFactory
 * @package App\Factory
 */
class OrderFactory
{
    /**
     * Creates an order.
     *
     * @return Order
     */
    public function create(): Order
    {
        $order = new Order();
        $order
            ->setStatus(Order::STATUS_CART)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $order;
    }

    /**
     * Creates an item for a book.
     *
     * @param Book $book
     * @return OrderItem
     */
    public function createItem(Book $book): OrderItem
    {
        $item = new OrderItem();
        $item->setBook($book);
        $item->setQuantity(1);

        return $item;
    }
}

