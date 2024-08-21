<?php

namespace App\Entity;

use App\Repository\ErrorLogRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ErrorLogRepository::class)]
class ErrorLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $error_message = null;

    #[ORM\Column(length: 255)]
    private ?string $error_type = null;

    #[ORM\Column]
    private int $status_code;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $container_id = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getErrorMessage(): ?string
    {
        return $this->error_message;
    }

    public function setErrorMessage(?string $error_message): self
    {
        $this->error_message = $error_message;

        return $this;
    }

    public function getErrorType(): ?string
    {
        return $this->error_type;
    }

    public function setErrorType(string $error_type): self
    {
        $this->error_type = $error_type;

        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->status_code;
    }

    public function setStatusCode(int $status_code): self
    {
        $this->status_code = $status_code;

        return $this;
    }

    public function getContainerId(): ?string
    {
        return $this->container_id;
    }

    public function setContainerId(?string $container_id): self
    {
        $this->container_id = $container_id;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}