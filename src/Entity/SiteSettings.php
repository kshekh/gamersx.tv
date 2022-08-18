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
}
