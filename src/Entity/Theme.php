<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Symfony\Component\HttpFoundation\File\{ File, UploadedFile };
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @Vich\Uploadable
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
     * @Vich\UploadableField(mapping="theme_banner", fileNameProperty="bannerImage")
     *
     * @var File|null
     */
    private $bannerImageFile;

    const IMAGE_TYPE_EMBED_BACKGROUND = 'embed';
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $embedBackground;

    /**
     * @Vich\UploadableField(mapping="theme_embed", fileNameProperty="embedBackground")
     *
     * @var File|null
     */
    private $embedBackgroundFile;

    const IMAGE_TYPE_ART_BACKGROUND = 'art';
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $artBackground;

    /**
     * @Vich\UploadableField(mapping="theme_art", fileNameProperty="artBackground")
     *
     * @var File|null
     */
    private $artBackgroundFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * Need an updateable field as a fix for vich uploader bug
     * https://github.com/dustin10/VichUploaderBundle/blob/master/docs/known_issues.md
     */
    private $updatedAt;

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

    public function getBannerImageFile(): ?File
    {
        return $this->bannerImageFile;
    }

    public function setBannerImageFile(?File $bannerImageFile): self
    {
        $this->bannerImageFile = $bannerImageFile;

        if ($this->bannerImageFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getBannerImageSlug(): ?string
    {
        return $this->getItemType().'-'.$this->getTwitchId().'-banner';
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

    public function getEmbedBackgroundFile(): ?File
    {
        return $this->embedBackgroundFile;
    }

    public function setEmbedBackgroundFile(?File $embedBackgroundFile): self
    {
        $this->embedBackgroundFile = $embedBackgroundFile;
        if ($this->embedBackgroundFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getEmbedBackgroundSlug(): ?string
    {
        return $this->getItemType().'-'.$this->getTwitchId().'-embedbg';
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

    public function getArtBackgroundFile(): ?File
    {
        return $this->artBackgroundFile;
    }

    public function setArtBackgroundFile(?File $artBackgroundFile): self
    {
        $this->artBackgroundFile = $artBackgroundFile;
        if ($this->artBackgroundFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getArtBackgroundSlug(): ?string
    {
        return $this->getItemType().'-'.$this->getTwitchId().'-artbg';
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    public function __toString(): string
    {
        if ($this->getLabel()) {
            $label = $this->getLabel();
        } else if ($this->getTwitchId()) {
            $label = $this->getTwitchId();
        }
        if ($this->getItemType()) {
            return ucfirst('Theme for '.$this->getItemType() . ' \'' . $label. '\'');
        } else {
            return $label;
        }
    }

}
