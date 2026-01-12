<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\Tag;
use Illuminate\Http\Response;
use Carbon\Carbon;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml dynamically from routers table
     *
     * @return Response
     */
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $now = Carbon::now()->format('Y-m-d');
        
        // Start XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Static URLs
        $staticUrls = [
            [
                'loc' => $baseUrl . '/',
                'changefreq' => 'daily',
                'priority' => '1.00'
            ],
            [
                'loc' => $baseUrl . '/lien-he.html',
                'changefreq' => 'monthly',
                'priority' => '0.60'
            ],
            [
                'loc' => $baseUrl . '/cac-truong-dao-tao-tu-xa.html',
                'changefreq' => 'weekly',
                'priority' => '0.80'
            ],
            [
                'loc' => $baseUrl . '/cac-nganh-dao-tao-tu-xa.html',
                'changefreq' => 'weekly',
                'priority' => '0.80'
            ],
        ];
        
        // Add static URLs to XML
        foreach ($staticUrls as $url) {
            $xml .= $this->buildUrlEntry(
                $url['loc'],
                $now,
                $url['changefreq'],
                $url['priority']
            );
        }
        
        // Controllers to exclude from sitemap
        $excludedControllers = [
            'App\Http\Controllers\Frontend\CartController',
            'App\Http\Controllers\Frontend\AuthController',
            'App\Http\Controllers\Frontend\CustomerController',
        ];
        
        // Canonicals to exclude from sitemap
        $excludedCanonicals = [
            'gio-hang',
            'thanh-toan',
            'cart',
            'customer',
            'danh-sach-yeu-thich',
            'tim-kiem',
        ];
        
        // Get all routers
        $routers = Router::whereNotIn('controllers', $excludedControllers)
            ->orderBy('updated_at', 'desc')
            ->get();
        
        foreach ($routers as $router) {
            $canonical = $router->canonical;
            
            // Skip excluded canonicals
            $shouldExclude = false;
            foreach ($excludedCanonicals as $excluded) {
                if (str_starts_with($canonical, $excluded)) {
                    $shouldExclude = true;
                    break;
                }
            }
            
            if ($shouldExclude) {
                continue;
            }
            
            // Determine priority based on controller type
            $priority = $this->determinePriority($router->controllers);
            
            // Determine changefreq based on controller type
            $changefreq = $this->determineChangefreq($router->controllers);
            
            // Build URL
            $loc = $baseUrl . '/' . $canonical . '.html';
            $lastmod = $router->updated_at 
                ? Carbon::parse($router->updated_at)->format('Y-m-d')
                : $now;
            
            $xml .= $this->buildUrlEntry($loc, $lastmod, $changefreq, $priority);
        }
        
        // Add Tags URLs
        $tags = Tag::whereNull('deleted_at')->orderBy('updated_at', 'desc')->get();
        foreach ($tags as $tag) {
            $loc = $baseUrl . '/tag/' . $tag->slug . '.html';
            $lastmod = $tag->updated_at 
                ? Carbon::parse($tag->updated_at)->format('Y-m-d')
                : $now;
            $xml .= $this->buildUrlEntry($loc, $lastmod, 'weekly', '0.60');
        }
        
        // Close XML
        $xml .= '</urlset>';
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
    
    /**
     * Build a single URL entry for sitemap
     */
    private function buildUrlEntry(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        $entry = "  <url>\n";
        $entry .= "    <loc>" . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n";
        $entry .= "    <lastmod>{$lastmod}</lastmod>\n";
        $entry .= "    <changefreq>{$changefreq}</changefreq>\n";
        $entry .= "    <priority>{$priority}</priority>\n";
        $entry .= "  </url>\n";
        
        return $entry;
    }
    
    /**
     * Determine priority based on controller type
     */
    private function determinePriority(string $controller): string
    {
        return match (true) {
            str_contains($controller, 'SchoolController') => '0.90',
            str_contains($controller, 'MajorController') => '0.85',
            str_contains($controller, 'ProductCatalogueController') => '0.80',
            str_contains($controller, 'ProductController') => '0.75',
            str_contains($controller, 'PostCatalogueController') => '0.70',
            str_contains($controller, 'PostController') => '0.65',
            default => '0.60',
        };
    }
    
    /**
     * Determine changefreq based on controller type
     */
    private function determineChangefreq(string $controller): string
    {
        return match (true) {
            str_contains($controller, 'SchoolController') => 'weekly',
            str_contains($controller, 'MajorController') => 'weekly',
            str_contains($controller, 'ProductCatalogueController') => 'weekly',
            str_contains($controller, 'ProductController') => 'weekly',
            str_contains($controller, 'PostController') => 'monthly',
            default => 'monthly',
        };
    }
}
