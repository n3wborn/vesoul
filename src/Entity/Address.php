<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $additional;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastname;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="addresses")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="deliveryAddress")
     */
    private $order_deliveryAddress;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="billAddress")
     */
    private $order_billAddress;

    public function __construct()
    {
        $this->order_deliveryAddress = new ArrayCollection();
        $this->order_billAddress = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAdditional(): ?string
    {
        return $this->additional;
    }

    public function setAdditional(?string $additional): self
    {
        $this->additional = $additional;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrderDeliveryAddress(): Collection
    {
        return $this->order_deliveryAddress;
    }

    public function addOrderDeliveryAddress(Order $orderDeliveryAddress): self
    {
        if (!$this->order_deliveryAddress->contains($orderDeliveryAddress)) {
            $this->order_deliveryAddress[] = $orderDeliveryAddress;
            $orderDeliveryAddress->setDeliveryAddress($this);
        }

        return $this;
    }

    public function removeOrderDeliveryAddress(Order $orderDeliveryAddress): self
    {
        if ($this->order_deliveryAddress->removeElement($orderDeliveryAddress)) {
            // set the owning side to null (unless already changed)
            if ($orderDeliveryAddress->getDeliveryAddress() === $this) {
                $orderDeliveryAddress->setDeliveryAddress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrderBillAddress(): Collection
    {
        return $this->order_billAddress;
    }

    public function addOrderBillAddress(Order $orderBillAddress): self
    {
        if (!$this->order_billAddress->contains($orderBillAddress)) {
            $this->order_billAddress[] = $orderBillAddress;
            $orderBillAddress->setBillAddress($this);
        }

        return $this;
    }

    public function removeOrderBillAddress(Order $orderBillAddress): self
    {
        if ($this->order_billAddress->removeElement($orderBillAddress)) {
            // set the owning side to null (unless already changed)
            if ($orderBillAddress->getBillAddress() === $this) {
                $orderBillAddress->setBillAddress(null);
            }
        }

        return $this;
    }
}
