<?php

namespace App\Model;

use App\Entity\Partner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


interface PartneredInterface
{
    public function getPartner(): ?Partner;
    public function setPartner(?Partner $partner): self;
}
