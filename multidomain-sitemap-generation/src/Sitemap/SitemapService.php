<?php

namespace App\Service\Sitemap;

use App\Model\ProductCategoryQuery;
use App\Model\ProductQuery;
use App\Model\TagsQuery;
use Propel\Runtime\Collection\ArrayCollection;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Twig\Environment;

class SitemapService
{
    protected Environment $twig;
    protected string $sitemapDir;
    protected const CATEGORY_ROUTE = 'catalog';
    protected const TAG_ROUTE = 'tags';
    protected const PRODUCT_ROUTE = 'products';
    protected const CATALOG_ROUTE = 'catalog';
    protected const SITEMAP_ITEMS_LIMIT = 50000;

    /**
     * @param Environment $twig
     * @param $sitemapDir
     */
    public function __construct(Environment $twig, $sitemapDir)
    {
        $this->twig = $twig;
        $this->sitemapDir = $sitemapDir;
    }

    public function generate(string $scheme, string $host, ?string $slug)
    {
        $sitemapFactory = new SitemapFactory($this->twig, $this->sitemapDir);

        // main page
        $url = $scheme . '://';
        $url .= $slug ? $slug . '.' : '';
        $url .= $host;
        $sitemapFactory->createSection('main_page', [$url]);
        // categories
        $categorySlugs = ProductCategoryQuery::create()->select(['slug'])->find();
        $urls = $this->generateUrls($scheme, $host, $slug, self::CATEGORY_ROUTE, $categorySlugs);

        for ($i = 0; $i < ceil(count($urls) / self::SITEMAP_ITEMS_LIMIT); $i++) {
            $sitemapFactory->createSection(
                'categories' . $i,
                array_slice($urls, self::SITEMAP_ITEMS_LIMIT * $i, self::SITEMAP_ITEMS_LIMIT)
            );
        }
        // tags
        $tagsSlugs = TagsQuery::create()->select(['slug'])->find();
        $tagsAliases = TagsQuery::create()->select(['alias'])->find();
        $urls = $this->generateTagUrls($scheme, $host, $slug, self::TAG_ROUTE, $tagsSlugs, $tagsAliases);

        for ($i = 0; $i < ceil(count($urls) / self::SITEMAP_ITEMS_LIMIT); $i++) {
            $sitemapFactory->createSection(
                'tags' . $i,
                array_slice($urls, self::SITEMAP_ITEMS_LIMIT * $i, self::SITEMAP_ITEMS_LIMIT)
            );
        }
        // products
        $productSlugs = ProductQuery::create()->select(['slug'])->find();
        $urls = $this->generateUrls($scheme, $host, $slug, self::PRODUCT_ROUTE, $productSlugs);

        for ($i = 0; $i < ceil(count($urls) / self::SITEMAP_ITEMS_LIMIT); $i++) {
            $sitemapFactory->createSection(
                'products' . $i,
                array_slice($urls, self::SITEMAP_ITEMS_LIMIT * $i, self::SITEMAP_ITEMS_LIMIT)
            );
        }
        // save
        $sitemapFactory->createIndex();
        $sitemapFactory->saveToDisk($slug ?? 'default');
    }

    /**
     * @param string $scheme
     * @param string $host
     * @param string|null $citySlug
     * @param string $rout
     * @param Collection $slugs
     *
     * @return array
     */
    protected function generateUrls(string $scheme, string $host, ?string $citySlug, string $rout, Collection $slugs): array
    {
        $urls = [];
        foreach ($slugs as $slug) {
            $url = $scheme . '://';
            $url .= $citySlug? $citySlug . '.' : '';
            $url .= $host . '/' . $rout . '/' . $slug;

            $urls[] = $url;
        }

        return $urls;
    }

    protected function generateTagUrls(string $scheme, string $host, ?string $citySlug, string $rout, Collection $slugs, Collection $aliases): array
    {
        $urls = [];

        for ($i = 0; $i < count($aliases); $i++) {
            $slug = $aliases[$i];
            $category = $slugs[$i];

            $urls[] = $this->generateUrls($scheme, $host, $citySlug, self::CATALOG_ROUTE . '/' .$category . '/' . $rout, new Collection([$slug]))[0];
        }

        return $urls;
    }
}
