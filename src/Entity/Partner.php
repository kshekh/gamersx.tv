<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Partner
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=PartnerRole::class, mappedBy="partner")
     */
    private $partnerRoles;

    /**
     * @ORM\OneToMany(targetEntity=HomeRow::class, mappedBy="partner")
     */
    private $homeRows;

    /**
     * @ORM\OneToMany(targetEntity=HomeRowItem::class, mappedBy="partner")
     */
    private $homeRowItems;

    public function __construct()
    {
        $this->partnerRoles = new ArrayCollection();
        $this->homeRows = new ArrayCollection();
        $this->homeRowItems = new ArrayCollection();
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
            $partnerRole->setPartner($this);
        }

        return $this;
    }

    public function removePartnerRole(PartnerRole $partnerRole): self
    {
        if ($this->partnerRoles->removeElement($partnerRole)) {
            // set the owning side to null (unless already changed)
            if ($partnerRole->getPartner() === $this) {
                $partnerRole->setPartner(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        if ($this->getName()) {
            return $this->getName();
        } else {
            return '';
        }
    }

    /**
     * @return Collection|HomeRow[]
     */
    public function getHomeRows(): Collection
    {
        return $this->homeRows;
    }

    public function addHomeRow(HomeRow $homeRow): self
    {
        if (!$this->homeRows->contains($homeRow)) {
            $this->homeRows[] = $homeRow;
            $homeRow->setPartner($this);
        }

        return $this;
    }

    public function removeHomeRow(HomeRow $homeRow): self
    {
        if ($this->homeRows->removeElement($homeRow)) {
            // set the owning side to null (unless already changed)
            if ($homeRow->getPartner() === $this) {
                $homeRow->setPartner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HomeRowItem[]
     */
    public function getHomeRowItems(): Collection
    {
        return $this->homeRowItems;
    }

    public function addHomeRowItem(HomeRowItem $homeRowItem): self
    {
        if (!$this->homeRowItems->contains($homeRowItem)) {
            $this->homeRowItems[] = $homeRowItem;
            $homeRowItem->setPartner($this);
        }

        return $this;
    }

    public function removeHomeRowItem(HomeRowItem $homeRowItem): self
    {
        if ($this->homeRowItems->removeElement($homeRowItem)) {
            // set the owning side to null (unless already changed)
            if ($homeRowItem->getPartner() === $this) {
                $homeRowItem->setPartner(null);
            }
        }

        return $this;
    }

}
