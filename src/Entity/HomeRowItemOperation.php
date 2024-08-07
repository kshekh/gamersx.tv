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
     * @ORM\Column(type="string", length=255, nullable=true,options={"comment":"itemtype=streamer => livestreaming_id,itemtype=offline_streamer => userid"})
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
    private $is_blacklisted;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $is_full_site_blacklisted;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $streamer_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $game_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $viewer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user_id;

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

    public function getIsBlacklisted(): ?int
    {
        return $this->is_blacklisted;
    }

    public function setIsBlacklisted(?int $is_blacklisted): self
    {
        $this->is_blacklisted = $is_blacklisted;

        return $this;
    }

    public function getIsFullSiteBlacklisted(): ?int
    {
        return $this->is_full_site_blacklisted;
    }

    public function setIsFullSiteBlacklisted(?int $is_full_site_blacklisted): self
    {
        $this->is_full_site_blacklisted = $is_full_site_blacklisted;

        return $this;
    }

    public function getStreamerName(): ?string
    {
        return $this->streamer_name;
    }

    public function setStreamerName(?string $streamer_name): self
    {
        $this->streamer_name = $streamer_name;

        return $this;
    }

    public function getGameName(): ?string
    {
        return $this->game_name;
    }

    public function setGameName(?string $game_name): self
    {
        $this->game_name = $game_name;

        return $this;
    }

    public function getViewer(): ?string
    {
        return $this->viewer;
    }

    public function setViewer(?string $viewer): self
    {
        $this->viewer = $viewer;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function setUserId(?string $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
