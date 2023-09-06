<?php


namespace App\Command;

use App\Service\Sitemap\SitemapService;
use Creonit\GeoIpBundle\Model\GeoipCityQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SitemapPopulateCommand extends Command
{
    protected static $defaultName = 'app:sitemap:populate';

    private ParameterBagInterface $params;
    private SitemapService $sitemapService;

    public function __construct(string $name = null, ParameterBagInterface $params, SitemapService $sitemapService)
    {
        parent::__construct($name);

        $this->params = $params;
        $this->sitemapService = $sitemapService;
    }

    protected function configure()
    {
        $this->setDescription('Generates sitemaps for all subdomains and base domain');
    }

    /**
     * @throws PropelException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $scheme = $this->params->get('router.request_context.scheme');
        $host = $this->params->get('router.request_context.host');
        $slugs = GeoipCityQuery::create()->select(['slug'])->filterBySlug('', Criteria::NOT_EQUAL)->find();

        $this->sitemapService->generate($scheme, $host, null);

        foreach ($slugs as $slug) {
            $this->sitemapService->generate($scheme, $host, $slug);
        }

        return Command::SUCCESS;
    }
}
