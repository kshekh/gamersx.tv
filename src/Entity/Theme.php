<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThemeRepository::class)
 */
class Theme
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $TwitchId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $itemType;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasBannerImage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasArtBackground;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasEmbedBackground;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTwitchId(): ?string
    {
        return $this->TwitchId;
    }

    public function setTwitchId(string $TwitchId): self
    {
        $this->TwitchId = $TwitchId;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getItemType(): ?string
    {
        return $this->itemType;
    }

    public function setItemType(string $itemType): self
    {
        $this->itemType = $itemType;

        return $this;
    }

    public function getHasBannerImage(): ?bool
    {
        return $this->hasBannerImage;
    }

    public function setHasBannerImage(bool $hasBannerImage): self
    {
        $this->hasBannerImage = $hasBannerImage;

        return $this;
    }

    public function getHasArtBackground(): ?bool
    {
        return $this->hasArtBackground;
    }

    public function setHasArtBackground(bool $hasArtBackground): self
    {
        $this->hasArtBackground = $hasArtBackground;

        return $this;
    }

    public function getHasEmbedBackground(): ?bool
    {
        return $this->hasEmbedBackground;
    }

    public function setHasEmbedBackground(bool $hasEmbedBackground): self
    {
        $this->hasEmbedBackground = $hasEmbedBackground;

        return $this;
    }

}
