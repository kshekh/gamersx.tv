<?php

namespace App\Entity;

use App\Repository\HomeRowItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HomeRowItemRepository::class)
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
     * The index of the item in the HomeRow
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $sortIndex;

    /**
     * The item's ID in the Twitch API
     *
     * @ORM\Column(type="string", length=32)
     */
    private $twitchId;

    /**
     * Whether to show box/profile art for this item
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
    const LINK_TYPE_TWITCH = 'twitch';
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


    /**
     * Gets the item's type from the parent row
     */
    public function getItemType(): ?string
    {
        return $this->getHomeRow()->getItemType();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSortIndex(): ?int
    {
        return $this->sortIndex;
    }

    public function setSortIndex(int $sortIndex): self
    {
        $this->sortIndex = $sortIndex;

        return $this;
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
        if ($this->getTwitchId() && $this->getItemType()) {
            return ucfirst($this->getItemType() . ' \'' . $this->getTwitchId()  . '\'');
        } else {
            return '';
        }
    }

}
