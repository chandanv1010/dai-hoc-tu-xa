<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\RouterRepository;

class RouterController extends FrontendController
{
    protected $language;
    protected $routerRepository;
    protected $router;

    public function __construct(
        RouterRepository $routerRepository,
    ) {
        $this->routerRepository = $routerRepository;
        parent::__construct();
    }


    public function index(string $canonical = '', Request $request)
    {
        $this->getRouter($canonical);
        if (!is_null($this->router) && !empty($this->router)) {
            $method = 'index';
            return app($this->router->controllers)->{$method}($this->router->module_id, $request);
        } else {
            // Return 404 view với layout chung
            $system = is_array($this->system) ? $this->system : [];
            $seo = [
                'meta_title' => '404 - Trang không tìm thấy | ' . ($system['homepage_company'] ?? 'Website'),
                'meta_description' => 'Trang bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.',
                'meta_keyword' => '404, trang không tìm thấy',
                'meta_image' => $system['homepage_logo'] ?? '',
                'canonical' => config('app.url') . '/404',
                'follow' => 0,
            ];
            $config = [
                'language' => $this->language,
                'js' => [],
                'css' => []
            ];
            return response(view('errors.404', compact('system', 'seo', 'config')), 404);
        }
    }

    public function page(string $canonical = '', $page = 1, Request $request)
    {
        $this->getRouter($canonical);
        $page = (!isset($page)) ? 1 : $page;
        if (!is_null($this->router) && !empty($this->router)) {
            $method = 'index';
            return app($this->router->controllers)->{$method}($this->router->module_id, $request, $page);
        } else {
            // Return 404 view với layout chung
            $system = is_array($this->system) ? $this->system : [];
            $seo = [
                'meta_title' => '404 - Trang không tìm thấy | ' . ($system['homepage_company'] ?? 'Website'),
                'meta_description' => 'Trang bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.',
                'meta_keyword' => '404, trang không tìm thấy',
                'meta_image' => $system['homepage_logo'] ?? '',
                'canonical' => config('app.url') . '/404',
                'follow' => 0,
            ];
            $config = [
                'language' => $this->language,
                'js' => [],
                'css' => []
            ];
            return response(view('errors.404', compact('system', 'seo', 'config')), 404);
        }
    }

    public function getRouter($canonical)
    {
        $this->router = $this->routerRepository->findByCondition(
            [
                ['canonical', '=', $canonical],
                ['language_id', '=', $this->language]
            ]
        );
    }



}