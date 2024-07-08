<?php

namespace App\Entity;

use App\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "fos_user__group")]
class Group extends BaseGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    protected ?int $id = null;

    #[ORM\Column(type: "string", nullable: true)] // Adjusted to nullable string
    protected ?string $name = null; // Adjusted to nullable string

    #[ORM\Column(type: "json")]
    protected array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string // Adjusted to nullable string
    {
        return $this->name;
    }

    public function setName(?string $name): self // Adjusted to nullable string
    {
        $this->name = $name;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
}
