<?php

namespace App\Entity;

use App\Shared\Entity\EntityIdTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="materials___materials")
 */
class Material
{
    use EntityIdTrait;

    /**
     * @ORM\ManyToOne(targetEntity=MaterialFile::class)
     * @ORM\JoinColumn(name="material_file_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected MaterialFile $materialFile;

    /**
     * @ORM\Column(name="nomenclature", type="string", length=32, nullable=false)
     */
    protected string $nomenclature;

    /**
     * @ORM\Column(name="is_with_vat", type="boolean", nullable=false)
     */
    protected bool $isWithVAT;

    /**
     * @ORM\ManyToOne(targetEntity=MaterialSeller::class, cascade={"persist"})
     * @ORM\JoinColumn(name="material_seller_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected MaterialSeller $seller;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, cascade={"persist"})
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected City $city;

    /**
     * @ORM\Column(name="address", type="string", length=128, nullable=false)
     */
    protected string $address;

    /**
     * @ORM\Column(name="phone", type="string", length=128, nullable=false)
     */
    protected string $phone;

    /**
     * @ORM\Column(name="website", type="string", length=128, nullable=false)
     */
    protected string $website;

    /**
     * @ORM\Column(name="relevant_on_date", type="datetime_immutable", nullable=false)
     */
    protected DateTimeImmutable $relevantOnDate;

    /**
     * @ORM\Column(name="price", type="float", nullable=false)
     */
    protected float $price;

    /**
     * @param MaterialFile $materialFile
     */
    public function setMaterialFile(MaterialFile $materialFile): void
    {
        $this->materialFile = $materialFile;
    }

    /**
     * @return MaterialFile
     */
    public function getMaterialFile(): MaterialFile
    {
        return $this->materialFile;
    }

    /**
     * @param string $nomenclature
     */
    public function setNomenclature(string $nomenclature): void
    {
        $this->nomenclature = $nomenclature;
    }

    /**
     * @return string
     */
    public function getNomenclature(): string
    {
        return $this->nomenclature;
    }

    /**
     * @param bool $isWithVAT
     */
    public function setIsWithVAT(bool $isWithVAT): void
    {
        $this->isWithVAT = $isWithVAT;
    }

    /**
     * @return bool
     */
    public function isWithVAT(): bool
    {
        return $this->isWithVAT;
    }

    /**
     * @param MaterialSeller $seller
     */
    public function setSeller(MaterialSeller $seller): void
    {
        $this->seller = $seller;
    }

    /**
     * @return MaterialSeller
     */
    public function getSeller(): MaterialSeller
    {
        return $this->seller;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city): void
    {
        $this->city = $city;
    }

    /**
     * @return City
     */
    public function getCity(): City
    {
        return $this->city;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $website
     */
    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getWebsite(): string
    {
        return $this->website;
    }

    /**
     * @param DateTimeImmutable $relevantOnDate
     */
    public function setRelevantOnDate(DateTimeImmutable $relevantOnDate): void
    {
        $this->relevantOnDate = $relevantOnDate;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getRelevantOnDate(): DateTimeImmutable
    {
        return $this->relevantOnDate;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
}
