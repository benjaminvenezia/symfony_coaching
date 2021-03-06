<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 7,
        max: 255,
        minMessage: 'Le nom du groupe doit faire {{ limit }} caractères au minimum.',
        maxMessage: 'Le nom du groupe doit faire {{ limit }} caractères au maximum.',
    )]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $linkToken;

    #[ORM\Column(type: 'datetime')]
    private $lastArchived;

    #[ORM\OneToMany(mappedBy: 'group_event', targetEntity: Event::class)]
    private $link_token;

    // #[ORM\OneToMany(mappedBy: 'linkToken', targetEntity: Ticket::class)]
    // private $tickets;

    #[ORM\Column(type: 'integer')]
    private $helped_counter;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $last_helped;

    #[ORM\OneToMany(mappedBy: 'group_ticket_id', targetEntity: Ticket::class)]
    private $group_id;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'group_id')]
    private $event;

    public function __construct()
    {
        $this->link_token = new ArrayCollection();
        // $this->tickets = new ArrayCollection();
        $this->helped_counter = 0;
        $this->group_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLinkToken(): ?string
    {
        return $this->linkToken;
    }

    public function setLinkToken(string $linkToken): self
    {
        $this->linkToken = $linkToken;

        return $this;
    }

    public function getLastArchived(): ?\DateTimeInterface
    {
        return $this->lastArchived;
    }

    public function setLastArchived(\DateTimeInterface $lastArchived): self
    {
        $this->lastArchived = $lastArchived;

        return $this;
    }

    public function addLinkToken(Event $linkToken): self
    {
        if (!$this->link_token->contains($linkToken)) {
            $this->link_token[] = $linkToken;
            // $linkToken->setGroupEvent($this);
        }

        return $this;
    }

    public function removeLinkToken(Event $linkToken): self
    {
        if ($this->link_token->removeElement($linkToken)) {
            // set the owning side to null (unless already changed)
            // if ($linkToken->getGroupEvent() === $this) {
            //     $linkToken->setGroupEvent(null);
            // }
        }

        return $this;
    }

    

    // /**
    //  * @return Collection<int, Ticket>
    //  */
    // public function getTickets(): Collection
    // {
    //     return $this->tickets;
    // }

    // public function addTicket(Ticket $ticket): self
    // {
    //     if (!$this->tickets->contains($ticket)) {
    //         $this->tickets[] = $ticket;
    //         $ticket->setLinkToken($this);
    //     }

    //     return $this;
    // }

    // public function removeTicket(Ticket $ticket): self
    // {
    //     if ($this->tickets->removeElement($ticket)) {
    //         // set the owning side to null (unless already changed)
    //         if ($ticket->getLinkToken() === $this) {
    //             $ticket->setLinkToken(null);
    //         }
    //     }

    //     return $this;
    // }

    public function getHelpedCounter(): ?int
    {
        return $this->helped_counter;
    }
    
    public function setHelpedCounter(int $helped_counter): self
    {
        $this->helped_counter = $helped_counter;
      
        return $this;
    }

    public function incrementHelpedCounter() : void {
        $this->last_helped = new DateTime('now');
        $this->helped_counter++;
    }

    public function getLastHelped(): ?\DateTimeInterface
    {
        return $this->last_helped;
    }

    public function setLastHelped(?\DateTimeInterface $last_helped): self
    {
        $this->last_helped = $last_helped;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getGroupId(): Collection
    {
        return $this->group_id;
    }

    public function addGroupId(Ticket $groupId): self
    {
        if (!$this->group_id->contains($groupId)) {
            $this->group_id[] = $groupId;
            $groupId->setGroupTicketId($this);
        }

        return $this;
    }

    public function removeGroupId(Ticket $groupId): self
    {
        if ($this->group_id->removeElement($groupId)) {
            // set the owning side to null (unless already changed)
            if ($groupId->getGroupTicketId() === $this) {
                $groupId->setGroupTicketId(null);
            }
        }

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }
}
