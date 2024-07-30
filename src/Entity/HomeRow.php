<?php

namespace App\Entity;

use App\Model\PartneredInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class HomeRow implements PartneredInterface
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

    /**
     * The name of the vue component for this row
     *
     * @ORM\Column(type="string", length=32)
     */
    private $layout;

    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';
    const SORT_FIXED = 'fixed';
    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $options = [];

    /**
     * @ORM\OneToMany(targetEntity=HomeRowItem::class, mappedBy="homeRow", orphanRemoval=true)
     * @ORM\OrderBy({"sortIndex" = "ASC"})
     */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity=Partner::class, inversedBy="homeRows")
     */
    private $partner;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $isGlowStyling;

    /**
     * @ORM\Column(type="string", length=50, options={"default" : 0})
     */
    private $isCornerCut;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timezone;

    /**
     * @ORM\Column(name="isPublishedStart",type="string", length=255, nullable=true)
     */

    private $isPublishedStart;

    /**
     * @ORM\Column(name="isPublishedEnd",type="string", length=255, nullable=true)
     */
    private $isPublishedEnd;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $onGamersXtv;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default" : 0})
     */
    private $row_padding_top;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default" : 0})
     */
    private $row_padding_bottom;

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

    public function getLayout(): ?string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): self
    {
        $this->layout = $layout;

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

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }

    public function __toString(): string
    {
        if ($this->getTitle()) {
            return $this->getTitle();
        } else {
            return '';
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

    public function getIsGlowStyling(): ?string
    {
        return $this->isGlowStyling;
    }

    public function setIsGlowStyling(string $isGlowStyling): self
    {
        $this->isGlowStyling = $isGlowStyling;

        return $this;
    }

    public function getIsCornerCut(): ?string
    {
        return $this->isCornerCut;
    }

    public function setIsCornerCut(string $isCornerCut): self
    {
        $this->isCornerCut = $isCornerCut;

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

    public function getonGamersXtv(): ?bool
    {
        return $this->onGamersXtv;
    }

    public function setonGamersXtv(bool $onGamersXtv): self
    {
        $this->onGamersXtv = $onGamersXtv;

        return $this;
    }

    public function getRowPaddingTop(): ?int
    {
        return $this->row_padding_top;
    }

    public function setRowPaddingTop(?int $row_padding_top): self
    {
        $this->row_padding_top = $row_padding_top;

        return $this;
    }

    public function getRowPaddingBottom(): ?int
    {
        return $this->row_padding_bottom;
    }

    public function setRowPaddingBottom(?int $row_padding_bottom): self
    {
        $this->row_padding_bottom = $row_padding_bottom;

        return $this;
    }

}