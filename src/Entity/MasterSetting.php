<?php

namespace App\Entity;

use App\Repository\MasterSettingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MasterSettingRepository::class)
 */
class MasterSetting
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=MasterTheme::class, inversedBy="masterSettings")
     */
    private $master_theme;

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getMasterTheme(): ?MasterTheme
    {
        return $this->master_theme;
    }

    public function setMasterTheme(?MasterTheme $master_theme): self
    {
        $this->master_theme = $master_theme;

        return $this;
    }
}
