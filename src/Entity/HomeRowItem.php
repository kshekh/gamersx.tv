<?php

namespace App\Entity;

use App\Model\PartneredInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class HomeRowItem implements PartneredInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * The index of the item in the HomeRow
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $sortIndex;

    const TYPE_GAME = 'game';
    const TYPE_STREAMER = 'streamer';
    const TYPE_CHANNEL = 'channel';
    const TYPE_POPULAR = 'popular';
    const TYPE_YOUTUBE = 'youtube';
    const TYPE_LINK = 'link';
    const TYPE_TWITCH_VIDEO = 'twitch_video';
    const TYPE_TWITCH_PLAYLIST = 'twitch_playlist';
    const TYPE_YOUTUBE_VIDEO = 'youtube_video';
    const TYPE_YOUTUBE_PLAYLIST = 'youtube_playlist';
    /**
     * @ORM\Column(type="string", length=32)
     */
    private $itemType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $videoId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $playlistId;

    /**
     * Options to be passed into Containerizer
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $topic = [];

    /**
     * Options to be passed into Containerizer
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $sortAndTrimOptions = [];

    /**
     * Whether to always show box/profile art for this item
     *
     * @ORM\Column(type="boolean")
     */
    private $showArt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customArt;

    /**
     * @Vich\UploadableField(mapping="hri_custom", fileNameProperty="customArt")
     *
     * @var File|null
     */
    private $customArtFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $overlayArt;

    /**
     * @Vich\UploadableField(mapping="hri_overlay", fileNameProperty="overlayArt")
     *
     * @var File|null
     */
    private $overlayArtFile;

    const OFFLINE_DISPLAY_ART = 'art';
    const OFFLINE_DISPLAY_OVERLAY = 'overlay';
    const OFFLINE_DISPLAY_STREAM = 'stream';
    const OFFLINE_DISPLAY_NONE = 'none';
    /**
     * @ORM\Column(type="string", length=32)
     */
    private $offlineDisplayType;

    const LINK_TYPE_GAMERSX = 'gamersx';
    const LINK_TYPE_EXTERNAL = 'external';
    const LINK_TYPE_CUSTOM = 'custom';
    /**
     * @ORM\Column(type="string", length=32)
     */
    private $linkType;

    /**
     * The Home Row this item belongs to
     *
     * @ORM\ManyToOne(targetEntity=HomeRow::class, inversedBy="items")
     * @ORM\JoinColumn()
     */
    private $homeRow;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customLink;

    /**
     * @ORM\ManyToOne(targetEntity=Partner::class, inversedBy="homeRowItems")
     */
    private $partner;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timezone;

    /**
     * @ORM\Column(name="isPublishedStart",type="string", length=255, nullable=true)
     */
    private $isPublishedStart;

    /**
     * @ORM\Column(name="isPublishedEnd", type="string", length=255, nullable=true)
     */
    private $isPublishedEnd;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $isPartner;

    /**
     * @ORM\OneToMany(targetEntity=HomeRowItemOperation::class, mappedBy="home_row_item")
     */
    private $homeRowItemOperations;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $is_unique_container;

    public function __construct()
    {
        $this->homeRowItemOperations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSortIndex(): ?int
    {
        return $this->sortIndex;
    }

    public function setSortIndex(?int $sortIndex): self
    {
        $this->sortIndex = $sortIndex;

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

    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    public function setVideoId(?string $videoId): self
    {
        $this->videoId = $videoId;

        return $this;
    }

    public function getPlaylistId(): ?string
    {
        return $this->playlistId;
    }

    public function setPlaylistId(?string $playlistId): self
    {
        $this->playlistId = $playlistId;

        return $this;
    }

    public function getTopic(): ?array
    {
        return $this->topic;
    }

    public function setTopic(?array $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getSortAndTrimOptions(): ?array
    {
        return $this->sortAndTrimOptions;
    }

    public function setSortAndTrimOptions(?array $sortAndTrimOptions): self
    {
        $this->sortAndTrimOptions = $sortAndTrimOptions;

        return $this;
    }
    public function getShowArt(): ?bool
    {
        return $this->showArt;
    }

    public function setShowArt(bool $showArt): self
    {
        $this->showArt = $showArt;

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
        if (null !== $customArtFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getOverlayArt(): ?string
    {
        return $this->overlayArt;
    }

    public function setOverlayArt(?string $overlayArt): self
    {
        $this->overlayArt = $overlayArt;

        return $this;
    }

    public function getOverlayArtFile(): ?File
    {
        return $this->overlayArtFile;
    }

    public function setOverlayArtFile(?File $overlayArtFile): self
    {
        $this->overlayArtFile = $overlayArtFile;
        if (null !== $overlayArtFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getOfflineDisplayType(): ?string
    {
        return $this->offlineDisplayType;
    }

    public function setOfflineDisplayType(string $offlineDisplayType): self
    {
        $this->offlineDisplayType = $offlineDisplayType;

        return $this;
    }

    public function getLinkType(): ?string
    {
        return $this->linkType;
    }

    public function setLinkType(string $linkType): self
    {
        $this->linkType = $linkType;

        return $this;
    }

    public function getCustomLink(): ?string
    {
        return $this->customLink;
    }

    public function setCustomLink(?string $customLink): self
    {
        $this->customLink = $customLink;

        return $this;
    }

    public function getHomeRow(): ?HomeRow
    {
        return $this->homeRow;
    }

    public function setHomeRow(?HomeRow $homeRow): self
    {
        $this->homeRow = $homeRow;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function __toString(): string
    {
        if ($this->getLabel()) {
            $label = $this->getLabel();
        } else {
            $label = '';
        }

        if ($this->getItemType()) {
            return ucfirst($this->getItemType() . ' \'' . $label. '\'');
        } else {
            return $label;
        }
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }
    public function getIsPublishedStart(): ?string
    {
        return $this->isPublishedStart;
    }

    public function setIsPublishedStart(?string $isPublishedStart): self
    {
        $this->isPublishedStart = $isPublishedStart;

        return $this;
    }

    public function getIsPublishedEnd(): ?string
    {
        return $this->isPublishedEnd;
    }

    public function setIsPublishedEnd(?string $isPublishedEnd): self
    {
        $this->isPublishedEnd = $isPublishedEnd;

        return $this;
    }

    public function getIsPartner(): ?bool
    {
        return $this->isPartner;
    }

    public function setIsPartner(bool $isPartner = false): self
    {
        $this->isPartner = $isPartner;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Collection<int, HomeRowItemOperation>
     */
    public function getHomeRowItemOperations(): Collection
    {
        return $this->homeRowItemOperations;
    }

    public function addHomeRowItemOperation(HomeRowItemOperation $homeRowItemOperation): self
    {
        if (!$this->homeRowItemOperations->contains($homeRowItemOperation)) {
            $this->homeRowItemOperations[] = $homeRowItemOperation;
            $homeRowItemOperation->setHomeRowItem($this);
        }

        return $this;
    }

    public function removeHomeRowItemOperation(HomeRowItemOperation $homeRowItemOperation): self
    {
        if ($this->homeRowItemOperations->removeElement($homeRowItemOperation)) {
            // set the owning side to null (unless already changed)
            if ($homeRowItemOperation->getHomeRowItem() === $this) {
                $homeRowItemOperation->setHomeRowItem(null);
            }
        }

        return $this;
    }

    public function getIsUniqueContainer(): ?bool
    {
        return $this->is_unique_container;
    }

    public function setIsUniqueContainer(bool $is_unique_container): self
    {
        $this->is_unique_container = $is_unique_container;

        return $this;
    }
}