<?php

namespace App\Service;

use App\DTO\MaterialsFilterDto;
use App\DTO\MaterialsListInfo;
use App\Entity\Material;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Elastica\Aggregation\Avg;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class MaterialService
{
    protected const ELASTICSEARCH_ITEMS_LIMIT = 10000;

    protected PaginatedFinderInterface $finder;
    protected EntityManagerInterface $em;

    /**
     * @param PaginatedFinderInterface $finder
     * @param EntityManagerInterface   $em
     */
    public function __construct(PaginatedFinderInterface $finder, EntityManagerInterface $em)
    {
        $this->finder = $finder;
        $this->em     = $em;
    }

    /**
     * @param MaterialsFilterDto $materialsFilterDto
     *
     * @return MaterialsListInfo
     */
    public function searchMaterials(MaterialsFilterDto $materialsFilterDto): MaterialsListInfo
    {
        if ($materialsFilterDto->getNomenclatureSearch() !== null) {
            return $this->getMaterialsWithoutSearch($materialsFilterDto);
        }

        $query = $this->createElasticQuery($materialsFilterDto);

        $limit  = $materialsFilterDto->getPagination()->getLimit();
        $offset = $materialsFilterDto->getPagination()->getOffset();

        $paginatorAdapter = $this->finder->createPaginatorAdapter($query);
        $totalCount       = min($paginatorAdapter->getTotalHits(), self::ELASTICSEARCH_ITEMS_LIMIT);
        $queryResult      = $paginatorAdapter->getResults($offset, $limit);

        $pagesCount  = intval(ceil($totalCount / $limit));
        $currentPage = $pagesCount !== 0 ? intval(floor($offset / $limit) + 1) : 0;

        return new MaterialsListInfo(
            $queryResult->toArray(),
            $currentPage,
            $pagesCount,
        $queryResult->getAggregations()['avg_price']['value'] ?? 0,
        );
    }

    /**
     * @param MaterialsFilterDto $materialsFilterDto
     *
     * @return Material[]
     */
    public function searchMaterialsWithoutPagination(MaterialsFilterDto $materialsFilterDto): array
    {
        $query = $this->createElasticQuery($materialsFilterDto);

        return $this->finder->find($query, self::ELASTICSEARCH_ITEMS_LIMIT);
    }

    /**
     * @param MaterialsFilterDto $materialsFilterDto
     *
     * @return Query
     */
    protected function createElasticQuery(MaterialsFilterDto $materialsFilterDto): Query
    {
        $query = new BoolQuery();

        $search = $materialsFilterDto->getNomenclatureSearch();
        if ($search !== null) {
            $q = new MatchQuery();
            $q->setFieldQuery('nomenclature', $search);
            $q->setFieldMinimumShouldMatch('nomenclature', '-5');
            $query->addMust($q);
        }

        $cityId = $materialsFilterDto->getCityId();
        if ($cityId !== null) {
            $term = new Term(['city.id' => $cityId]);
            $query->addMust($term);
        }

        $sellerId = $materialsFilterDto->getSellerId();
        if ($sellerId !== null) {
            $term = new Term(['seller.id' => $sellerId]);
            $query->addMust($term);
        }

        $isWithVat = $materialsFilterDto->getIsWithVat();
        if ($isWithVat !== null) {
            $term = new Term(['is_with_vat' => $isWithVat]);
            $query->addMust($term);
        }

        $parentQuery = new Query();
        $parentQuery->setQuery($query);
        $avg = new Avg('avg_price');
        $avg->setField('price');
        $parentQuery->addAggregation($avg);

        $sortByPrice = $materialsFilterDto->getSortByPrice();
        if ($sortByPrice !== null) {
            $parentQuery->setSort(['price' => $sortByPrice ? 'asc' : 'desc']);
        }

        return $parentQuery;
    }

    /**
     * @param MaterialsFilterDto $materialsFilterDto
     *
     * @return MaterialsListInfo
     */
    protected function getMaterialsWithoutSearch(MaterialsFilterDto $materialsFilterDto): MaterialsListInfo
    {
        $limit  = $materialsFilterDto->getPagination()->getLimit();
        $offset = $materialsFilterDto->getPagination()->getOffset();

        $materials = $this
            ->createDoctrineQuery($materialsFilterDto)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->execute()
        ;
        $aggregations = $this
            ->createDoctrineQuery($materialsFilterDto)
            ->select('avg(m.price), count(m.id)')
            ->getQuery()
            ->execute()[0]
        ;
        $avgPrice   = $aggregations[1];
        $totalCount = $aggregations[2];

        $pagesCount  = intval(ceil($totalCount / $limit));
        $currentPage = $pagesCount !== 0 ? intval(floor($offset / $limit) + 1) : 0;

        return new MaterialsListInfo(
            $materials,
            $currentPage,
            $pagesCount,
            $avgPrice ?? 0,
        );
    }

    /**
     * @param MaterialsFilterDto $materialsFilterDto
     *
     * @return QueryBuilder
     */
    protected function createDoctrineQuery(MaterialsFilterDto $materialsFilterDto): QueryBuilder
    {
        $query = $this->em->getRepository(Material::class)
            ->createQueryBuilder('m')
            ->innerJoin('m.city', 'c')
            ->innerJoin('m.seller', 's')
            ->orderBy('m.price', $materialsFilterDto->getSortByPrice() ? 'asc' : 'desc')
        ;

        if ($materialsFilterDto->getCityId() !== null) {
            $query
                ->andWhere('c.id = :cityId')
                ->setParameter('cityId', $materialsFilterDto->getCityId())
            ;
        }

        if ($materialsFilterDto->getSellerId() !== null) {
            $query
                ->andWhere('s.id = :sellerId')
                ->setParameter('sellerId', $materialsFilterDto->getSellerId())
            ;
        }

        if ($materialsFilterDto->getIsWithVat() !== null) {
            $query
                ->andWhere('m.isWithVAT = :isWithVAT')
                ->setParameter('isWithVAT', $materialsFilterDto->getIsWithVat())
            ;
        }

        return $query;
    }
}