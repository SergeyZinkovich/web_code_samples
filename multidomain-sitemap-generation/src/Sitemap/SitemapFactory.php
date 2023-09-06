<?php

namespace App\Service\Sitemap;

use Twig\Environment;

class SitemapFactory
{
    protected array $sectionNames = [];

    protected array $sections = [];

    protected string $index;

    protected Environment $twig;

    protected string $sitemapDir;

    /**
     * @param Environment $twig
     * @param string $sitemapDir
     */
    public function __construct(Environment $twig, string $sitemapDir)
    {
        $this->twig = $twig;
        $this->sitemapDir = $sitemapDir;
    }

    public function createSection(string $sectionName, array $urls): string
    {
        $sectionContent = $this->twig->render('sitemap/sitemap.section.xml.twig', [
            'urls' => $urls,
        ]);

        $this->sectionNames[] = $sectionName;
        $this->sections[] = $sectionContent;

        return $sectionContent;
    }

    public function createIndex(): string
    {
        $sectionFiles = [];
        foreach ($this->sectionNames as $sectionName) {
            $sectionFiles[] = 'sitemap.' . $sectionName . '.xml';
        }

        $indexContent = $this->twig->render('sitemap/sitemap.xml.twig', [
            'sections' => $sectionFiles,
        ]);

        $this->index = $indexContent;

        return $indexContent;
    }

    public function saveToDisk(string $folder)
    {
        for ($i = 0; $i < sizeof($this->sections); $i++) {
            $this->saveXml($folder, 'sitemap.' . $this->sectionNames[$i], $this->sections[$i]);
        }

        $this->saveXml($folder, 'sitemap', $this->index);
    }

    protected function saveXml(string $folder, string $fileName, string $content)
    {
        $dir = $this->sitemapDir . $folder . '/';
        if (!is_dir($dir)) {
            mkdir($dir,0777, true);
        }

        file_put_contents($dir . $fileName . '.xml', $content);
    }
}
