<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TicketRepository")
 */
class Ticket
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priorityLevel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $externalStatusMessage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ticketText;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ticket")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="tickets")
     */
    private $users;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriorityLevel(): ?int
    {
        return $this->priorityLevel;
    }

    public function setPriorityLevel(?int $priorityLevel): self
    {
        $this->priorityLevel = $priorityLevel;

        return $this;
    }

    public function getExternalStatusMessage(): ?string
    {
        return $this->externalStatusMessage;
    }

    public function setExternalStatusMessage(string $externalStatusMessage): self
    {
        $this->externalStatusMessage = $externalStatusMessage;

        return $this;
    }

    public function getTicketText(): ?string
    {
        return $this->ticketText;
    }

    public function setTicketText(string $ticketText): self
    {
        $this->ticketText = $ticketText;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTicket($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTicket() === $this) {
                $comment->setTicket(null);
            }
        }

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
            $user->addTicket($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeTicket($this);
        }

        return $this;
    }
}