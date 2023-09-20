<?php

namespace App\Entity;

use App\Repository\HomeRowItemOperationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HomeRowItemOperationRepository::class)
 */
class HomeRowItemOperation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=HomeRowItem::class, inversedBy="homeRowItemOperations")
     */
    private $home_row_item;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $item_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $streamer_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $game_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priority;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $is_whitelisted;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $is_backlisted;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $is_full_site_backlisted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHomeRowItem(): ?HomeRowItem
    {
        return $this->home_row_item;
    }

    public function setHomeRowItem(?HomeRowItem $home_row_item): self
    {
        $this->home_row_item = $home_row_item;

        return $this;
    }

    public function getItemType(): ?string
    {
        return $this->item_type;
    }

    public function setItemType(string $item_type): self
    {
        $this->item_type = $item_type;

        return $this;
    }

    public function getStreamerId(): ?string
    {
        return $this->streamer_id;
    }

    public function setStreamerId(?string $streamer_id): self
    {
        $this->streamer_id = $streamer_id;

        return $this;
    }

    public function getGameId(): ?string
    {
        return $this->game_id;
    }

    public function setGameId(?string $game_id): self
    {
        $this->game_id = $game_id;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getIsWhitelisted(): ?int
    {
        return $this->is_whitelisted;
    }

    public function setIsWhitelisted(?int $is_whitelisted): self
    {
        $this->is_whitelisted = $is_whitelisted;

        return $this;
    }

    public function getIsBacklisted(): ?int
    {
        return $this->is_backlisted;
    }

    public function setIsBacklisted(?int $is_backlisted): self
    {
        $this->is_backlisted = $is_backlisted;

        return $this;
    }

    public function getIsFullSiteBacklisted(): ?int
    {
        return $this->is_full_site_backlisted;
    }

    public function setIsFullSiteBacklisted(?int $is_full_site_backlisted): self
    {
        $this->is_full_site_backlisted = $is_full_site_backlisted;

        return $this;
    }
}
