<?php

namespace App\Http\Controllers\Backend\Major;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MajorService;
use App\Repositories\MajorRepository;
use App\Repositories\PostRepository;
use App\Repositories\MajorCatalogueRepository;
use App\Repositories\PostCatalogueRepository;
use App\Http\Requests\Major\StoreMajorRequest;
use App\Http\Requests\Major\UpdateMajorRequest;
use App\Models\Language;
use Illuminate\Support\Facades\App;

class MajorController extends Controller
{
    protected $majorService;
    protected $majorRepository;
    protected $postRepository;
    protected $majorCatalogueRepository;
    protected $postCatalogueRepository;
    protected $language;

    public function __construct(
        MajorService $majorService,
        MajorRepository $majorRepository,
        PostRepository $postRepository,
        MajorCatalogueRepository $majorCatalogueRepository,
        PostCatalogueRepository $postCatalogueRepository
    ) {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });

        $this->majorService = $majorService;
        $this->majorRepository = $majorRepository;
        $this->postRepository = $postRepository;
        $this->majorCatalogueRepository = $majorCatalogueRepository;
        $this->postCatalogueRepository = $postCatalogueRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'major.index');
        $majors = $this->majorService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Major'
        ];
        $config['seo'] = 'Quản lý Ngành học';
        $template = 'backend.major.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'majors'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'major.create');
        $config = $this->configData();
        $config['seo'] = 'Quản lý Ngành học';
        $config['method'] = 'create';
        $posts = $this->postRepository->getAllByLanguage($this->language);
        $majorCatalogues = $this->majorCatalogueRepository->getAllMajorCatalogues($this->language);
        $postCatalogues = $this->postCatalogueRepository->getAllByLanguage($this->language);
        $template = 'backend.major.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'posts',
            'majorCatalogues',
            'postCatalogues',
        ));
    }

    public function store(StoreMajorRequest $request)
    {
        $record = $this->majorService->create($request, $this->language);

        if ($record) {
            return redirect()->route('major.edit', $record->id)->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->back()->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'major.update');
        $major = $this->majorRepository->getMajorById($id, $this->language);
        
        if (!$major) {
            return redirect()->route('major.index')->with('error', 'Không tìm thấy bản ghi');
        }
        
        // Dữ liệu JSON đã được decode trong repository
        $config = $this->configData();
        $config['seo'] = 'Quản lý Ngành học';
        $config['method'] = 'edit';
        $posts = $this->postRepository->getAllByLanguage($this->language);
        $majorCatalogues = $this->majorCatalogueRepository->getAllMajorCatalogues($this->language);
        $postCatalogues = $this->postCatalogueRepository->getAllByLanguage($this->language);

        $template = 'backend.major.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'major',
            'posts',
            'majorCatalogues',
            'postCatalogues',
        ));
    }

    public function update($id, UpdateMajorRequest $request)
    {
        $queryString = base64_decode($request->getQueryString());

        if ($this->majorService->update($id, $request, $this->language)) {
            if ($request->input('send') == 'send_and_stay') {
                return redirect()
                    ->route('major.edit', [$id, 'query' => base64_encode($queryString)])
                    ->with('success', 'Cập nhật bản ghi thành công');
            }

            return redirect()
                ->route('major.index', $queryString)
                ->with('success', 'Cập nhật bản ghi thành công');
        }

        return redirect()
            ->back()
            ->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'major.destroy');
        $config['seo'] = 'Quản lý Ngành học';
        $major = $this->majorRepository->getMajorById($id, $this->language);
        $template = 'backend.major.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'major',
            'config',
        ));
    }

    public function duplicate($id)
    {
        $this->authorize('modules', 'major.create');
        $duplicatedMajor = $this->majorService->duplicate($id, $this->language);
        
        if ($duplicatedMajor) {
            return redirect()->route('major.edit', $duplicatedMajor->id)->with('success', 'Nhân bản bản ghi thành công');
        }
        return redirect()->route('major.index')->with('error', 'Nhân bản bản ghi không thành công. Hãy thử lại');
    }

    public function destroy($id)
    {
        if ($this->majorService->destroy($id)) {
            return redirect()->route('major.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('major.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData()
    {
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/js/plugins/switchery/switchery.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/css/plugins/switchery/switchery.css'
            ]
        ];
    }
}
