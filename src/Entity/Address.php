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
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="facturation", orphanRemoval=true, cascade={"persist"})
     */
    private $order_facturation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="livraison", orphanRemoval=true, cascade={"persist"})

     */
    private $order_livraison;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="addresses")
     */
    private $user;

    public function __construct()
    {
        $this->order_facturation = new ArrayCollection();
        $this->order_livraison = new ArrayCollection();
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

    /**
     * @return Collection|Order[]
     */
    public function getOrderFacturation(): Collection
    {
        return $this->order_facturation;
    }

    public function addOrderFacturation(Order $orderFacturation): self
    {
        if (!$this->order_facturation->contains($orderFacturation)) {
            $this->order_facturation[] = $orderFacturation;
            $orderFacturation->setFacturation($this);
        }

        return $this;
    }

    public function removeOrderFacturation(Order $orderFacturation): self
    {
        if ($this->order_facturation->contains($orderFacturation)) {
            $this->order_facturation->removeElement($orderFacturation);
            // set the owning side to null (unless already changed)
            if ($orderFacturation->getFacturation() === $this) {
                $orderFacturation->setFacturation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrderLivraison(): Collection
    {
        return $this->order_livraison;
    }

    public function addOrderLivraison(Order $orderLivraison): self
    {
        if (!$this->order_livraison->contains($orderLivraison)) {
            $this->order_livraison[] = $orderLivraison;
            $orderLivraison->setLivraison($this);
        }

        return $this;
    }

    public function removeOrderLivraison(Order $orderLivraison): self
    {
        if ($this->order_livraison->contains($orderLivraison)) {
            $this->order_livraison->removeElement($orderLivraison);
            // set the owning side to null (unless already changed)
            if ($orderLivraison->getLivraison() === $this) {
                $orderLivraison->setLivraison(null);
            }
        }

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
}
