<?php

namespace App\Entity;

use App\Shared\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="materials___seller")
 */
class MaterialSeller
{
    use EntityIdTrait;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected string $name;

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
