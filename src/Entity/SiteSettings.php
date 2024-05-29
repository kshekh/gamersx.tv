<?php

namespace App\Entity;

use App\Repository\SiteSettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteSettingRepository::class)]
class SiteSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(options: ['default' => 1])]
    private ?bool $disableHomeAccess = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisableHomeAccess(): ?bool
    {
        return $this->disableHomeAccess;
    }

    public function setDisableHomeAccess(bool $disableHomeAccess): self
    {
        $this->disableHomeAccess = $disableHomeAccess;

        return $this;
    }

}
