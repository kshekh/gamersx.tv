<?php

namespace App\Entity;

use App\Repository\HomeRowItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class HomeRowItem
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

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $itemType;

    /**
     * Options to be passed into Containerizer
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $containerizerOptions = [];

    /**
     * Whether to always show box/profile art for this item
     *
     * @ORM\Column(type="boolean")
     */
    private $showArt;

    const OFFLINE_DISPLAY_ART = 'art';
    const OFFLINE_DISPLAY_STREAM = 'stream';
    const OFFLINE_DISPLAY_NONE = 'none';
    /**
     * @ORM\Column(type="string", length=32)
     */
    private $offlineDisplayType;

    const LINK_TYPE_GAMERSX = 'gamersx';
    const LINK_TYPE_EXTERNAL = 'external';
    /**
     * @ORM\Column(type="string", length=32)
     */
    private $linkType;

    /**
     * The Home Row this item belongs to
     *
     * @ORM\ManyToOne(targetEntity=HomeRow::class, inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $homeRow;


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

    public function getContainerizerOptions(): ?array
    {
        return $this->containerizerOptions;
    }

    public function setContainerizerOptions(?array $containerizerOptions): self
    {
        $this->containerizerOptions = $containerizerOptions;

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

    public function getHomeRow(): ?HomeRow
    {
        return $this->homeRow;
    }

    public function setHomeRow(?HomeRow $homeRow): self
    {
        $this->homeRow = $homeRow;

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

}
