<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
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
        minMessage: 'Le nom de votre événement doit faire {{ limit }} caractères au minimum.',
        maxMessage: 'Le nom de votre événement doit faire {{ limit }} caractères au maximum.',
    )]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $adminLinkToken;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $email;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Group::class)]
    private $group_id;

    public function __construct()
    {
        $this->group_id = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Group>
     */
    public function getGroupId(): Collection
    {
        return $this->group_id;
    }

    public function addGroupId(Group $groupId): self
    {
        if (!$this->group_id->contains($groupId)) {
            $this->group_id[] = $groupId;
            $groupId->setEvent($this);
        }

        return $this;
    }

    public function removeGroupId(Group $groupId): self
    {
        if ($this->group_id->removeElement($groupId)) {
            // set the owning side to null (unless already changed)
            if ($groupId->getEvent() === $this) {
                $groupId->setEvent(null);
            }
        }

        return $this;
    }
}
