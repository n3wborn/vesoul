<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity(repositoryClass="App\Repository\CommandRepository")
*/
class Command
{
/**
* @ORM\Id()
* @ORM\GeneratedValue()
* @ORM\Column(type="integer")
*/
private $id;

/**
* @ORM\Column(type="datetime_immutable")
*/
private $date;

/**
* @ORM\Column(type="string", length=150)
*/
private $number;

/**
* @ORM\Column(type="integer")
*/
private $quantity;

/**
* @ORM\Column(type="float")
*/
private $totalcost;

/**
* @ORM\Column(type="string", length=150)
*/
private $state;

/**
* @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="commands")
*/
private $books;

/**
* @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="commands")
*/
private $user;

/**
 * @ORM\ManyToOne(targetEntity="App\Entity\Address", inversedBy="command_facturation")
 * @ORM\JoinColumn(nullable=false)
 */
private $facturation;

/**
 * @ORM\ManyToOne(targetEntity="App\Entity\Address", inversedBy="command_livraison")
 * @ORM\JoinColumn(nullable=false)
 */
private $livraison;

public function __construct()
{
$this->books = new ArrayCollection();
}

public function getId(): ?int
{
return $this->id;
}

public function getDate(): ?\DateTimeImmutable
{
return $this->date;
}

public function setDate(\DateTimeImmutable $date): self
{
$this->date = $date;

return $this;
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

public function getQuantity(): ?int
{
return $this->quantity;
}

public function setQuantity(int $quantity): self
{
$this->quantity = $quantity;

return $this;
}

public function getTotalcost(): ?float
{
return $this->totalcost;
}

public function setTotalcost(float $totalcost): self
{
$this->totalcost = $totalcost;

return $this;
}

public function getState(): ?string
{
return $this->state;
}

public function setState(string $state): self
{
$this->state = $state;

return $this;
}

/**
* @return Collection|Book[]
*/
public function getBooks(): Collection
{
return $this->books;
}

public function addBook(Book $book): self
{
    $this->books[] = $book;

    return $this;
}

public function removeBook(Book $book): self
{
    if ($this->books->contains($book)) {
        $this->books->removeElement($book);
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

public function __toString(){
    return $this->number;
}

public function getFacturation(): ?Address
{
    return $this->facturation;
}

public function setFacturation(?Address $facturation): self
{
    $this->facturation = $facturation;

    return $this;
}

public function getLivraison(): ?Address
{
    return $this->livraison;
}

public function setLivraison(?Address $livraison): self
{
    $this->livraison = $livraison;

    return $this;
}

}