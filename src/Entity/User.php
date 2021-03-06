<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements \Symfony\Component\Security\Core\User\UserInterface
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Ticket", mappedBy="users", cascade={"remove"})
     */
    private $tickets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author", cascade={"remove"})
     */
    private $authoredComments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ticket", mappedBy="author", cascade={"remove"})
     */
    private $authoredTickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->authoredComments = new ArrayCollection();
        $this->authoredTickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_CUSTOMER';

        return array_unique($roles);
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->addUser($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            $ticket->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getAuthoredComments(): Collection
    {
        return $this->authoredComments;
    }

    public function addAuthoredComment(Comment $comment): self
    {
        if (!$this->authoredComments->contains($comment)) {
            $this->authoredComments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthoredComment(Comment $comment): self
    {
        if ($this->authoredComments->contains($comment)) {
            $this->authoredComments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getAuthoredTickets(): Collection
    {
        return $this->authoredTickets;
    }

    public function addAuthoredTickets(Ticket $ticket): self
    {
        if (!$this->authoredTickets->contains($ticket)) {
            $this->authoredTickets[] = $ticket;
            $ticket->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthoredTickets(Ticket $ticket): self
    {
        if ($this->authoredTickets->contains($ticket)) {
            $this->authoredTickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getAuthor() === $this) {
                $ticket->setAuthor(null);
            }
        }

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->getEmail();
        // TODO: Implement getUsername() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __toString(): string
    {
        return $this->getEmail();
        // TODO: Implement eraseCredentials() method.
    }
}
