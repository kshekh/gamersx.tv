<?php

namespace App\Entity;

use App\Repository\HomeRowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class HomeRow
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * The index of the item in the HomeRow
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $sortIndex;

    const ITEM_TYPE_GAME = 'game';
    const ITEM_TYPE_STREAMER = 'streamer';
    const ITEM_TYPE_CHANNEL = 'channel';
    const ITEM_TYPE_POPULAR = 'popular';
    const ITEM_TYPE_YOUTUBE = 'youtube';

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $itemType;

    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';
    const SORT_FIXED = 'fixed';
    /**
     * @ORM\Column(type="string", length=8)
     */
    private $itemSortType;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $options = [];

    /**
     * @ORM\OneToMany(targetEntity=HomeRowItem::class, mappedBy="homeRow", orphanRemoval=true)
     * @ORM\OrderBy({"sortIndex" = "ASC"})
     */
    private $items;


    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getItemSortType(): ?string
    {
        return $this->itemSortType;
    }

    public function setItemSortType(string $itemSortType): self
    {
        $this->itemSortType = $itemSortType;

        return $this;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(?array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return Collection|HomeRowItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(HomeRowItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setHomeRow($this);
        }

        return $this;
    }

    public function removeItem(HomeRowItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getHomeRow() === $this) {
                $item->setHomeRow(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        if ($this->getTitle() && $this->getItemType()) {
            return $this->getTitle() . ' (' . $this->getItemType() . ' row)';
        } else {
            return '';
        }
    }

}
