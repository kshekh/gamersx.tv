<?php

namespace App\Entity;

use App\Repository\HomeRowItemOperationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HomeRowItemOperationRepository::class)]

class HomeRowItemOperation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $item_type = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => 'itemType=streamer => livestreaming_id, itemType=offline_streamer => userid'])]
    private ?string $streamer_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $game_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $priority = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $is_whitelisted = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $is_blacklisted = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $is_full_site_blacklisted = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $streamer_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $game_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $viewer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'homeRowItemOperations')]
    private ?HomeRowItem $homeRowItem = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHomeRowItem(): ?HomeRowItem
    {
        return $this->homeRowItem;
    }

    public function setHomeRowItem(?HomeRowItem $homeRowItem): self
    {
        $this->homeRowItem = $homeRowItem;

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
