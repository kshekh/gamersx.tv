<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class SiteSettings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    private $disableHomeAccess;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $row_padding_top;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $row_padding_bottom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisableHomeAccess(): ?bool
    {
        return $this->disableHomeAccess;
    }

    public function setDisableHomeAccess(bool $disableHomeAccess): self
    {
        $this->disableHomeAccess = $disableHomeAccess;

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
