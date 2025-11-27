<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Repositories\ProductCatalogueRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RouterRepository;


class CrawlerController extends FrontendController
{

    protected $productCatalogueRepository;
    protected $productRepository;
    protected $routerRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductRepository $productRepository,
        RouterRepository $routerRepository,
    ){
       $this->productCatalogueRepository = $productCatalogueRepository;
       $this->productRepository = $productRepository;
       $this->routerRepository = $routerRepository;
       
    }

    public function index(Request $request){
        // $url = 'http://127.0.0.1:9000/';
        // $content = json_decode(file_get_contents($url), true);
        // $payload = [];
        
        // $chunks = array_chunk($content, 100); // Split into chunks of 100 records
    
        // DB::beginTransaction();
        // try {
        //     foreach ($chunks as $chunk) {
        //         $payload = [];
        //         foreach ($chunk as $val) {
        //             $controllerName = match ($val['module']) {
        //                 'product_catalogues' => 'ProductCatalogueController',
        //                 'products' => 'ProductController',
        //                 'article_catalogues' => 'PostCatalogueController',
        //                 'articles' => 'PostController'
        //             };
                    
        //             $payload[] = [
        //                 'id' => $val['id'],
        //                 'canonical' => $val['canonical'],
        //                 'module_id' => $val['objectid'],
        //                 'language_id' => 1,
        //                 'controllers' => 'App\Http\Controllers\Frontend\\'.$controllerName.'',
                        
        //             ];
        //         }
        //         DB::table('routers')->insert($payload);
        //     }
        //     DB::commit();
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     throw $th;
        // }
        
    }

    

}
