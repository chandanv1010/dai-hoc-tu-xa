<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TagService;
use App\Repositories\TagRepository;

class TagController extends Controller
{
    protected $tagService;
    protected $tagRepository;

    public function __construct(
        TagService $tagService,
        TagRepository $tagRepository
    ){
        $this->tagService = $tagService;
        $this->tagRepository = $tagRepository;
    }

    public function index(Request $request){
        $this->authorize('modules', 'tag.index');
        $tags = $this->tagService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
            ],
            'model' => 'Tag'
        ];
        $config['seo'] = 'Quản lý Tags';
        $template = 'backend.tag.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'tags'
        ));
    }

    public function create(){
        $this->authorize('modules', 'tag.create');
        $config = $this->configData();
        $config['seo'] = 'Quản lý Tags';
        $config['method'] = 'create';
        $template = 'backend.tag.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tag = $this->tagService->create($request);

        if ($tag) {
            return redirect()->route('tag.index')->with('success', 'Thêm mới tag thành công');
        }
        return redirect()->back()->with('error', 'Thêm mới tag không thành công. Hãy thử lại');
    }

    public function edit($id){
        $this->authorize('modules', 'tag.update');
        $tag = $this->tagRepository->findById($id);
        $config = $this->configData();
        $config['seo'] = 'Quản lý Tags';
        $config['method'] = 'edit';
        $template = 'backend.tag.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'tag',
        ));
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($this->tagService->update($id, $request)) {
            return redirect()->route('tag.index')->with('success', 'Cập nhật tag thành công');
        }

        return redirect()->back()->with('error', 'Cập nhật tag không thành công. Hãy thử lại');
    }

    public function delete($id){
        $this->authorize('modules', 'tag.destroy');
        $config['seo'] = 'Quản lý Tags';
        $tag = $this->tagRepository->findById($id);
        $template = 'backend.tag.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'tag',
            'config',
        ));
    }

    public function destroy($id){
        if($this->tagService->delete($id)){
            return redirect()->route('tag.index')->with('success','Xóa tag thành công');
        }
        return redirect()->route('tag.index')->with('error','Xóa tag không thành công. Hãy thử lại');
    }

    public function getAllTags(Request $request){
        $keyword = $request->input('keyword', '');
        $tags = $this->tagRepository->all();
        
        if ($keyword) {
            $tags = $tags->filter(function($tag) use ($keyword) {
                return stripos($tag->name, $keyword) !== false;
            });
        }
        
        return response()->json($tags->values());
    }

    private function configData(){
        return [
            'js' => [],
            'css' => []
        ];
    }
}
