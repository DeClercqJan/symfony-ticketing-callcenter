<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
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
    private $commentText;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCommentPublic;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ticket", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $ticket;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="authoredComments")
     * @ORM\JoinColumn(nullable=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentText(): ?string
    {
        return $this->commentText;
    }

    public function setCommentText(?string $commentText): self
    {
//        dd($commentText);
        $this->commentText = $commentText;

        return $this;
    }

    public function getIsCommentPublic(): ?bool
    {
        return $this->isCommentPublic;
    }

    public function setIsCommentPublic(bool $isCommentPublic): self
    {
        $this->isCommentPublic = $isCommentPublic;

        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
    public function __toString() : string
    {
        return $this->getCommentText();
        // TODO: Implement eraseCredentials() method.
    }
}
