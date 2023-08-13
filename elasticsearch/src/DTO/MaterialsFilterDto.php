<?php

namespace App\DTO;

use App\Shared\Type\PaginationData;

class MaterialsFilterDto
{
    protected ?string $nomenclatureSearch = null;
    protected ?int $sellerId              = null;
    protected ?int $cityId                = null;
    protected ?bool $sortByPrice          = null;
    protected ?bool $isWithVat            = null;
    protected PaginationData $pagination;

    /**
     * @param string|null $nomenclatureSearch
     */
    public function setNomenclatureSearch(?string $nomenclatureSearch): void
    {
        $this->nomenclatureSearch = $nomenclatureSearch;
    }

    /**
     * @return string|null
     */
    public function getNomenclatureSearch(): ?string
    {
        return $this->nomenclatureSearch;
    }

    /**
     * @param PaginationData $pagination
     */
    public function setPagination(PaginationData $pagination): void
    {
        $this->pagination = $pagination;
    }

    /**
     * @return PaginationData
     */
    public function getPagination(): PaginationData
    {
        return $this->pagination;
    }

    /**
     * @param int|null $sellerId
     */
    public function setSellerId(?int $sellerId): void
    {
        $this->sellerId = $sellerId;
    }

    /**
     * @return int|null
     */
    public function getSellerId(): ?int
    {
        return $this->sellerId;
    }

    /**
     * @param int|null $cityId
     */
    public function setCityId(?int $cityId): void
    {
        $this->cityId = $cityId;
    }

    /**
     * @return int|null
     */
    public function getCityId(): ?int
    {
        return $this->cityId;
    }

    /**
     * @param bool|null $isWithVat
     */
    public function setIsWithVat(?bool $isWithVat): void
    {
        $this->isWithVat = $isWithVat;
    }

    /**
     * @return bool|null
     */
    public function getIsWithVat(): ?bool
    {
        return $this->isWithVat;
    }

    /**
     * @param bool|null $sortByPrice
     */
    public function setSortByPrice(?bool $sortByPrice): void
    {
        $this->sortByPrice = $sortByPrice;
    }

    /**
     * @return bool|null
     */
    public function getSortByPrice(): ?bool
    {
        return $this->sortByPrice;
    }
}