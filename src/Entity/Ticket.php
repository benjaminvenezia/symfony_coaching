<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 2000)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 7,
        max: 2000,
        minMessage: 'Votre description doit faire {{ limit }} caractères au minimum.',
        maxMessage: 'Votre description doit faire {{ limit }} caractères au maximum.',
    )]
    private $request;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'group_id')]
    private $group_ticket_id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 7,
        max: 50,
        minMessage: 'Votre titre doit faire {{ limit }} caractères au minimum.',
        maxMessage: 'Votre titre doit faire {{ limit }} caractères au maximum.',
    )]
    private $title;

    // #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'tickets')]
    // private $linkToken;


    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->updatedAt = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(string $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    // public function getLinkToken(): ?Group
    // {
    //     return $this->linkToken;
    // }

    // public function setLinkToken(?Group $linkToken): self
    // {
    //     $this->linkToken = $linkToken;

    //     return $this;
    // }

    public function getGroupTicketId(): ?Group
    {
        return $this->group_ticket_id;
    }

    public function setGroupTicketId(?Group $group_ticket_id): self
    {
        $this->group_ticket_id = $group_ticket_id;

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
}
