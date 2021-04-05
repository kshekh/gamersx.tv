<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    private $twitchId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $itemType;

    const IMAGE_TYPE_BANNER = 'banner';
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bannerImage;

    /**
     * This is a non-persisted object to assist with saving
     */
    private $bannerImageFile;

    const IMAGE_TYPE_EMBED_BACKGROUND = 'embed';
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $embedBackground;

    /**
     * This is a non-persisted object to assist with saving
     */
    private $embedBackgroundFile;

    const IMAGE_TYPE_ART_BACKGROUND = 'art';
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $artBackground;

    /**
     * This is a non-persisted object to assist with saving
     */
    private $artBackgroundFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTwitchId(): ?string
    {
        return $this->twitchId;
    }

    public function setTwitchId(string $twitchId): self
    {
        $this->twitchId = $twitchId;

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

    public function getBannerImage(): ?string
    {
        return $this->bannerImage;
    }

    public function setBannerImage(?string $bannerImage): self
    {
        $this->bannerImage = $bannerImage;

        return $this;
    }

    public function getBannerImageFile(): ?UploadedFile
    {
        return $this->bannerImageFile;
    }

    public function setBannerImageFile(?UploadedFile $bannerImageFile): self
    {
        $this->bannerImageFile = $bannerImageFile;

        return $this;
    }

    public function getEmbedBackground(): ?string
    {
        return $this->embedBackground;
    }

    public function setEmbedBackground(?string $embedBackground): self
    {
        $this->embedBackground = $embedBackground;

        return $this;
    }

    public function getEmbedBackgroundFile(): ?UploadedFile
    {
        return $this->embedBackgroundFile;
    }

    public function setEmbedBackgroundFile(?UploadedFile $embedBackgroundFile): self
    {
        $this->embedBackgroundFile = $embedBackgroundFile;

        return $this;
    }

    public function getArtBackground(): ?string
    {
        return $this->artBackground;
    }

    public function setArtBackground(?string $artBackground): self
    {
        $this->artBackground = $artBackground;

        return $this;
    }

    public function getArtBackgroundFile(): ?UploadedFile
    {
        return $this->artBackgroundFile;
    }

    public function setArtBackgroundFile(?UploadedFile $artBackgroundFile): self
    {
        $this->artBackgroundFile = $artBackgroundFile;

        return $this;
    }

}
