<?php

namespace App\Entity;

use App\Model\PartneredInterface;
use App\Repository\HomeRowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HomeRowRepository::class)]
class HomeRow implements PartneredInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $sortIndex = null;


    #[ORM\Column(length: 32)]
    private ?string $layout = null;

    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';
    const SORT_FIXED = 'fixed';

    #[ORM\Column(nullable: true)]
    private ?array $options = null;

    #[ORM\OneToMany(mappedBy: 'homeRow', targetEntity: HomeRowItem::class, orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private ?Collection $items;

    #[ORM\ManyToOne(inversedBy: 'homeRows')]
    private ?Partner $partner = null;

    #[ORM\Column]
    private bool $isPublished;

    #[ORM\Column(length: 50)]
    private ?string $isGlowStyling = null;

    #[ORM\Column(length: 50, options: ['default' => 0])]
    private ?string $isCornerCut = null;

    #[ORM\Column(length: 255)]
    private ?string $timezone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $isPublishedStart = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $isPublishedEnd = null;

    #[ORM\Column(options: ['default' => 0])]
    private ?bool $onGamersXtv = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private ?int $row_padding_top = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private ?int $row_padding_bottom = null;

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
     * @return Collection
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