<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *    fields={"username"},
 *    message="Utilisateur déjà utilisé"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", options={"default":null})
     */
    private ?int $gender;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $lastname;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\Length(min="8",    minMessage="Votre mot de passe doit faire minimum 8 caractères")
     */
    private string $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Votre mot de passe ne correspond pas au champ de vérification")
     */
    public string $confirm_password;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     */
    private string $username;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="user")
     */
    private $orders;

    /**
     * @ORM\Column(type="simple_array")
     */
    private array $roles;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private ?string $tel;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $birth;

    /**
     * @ORM\OneToMany(targetEntity=Address::class, mappedBy="user")
     */
    private $addresses;


    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->addresses = new ArrayCollection();
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }


    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {

    }

    public function getBirth(): ?DateTime
    {
        return $this->birth;
    }

    public function setBirth(?DateTime $birth): self
    {
        $this->birth = $birth;

        return $this;
    }

    public function __toString()
    {
        return $this->firstname;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }

        return $this;
    }
}
