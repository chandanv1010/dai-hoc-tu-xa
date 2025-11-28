<?php

namespace App\Http\Controllers\Backend\Major;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\MajorCatalogueService;
use App\Repositories\MajorCatalogueRepository;
use App\Http\Requests\Major\StoreMajorCatalogueRequest;
use App\Http\Requests\Major\UpdateMajorCatalogueRequest;
use App\Http\Requests\Major\DeleteMajorCatalogueRequest;
use App\Models\Language;

class MajorCatalogueController extends Controller
{
    protected $majorCatalogueService;
    protected $majorCatalogueRepository;
    protected $language;

    public function __construct(
        MajorCatalogueService $majorCatalogueService,
        MajorCatalogueRepository $majorCatalogueRepository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });

        $this->majorCatalogueService = $majorCatalogueService;
        $this->majorCatalogueRepository = $majorCatalogueRepository;
    }

    public function index(Request $request){
        // $this->authorize('modules', 'major.catalogue.index'); // TODO: Tạo permission trong admin
        $majorCatalogues = $this->majorCatalogueService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/library/finder.js',
                'backend/js/plugins/switchery/switchery.js',
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
            ],
            'model' => 'MajorCatalogue',
        ];
        $config['seo'] = 'Danh mục Ngành học';
        $template = 'backend.major.catalogue.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'majorCatalogues'
        ));
    }

    public function create(){
        // $this->authorize('modules', 'major.catalogue.create'); // TODO: Tạo permission trong admin
        $config = $this->configData();
        $config['seo'] = 'Danh mục Ngành học';
        $config['method'] = 'create';
        $template = 'backend.major.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreMajorCatalogueRequest $request)
    {
        $record = $this->majorCatalogueService->create($request, $this->language);

        if ($record) {
            return redirect()->route('major.catalogue.edit', $record->id)->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->back()->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        // $this->authorize('modules', 'major.catalogue.update'); // TODO: Tạo permission trong admin
        $majorCatalogue = $this->majorCatalogueRepository->getMajorCatalogueById($id, $this->language);
        $config = $this->configData();
        $config['seo'] = 'Danh mục Ngành học';
        $config['method'] = 'edit';
        $template = 'backend.major.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'majorCatalogue',
        ));
    }

    public function update($id, UpdateMajorCatalogueRequest $request)
    {
        $queryString = base64_decode($request->getQueryString());

        if ($this->majorCatalogueService->update($id, $request, $this->language)) {
            if ($request->input('send') == 'send_and_stay') {
                return redirect()
                    ->route('major.catalogue.edit', [$id, 'query' => base64_encode($queryString)])
                    ->with('success', 'Cập nhật bản ghi thành công');
            }

            return redirect()
                ->route('major.catalogue.index', $queryString)
                ->with('success', 'Cập nhật bản ghi thành công');
        }

        return redirect()
            ->back()
            ->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        // $this->authorize('modules', 'major.catalogue.destroy'); // TODO: Tạo permission trong admin
        $config['seo'] = 'Danh mục Ngành học';
        $majorCatalogue = $this->majorCatalogueRepository->getMajorCatalogueById($id, $this->language);
        $template = 'backend.major.catalogue.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'majorCatalogue',
            'config',
        ));
    }

    public function destroy(DeleteMajorCatalogueRequest $request, $id){
        if($this->majorCatalogueService->destroy($id, $this->language)){
            return redirect()->route('major.catalogue.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('major.catalogue.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData(){
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
            ],
            'css' => []
        ];
    }
}

