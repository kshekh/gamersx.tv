<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "fos_user__user")]
class User extends BaseUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    protected $id;

    #[ORM\OneToMany(targetEntity: PartnerRole::class, mappedBy: "user", orphanRemoval: true)]
    private ?Collection $partnerRoles;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $twitchUserId = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $twitchAccessToken = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $twitchRefreshToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $updatedAt;

//    #[ORM\Column(type: "string", length: 255)]
//    protected $gender = UserInterface::GENDER_UNKNOWN; // set the default to unknown

     const GENDER_UNKNOWN = 'unknown';
     const GENDER_FEMALE = 'female';
     const GENDER_MALE = 'male';

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected $gender =  self::GENDER_UNKNOWN;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $website = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $biography = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $locale = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $timezone = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $facebookUid = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $facebookName = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $facebookData = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $twitterUid = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $twitterName = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $twitterData = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $gplusUid = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $gplusName = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $token = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $twoStepCode = null;

    #[ORM\Column(type: "datetime", length: 255, nullable: true)]
    protected $dateOfBirth = null;

    public function __construct()
    {
        parent::__construct();
        $this->partnerRoles = new ArrayCollection();
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


    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    // Add a public getter method for the 'gender' property
    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public static function getGenderList()
    {
        return [
            self::GENDER_UNKNOWN => "u",
            self::GENDER_FEMALE => "f",
            self::GENDER_MALE => "m",
        ];
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function setBiography($biography)
    {
        $this->biography = $biography;

        return $this;
    }

    public function getBiography()
    {
        return $this->biography;
    }


    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setTwitterData($twitterData)
    {
        $this->twitterData = $twitterData;

        return $this;
    }

    public function getTwitterData()
    {
        return $this->twitterData;
    }

    public function setTwitterName($twitterName)
    {
        $this->twitterName = $twitterName;

        return $this;
    }

    public function getTwitterName()
    {
        return $this->twitterName;
    }

    public function setTwitterUid($twitterUid)
    {
        $this->twitterUid = $twitterUid;

        return $this;
    }

    public function getTwitterUid()
    {
        return $this->twitterUid;
    }

    public function getFacebookName()
    {
        return $this->facebookName;
    }

    public function setFacebookName($facebookName)
    {
        $this->facebookName = $facebookName;

        return $this;
    }

    public function getFacebookUid()
    {
        return $this->facebookUid;
    }

    public function setFacebookUid($facebookUid)
    {
        $this->facebookUid = $facebookUid;

        return $this;
    }

    public function setFacebookData($facebookData)
    {
        $this->facebookData = $facebookData;

        return $this;
    }

    public function getFacebookData()
    {
        return $this->facebookData;
    }

    public function setGplusUid($gplusUid)
    {
        $this->gplusUid = $gplusUid;

        return $this;
    }

    public function getGplusUid()
    {
        return $this->gplusUid;
    }

    public function setGplusName($gplusName)
    {
        $this->gplusName = $gplusName;

        return $this;
    }

    public function getGplusName()
    {
        return $this->gplusName;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }
    public function setTwoStepCode($twoStepCode)
    {
        $this->twoStepCode = $twoStepCode;

        return $this;
    }

    public function getTwoStepCode()
    {
        return $this->twoStepCode;
    }

    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }
}