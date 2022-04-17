<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $isArchived;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToOne(targetEntity: Ticket::class, cascade: ['persist', 'remove'])]
    private $ticket_status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
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

    public function getTicketStatus(): ?Ticket
    {
        return $this->ticket_status;
    }

    public function setTicketStatus(?Ticket $ticket_status): self
    {
        $this->ticket_status = $ticket_status;

        return $this;
    }
}
