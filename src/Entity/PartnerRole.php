<?php

namespace App\Entity;

use App\Repository\PartnerRoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartnerRoleRepository::class)]
class PartnerRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'partnerRoles')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'partnerRoles')]
    private ?Partner $partner = null;

    const ADMIN = 'ROLE_PARTNER_ADMIN';
    const EDITOR = 'ROLE_PARTNER_EDITOR';
    const AD_EDITOR = 'ROLE_PARTNER_AD_EDITOR';

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

}
