<?php

namespace App\Entity;

use App\Repository\ErrorLogRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: ErrorLogRepository::class)]
class ErrorLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $error_message = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $error_type = null;

    #[ORM\Column(type: "integer")]
    private int $status_code;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $container_id = null;

    #[ORM\Column(type: "datetime")]
    private DateTimeInterface $created_at;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}