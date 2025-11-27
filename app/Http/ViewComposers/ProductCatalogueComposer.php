<?php  
namespace App\Http\ViewComposers;

use Illuminate\View\View;
// use App\Services\WidgetService;
use App\Repositories\ProductCatalogueRepository;

class ProductCatalogueComposer
{

    // protected $widgetService;
    protected $productCatalogueRepository;

    public function __construct(
        // WidgetService $widgetService,
        ProductCatalogueRepository $productCatalogueRepository,
    ){
    //    $this->widgetService = $widgetService;
       $this->productCatalogueRepository = $productCatalogueRepository;
    }

    public function compose(View $view)
    {
        
        $categories = $this->productCatalogueRepository->all();



        $view->with('categories', $categories);
    }

   

}