<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\MajorCatalogueRepository;
use App\Repositories\MajorRepository;
use App\Services\WidgetService;
use App\Services\SlideService;
use App\Enums\SlideEnum;
use App\Repositories\SystemRepository;

class MajorCatalogueController extends FrontendController
{
    protected $language;
    protected $system;
    protected $majorCatalogueRepository;
    protected $majorRepository;
    protected $widgetService;
    protected $slideService;

    public function __construct(
        MajorCatalogueRepository $majorCatalogueRepository,
        MajorRepository $majorRepository,
        WidgetService $widgetService,
        SlideService $slideService,
        SystemRepository $systemRepository,
    ) {
        $this->majorCatalogueRepository = $majorCatalogueRepository;
        $this->majorRepository = $majorRepository;
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
        parent::__construct($systemRepository);
    }

    public function index($id, $request, $page = 1)
    {
        $majorCatalogue = $this->majorCatalogueRepository->getMajorCatalogueById($id, $this->language);
        
        if (!$majorCatalogue) {
            abort(404);
        }

        // Lấy danh sách majors theo catalogue
        $majors = $this->majorRepository->getMajorsByCatalogue($id, $this->language, $page);

        // Lấy tất cả major catalogues để hiển thị filter tabs
        $majorCatalogues = $this->majorCatalogueRepository->getAllMajorCatalogues($this->language);

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'majors-list'],
        ], $this->language);

        $slides = $this->slideService->getSlide(
            [SlideEnum::MAIN],
            $this->language
        );

        $template = 'frontend.major.catalogue.index';

        $config = $this->config();
        $system = $this->system;
        $seo = seo($majorCatalogue, $page);

        return view($template, compact(
            'config',
            'seo',
            'system',
            'majorCatalogue',
            'majorCatalogues',
            'majors',
            'widgets',
            'slides',
            'page'
        ));
    }

    private function config()
    {
        return [
            'language' => $this->language,
        ];
    }
}

