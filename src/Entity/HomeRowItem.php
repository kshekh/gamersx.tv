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
     * The item's ID in the Twitch API
     *
     * @ORM\Column(type="string", length=32)
     */
    private $itemId;

    /**
     * The index of the item in the HomeRow
     *
     * @ORM\Column(type="smallint")
     */
    private $sortIndex;

    /**
     * Whether to show box/profile art for this item
     *
     * @ORM\Column(type="boolean")
     */
    private $showArt;

    /**
     * Whether to show an embedded stream for this item
     *
     * @ORM\Column(type="boolean")
     */
    private $showStream;

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

    public function getItemId(): ?string
    {
        return $this->itemId;
    }

    public function setItemId(string $itemId): self
    {
        $this->itemId = $itemId;

        return $this;
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

    public function getShowArt(): ?bool
    {
        return $this->showArt;
    }

    public function setShowArt(bool $showArt): self
    {
        $this->showArt = $showArt;

        return $this;
    }

    public function getShowStream(): ?bool
    {
        return $this->showStream;
    }

    public function setShowStream(bool $showStream): self
    {
        $this->showStream = $showStream;

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

    public function getItemType(): ?string
    {
        return $this->getHomeRow()->itemType;
    }

}
