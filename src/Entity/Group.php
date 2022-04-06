<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $linkToken;

    #[ORM\Column(type: 'datetime')]
    private $lastArchived;

    #[ORM\OneToMany(mappedBy: 'group_event', targetEntity: Event::class)]
    private $link_token;

    public function __construct()
    {
        $this->link_token = new ArrayCollection();
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
}
