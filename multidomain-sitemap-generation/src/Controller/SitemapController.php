<?php


use App\Model\FormResultQuery;
use App\Service\Form\Notifier\FormNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.{file}", methods={"GET"})
     */
    public function getSitemap(Request $request, string $file): BinaryFileResponse
    {
        $sitemapDir = $this->getParameter('sitemap_dir');
        $host = $request->getHost();
        $citySlug = explode('.', $host)[0];
        if ($host === $this->getParameter('router.request_context.host')) {
            $citySlug = 'default';
        }

        $response = new BinaryFileResponse($sitemapDir . $citySlug . '/sitemap.'. $file);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
