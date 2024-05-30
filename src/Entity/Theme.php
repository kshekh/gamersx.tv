<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: ThemeRepository::class)]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $topicId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(length: 32)]
    private ?string $itemType = null;

    const IMAGE_TYPE_BANNER = 'banner';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bannerImage = null;

    #[Vich\UploadableField(mapping: 'theme_banner', fileNameProperty: 'bannerImage')]
    private ?File $bannerImageFile = null;

    const IMAGE_TYPE_EMBED_BACKGROUND = 'embed';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $embedBackground = null;

    #[Vich\UploadableField(mapping: 'theme_embed', fileNameProperty: 'embedBackground')]
    private ?File $embedBackgroundFile = null;

    const IMAGE_TYPE_CUSTOM_ART = 'art';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customArt = null;

    #[Vich\UploadableField(mapping: 'theme_art', fileNameProperty: 'customArt')]
    private ?File $customArtFile = null;

    const IMAGE_TYPE_ART_BACKGROUND = 'artBg';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $artBackground = null;

    #[Vich\UploadableField(mapping: 'theme_art_background', fileNameProperty: 'artBackground')]
    private ?File $artBackgroundFile = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopicId(): ?string
    {
        return $this->topicId;
    }

    public function setTopicId(string $topicId): self
    {
        $this->topicId = $topicId;

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

        if (null !== $bannerImageFile ) {
            $this->updatedAt = new \DateTimeImmutable();
        }

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

    public function getEmbedBackgroundFile(): ?File
    {
        return $this->embedBackgroundFile;
    }

    public function setEmbedBackgroundFile(?File $embedBackgroundFile): self
    {
        $this->embedBackgroundFile = $embedBackgroundFile;

        if (null !== $embedBackgroundFile ) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getCustomArt(): ?string
    {
        return $this->customArt;
    }

    public function setCustomArt(?string $customArt): self
    {
        $this->customArt = $customArt;

        return $this;
    }

    public function getCustomArtFile(): ?File
    {
        return $this->customArtFile;
    }

    public function setCustomArtFile(?File $customArtFile): self
    {
        $this->customArtFile = $customArtFile;

        if (null !== $customArtFile ) {
            $this->updatedAt = new \DateTimeImmutable();
        }

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

    public function getArtBackgroundFile(): ?File
    {
        return $this->artBackgroundFile;
    }

    public function setArtBackgroundFile(?File $artBackgroundFile): self
    {
        $this->artBackgroundFile = $artBackgroundFile;

        if (null !== $artBackgroundFile ) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }


    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    public function __toString(): string
    {
        if ($this->getLabel()) {
            $label = $this->getLabel();
        } else if ($this->getTopicId()) {
            $label = $this->getTopicId();
        }
        if ($this->getItemType()) {
            return ucfirst('Theme for '.$this->getItemType() . ' \'' . $label. '\'');
        } else {
            return $label;
        }
    }

}
