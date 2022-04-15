<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $adminLinkToken;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    // #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'link_token')]
    // private $group_event;

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

    public function getAdminLinkToken(): ?string
    {
        return $this->adminLinkToken;
    }

    public function setAdminLinkToken(string $adminLinkToken): self
    {
        $this->adminLinkToken = $adminLinkToken;

        return $this;
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

    // public function getGroupEvent(): ?Group
    // {
    //     return $this->group_event;
    // }

    // public function setGroupEvent(?Group $group_event): self
    // {
    //     $this->group_event = $group_event;

    //     return $this;
    // }
}