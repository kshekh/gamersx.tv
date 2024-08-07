<?php

namespace App\Entity;

use App\Repository\MasterThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MasterThemeRepository::class)
 */
class MasterTheme
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
     * @ORM\OneToMany(targetEntity=MasterSetting::class, mappedBy="master_theme")
     */
    private $masterSettings;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"comment":"0-inactive, 1-active","default"= 0})
     */
    private $status;

    public function __construct()
    {
        $this->masterSettings = new ArrayCollection();
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
     * @return Collection<int, MasterSetting>
     */
    public function getMasterSettings(): Collection
    {
        return $this->masterSettings;
    }

    public function addMasterSetting(MasterSetting $masterSetting): self
    {
        if (!$this->masterSettings->contains($masterSetting)) {
            $this->masterSettings[] = $masterSetting;
            $masterSetting->setMasterTheme($this);
        }

        return $this;
    }

    public function removeMasterSetting(MasterSetting $masterSetting): self
    {
        if ($this->masterSettings->removeElement($masterSetting)) {
            // set the owning side to null (unless already changed)
            if ($masterSetting->getMasterTheme() === $this) {
                $masterSetting->setMasterTheme(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
