<?php

namespace App\Entity;

use App\Entity\Media\Media;
use App\Entity\MaterialQuarter;
use App\Shared\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="materials___file")
 */
class MaterialFile
{
    use EntityIdTrait;

    /**
     * @ORM\OneToOne(targetEntity=Media::class, orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Media $file;

    /**
     * @ORM\ManyToOne(targetEntity=MaterialQuarter::class, inversedBy="materialFiles")
     * @ORM\JoinColumn(name="material_quarter_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected MaterialQuarter $materialQuarter;

    /**
     * @ORM\Column(name="is_enabled", type="boolean", nullable=false, options={"default" = true})
     */
    protected bool $isEnabled = true;

    /**
     * @ORM\Column(name="is_parsed", type="boolean", nullable=false, options={"default" = false})
     */
    protected bool $isParsed = false;

    /**
     * @param MaterialQuarter $materialQuarter
     */
    public function setMaterialQuarter(MaterialQuarter $materialQuarter): void
    {
        $this->materialQuarter = $materialQuarter;
    }

    /**
     * @return MaterialQuarter
     */
    public function getMaterialQuarter(): MaterialQuarter
    {
        return $this->materialQuarter;
    }

    /**
     * @param bool $isEnabled
     */
    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @param Media $file
     */
    public function setFile(Media $file): void
    {
        $this->file = $file;
    }

    /**
     * @return Media
     */
    public function getFile(): Media
    {
        return $this->file;
    }

    /**
     * @param bool $isParsed
     */
    public function setIsParsed(bool $isParsed): void
    {
        $this->isParsed = $isParsed;
    }

    /**
     * @return bool
     */
    public function isParsed(): bool
    {
        return $this->isParsed;
    }
}
