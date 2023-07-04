<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user__user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity=PartnerRole::class, mappedBy="user", orphanRemoval=true)
     */
    private $partnerRoles;

    public function __construct()
    {
        parent::__construct();
        $this->partnerRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|PartnerRole[]
     */
    public function getPartnerRoles(): Collection
    {
        return $this->partnerRoles;
    }

    public function addPartnerRole(PartnerRole $partnerRole): self
    {
        if (!$this->partnerRoles->contains($partnerRole)) {
            $this->partnerRoles[] = $partnerRole;
            $partnerRole->setUser($this);
        }

        return $this;
    }

    public function removePartnerRole(PartnerRole $partnerRole): self
    {
        if ($this->partnerRoles->removeElement($partnerRole)) {
            // set the owning side to null (unless already changed)
            if ($partnerRole->getUser() === $this) {
                $partnerRole->setUser(null);
            }
        }

        return $this;
    }
}
