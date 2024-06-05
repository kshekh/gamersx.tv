<?php

namespace App\Entity;

use App\Repository\MasterSettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MasterSettingRepository::class)]
class MasterSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'masterSettings')]
    private ?MasterTheme $master_theme = null;

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
