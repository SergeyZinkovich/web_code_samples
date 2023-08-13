<?php

namespace App\DTO;

use App\Entity\Material;

class MaterialsListInfo
{
    /** @var Material[] $materials */
    protected array $newsArticlesList;
    protected int $currentPage;
    protected int $pagesCount;
    protected int $avgPrice;

    /**
     * @param Material[] $newsArticlesList
     * @param int        $currentPage
     * @param int        $pagesCount
     * @param int        $avgPrice
     */
    public function __construct(array $newsArticlesList, int $currentPage, int $pagesCount, int $avgPrice)
    {
        $this->newsArticlesList = $newsArticlesList;
        $this->currentPage      = $currentPage;
        $this->pagesCount       = $pagesCount;
        $this->avgPrice         = $avgPrice;
    }

    /**
     * @param array $newsArticlesList
     */
    public function setNewsArticlesList(array $newsArticlesList): void
    {
        $this->newsArticlesList = $newsArticlesList;
    }

    /**
     * @return array
     */
    public function getNewsArticlesList(): array
    {
        return $this->newsArticlesList;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $pagesCount
     */
    public function setPagesCount(int $pagesCount): void
    {
        $this->pagesCount = $pagesCount;
    }

    /**
     * @return int
     */
    public function getPagesCount(): int
    {
        return $this->pagesCount;
    }

    /**
     * @param int $avgPrice
     */
    public function setAvgPrice(int $avgPrice): void
    {
        $this->avgPrice = $avgPrice;
    }

    /**
     * @return int
     */
    public function getAvgPrice(): int
    {
        return $this->avgPrice;
    }
}