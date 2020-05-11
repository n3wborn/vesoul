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
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="addresses")
     */
    private $users;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Command", mappedBy="facturation")
     */
    private $command_facturation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Command", mappedBy="livraison")
     */
    private $command_livraison;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->command_facturation = new ArrayCollection();
        $this->command_livraison = new ArrayCollection();
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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addAddress($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeAddress($this);
        }

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
     * @return Collection|Command[]
     */
    public function getCommandFacturation(): Collection
    {
        return $this->command_facturation;
    }

    public function addCommandFacturation(Command $commandFacturation): self
    {
        if (!$this->command_facturation->contains($commandFacturation)) {
            $this->command_facturation[] = $commandFacturation;
            $commandFacturation->setFacturation($this);
        }

        return $this;
    }

    public function removeCommandFacturation(Command $commandFacturation): self
    {
        if ($this->command_facturation->contains($commandFacturation)) {
            $this->command_facturation->removeElement($commandFacturation);
            // set the owning side to null (unless already changed)
            if ($commandFacturation->getFacturation() === $this) {
                $commandFacturation->setFacturation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Command[]
     */
    public function getCommandLivraison(): Collection
    {
        return $this->command_livraison;
    }

    public function addCommandLivraison(Command $commandLivraison): self
    {
        if (!$this->command_livraison->contains($commandLivraison)) {
            $this->command_livraison[] = $commandLivraison;
            $commandLivraison->setLivraison($this);
        }

        return $this;
    }

    public function removeCommandLivraison(Command $commandLivraison): self
    {
        if ($this->command_livraison->contains($commandLivraison)) {
            $this->command_livraison->removeElement($commandLivraison);
            // set the owning side to null (unless already changed)
            if ($commandLivraison->getLivraison() === $this) {
                $commandLivraison->setLivraison(null);
            }
        }

        return $this;
    }
}