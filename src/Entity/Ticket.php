<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TicketRepository")
 */
class Ticket
{
    const EXTERNAL_STATUS_MESSAGE_OPEN = 'open';
    const EXTERNAL_STATUS_MESSAGE_PROGRESS_ = 'in progress';
    const EXTERNAL_STATUS_MESSAGE_WAITING = 'waiting for customer feedback';
    const EXTERNAL_STATUS_MESSAGE_CLOSED = 'closed';
    const EXTERNAL_STATUS_MESSAGE_WONT = 'won\'t fix';

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
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ticket", cascade={"remove", "persist"})
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="tickets")
     * @ORM\JoinTable(name="ticket_user",
     *      joinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="authoredTickets")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $author;

    private $canReopenUntil;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function setCanReopenUntil(): self
    {
        //$this->canReopenUntil = new \DateTime('-1 hour');
        $this->canReopenUntil = new \DateTime('+1 hour');
        return $this;
    }

    public function getCanReopenUntil(): bool

    {
        return $this->canReopenUntil <= new \DateTime();
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
        dump($comment);
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

    public function __toString(): string
    {
        return $this->getTicketText();
        // TODO: Implement eraseCredentials() method.
    }

    public function setAuthor($author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

//    public function addAuthor(User $user): self
//    {
//        if (!$this->author->contains($user)) {
//            $this->author[] = $user;
//            $user->addAuthoredTickets($this);
//        }
//
//        return $this;
//    }
//
//    public function removeAuthor(User $user): self
//    {
//        if ($this->users->contains($user)) {
//            $this->users->removeElement($user);
//            $user->removeAuthoredTickets($this);
//        }
//
//        return $this;
//    }
}
