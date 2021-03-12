<?php

namespace App\Entity;

use App\Repository\HomeRowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HomeRowRepository::class)
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

    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';
    /**
     * @ORM\Column(type="string", length=8)
     */
    private $sort;

    const ITEM_TYPE_GAME = 'game';
    const ITEM_TYPE_STREAMER = 'streamer';
    const ITEM_TYPE_POPULAR = 'popular';
    /**
     * @ORM\Column(type="string", length=32)
     */
    private $itemType;

    /**
     * @ORM\Column(type="boolean")
     */
    private $displayOffline;

    /**
     * @ORM\OneToMany(targetEntity=HomeRowItem::class, mappedBy="homeRow", orphanRemoval=true)
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

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function setSort(string $sort): self
    {
        $this->sort = $sort;

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

    public function getDisplayOffline(): ?bool
    {
        return $this->displayOffline;
    }

    public function setDisplayOffline(bool $displayOffline): self
    {
        $this->displayOffline = $displayOffline;

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

}
