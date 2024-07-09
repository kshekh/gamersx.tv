<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "fos_user__user")]
class User extends BaseUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    protected $id;

    #[ORM\OneToMany(targetEntity: PartnerRole::class, mappedBy: "user", orphanRemoval: true)]
    private $partnerRoles;

    #[ORM\Column(type: "string", length: 255)]
    private $twitchUserId;

    #[ORM\Column(type: "string", length: 500)]
    private $twitchAccessToken;

    #[ORM\Column(type: "string", length: 500)]
    private $twitchRefreshToken;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "datetime")]
    private $updatedAt;

    private array $realRoles = [];

    public function __construct()
    {
        parent::__construct();
        $this->partnerRoles = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|PartnerRole[]
     */
    public function getPartnerRoles(): Collection
    {
        return $this->partnerRoles;
    }

    public function addPartnerRole(PartnerRole $partnerRole): self
    {
        if (!$this->partnerRoles->contains($partnerRole)) {
            $this->partnerRoles[] = $partnerRole;
            $partnerRole->setUser($this);
        }

        return $this;
    }

    public function removePartnerRole(PartnerRole $partnerRole): self
    {
        if ($this->partnerRoles->removeElement($partnerRole)) {
            // set the owning side to null (unless already changed)
            if ($partnerRole->getUser() === $this) {
                $partnerRole->setUser(null);
            }
        }

        return $this;
    }

    public function getTwitchUserId(): ?string
    {
        return $this->twitchUserId;
    }

    public function setTwitchUserId(string $twitchUserId): self
    {
        $this->twitchUserId = $twitchUserId;

        return $this;
    }

    public function getTwitchAccessToken(): ?string
    {
        return $this->twitchAccessToken;
    }

    public function setTwitchAccessToken(string $twitchAccessToken): self
    {
        $this->twitchAccessToken = $twitchAccessToken;

        return $this;
    }

    public function getTwitchRefreshToken(): ?string
    {
        return $this->twitchRefreshToken;
    }

    public function setTwitchRefreshToken(string $twitchRefreshToken): self
    {
        $this->twitchRefreshToken = $twitchRefreshToken;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param array $realRoles
     */
    public function setRealRoles($realRoles)
    {
        $this->realRoles = $realRoles;
    }

    /**
     * @return array
     */
    public function getRealRoles()
    {
        return $this->realRoles;
    }


    /**
     * @return mixed
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
