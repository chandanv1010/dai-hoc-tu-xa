<?php

namespace App\Http\Controllers\Backend\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SchoolService;
use App\Repositories\SchoolRepository;
use App\Repositories\MajorRepository;
use App\Repositories\PostRepository;
use App\Repositories\PostCatalogueRepository;
use App\Http\Requests\School\StoreSchoolRequest;
use App\Http\Requests\School\UpdateSchoolRequest;
use App\Models\Language;
use Illuminate\Support\Facades\App;

class SchoolController extends Controller
{
    protected $schoolService;
    protected $schoolRepository;
    protected $majorRepository;
    protected $postRepository;
    protected $postCatalogueRepository;
    protected $language;

    public function __construct(
        SchoolService $schoolService,
        SchoolRepository $schoolRepository,
        MajorRepository $majorRepository,
        PostRepository $postRepository,
        PostCatalogueRepository $postCatalogueRepository
    ) {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });

        $this->schoolService = $schoolService;
        $this->schoolRepository = $schoolRepository;
        $this->majorRepository = $majorRepository;
        $this->postRepository = $postRepository;
        $this->postCatalogueRepository = $postCatalogueRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'school.index');
        $schools = $this->schoolService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'School'
        ];
        $config['seo'] = 'Quản lý Trường học';
        $template = 'backend.school.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'schools'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'school.create');
        $config = $this->configData();
        $config['seo'] = 'Quản lý Trường học';
        $config['method'] = 'create';
        $majorsList = $this->majorRepository->getAllByLanguage($this->language);
        $postCatalogues = $this->postCatalogueRepository->getAllByLanguage($this->language);
        $template = 'backend.school.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'majorsList',
            'postCatalogues',
        ));
    }

    public function store(StoreSchoolRequest $request)
    {
        $record = $this->schoolService->create($request, $this->language);

        if ($record) {
            return redirect()->route('school.edit', $record->id)->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->back()->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'school.update');
        $school = $this->schoolRepository->getSchoolById($id, $this->language);
        
        if (!$school) {
            return redirect()->route('school.index')->with('error', 'Không tìm thấy bản ghi');
        }
        
        $config = $this->configData();
        $config['seo'] = 'Quản lý Trường học';
        $config['method'] = 'edit';
        $majorsList = $this->majorRepository->getAllByLanguage($this->language);
        $postCatalogues = $this->postCatalogueRepository->getAllByLanguage($this->language);
        $template = 'backend.school.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'school',
            'majorsList',
            'postCatalogues',
        ));
    }

    public function update($id, UpdateSchoolRequest $request)
    {
        $queryString = base64_decode($request->getQueryString());

        if ($this->schoolService->update($id, $request, $this->language)) {
            if ($request->input('send') == 'send_and_stay') {
                return redirect()
                    ->route('school.edit', [$id, 'query' => base64_encode($queryString)])
                    ->with('success', 'Cập nhật bản ghi thành công');
            }

            return redirect()
                ->route('school.index', $queryString)
                ->with('success', 'Cập nhật bản ghi thành công');
        }

        return redirect()
            ->back()
            ->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'school.destroy');
        $config['seo'] = 'Quản lý Trường học';
        $school = $this->schoolRepository->getSchoolById($id, $this->language);
        $template = 'backend.school.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'school',
            'config',
        ));
    }

    public function duplicate($id)
    {
        $this->authorize('modules', 'school.create');
        $duplicatedSchool = $this->schoolService->duplicate($id, $this->language);
        
        if ($duplicatedSchool) {
            return redirect()->route('school.edit', $duplicatedSchool->id)->with('success', 'Nhân bản bản ghi thành công');
        }
        return redirect()->route('school.index')->with('error', 'Nhân bản bản ghi không thành công. Hãy thử lại');
    }

    public function destroy($id)
    {
        if ($this->schoolService->destroy($id)) {
            return redirect()->route('school.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('school.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData()
    {
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
    }
}
