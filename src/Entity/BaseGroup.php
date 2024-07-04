<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Model\Group as AbstractedGroup;

/**
 * @ORM\MappedSuperclass
 */
class BaseGroup extends AbstractedGroup
{
    /**
     * Represents a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }
}
