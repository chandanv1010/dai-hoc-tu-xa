@include('backend.dashboard.component.breadcrumb', ['title' => ($config['method'] == 'create') ? 'Thêm mới Trường học' : 'Cập nhật Trường học'])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('school.store') : route('school.update', $school->id ?? '');
    // Load data from JSON fields in school_language pivot
    $pivot = isset($school) && $school->languages && count($school->languages) > 0 ? $school->languages[0]->pivot : null;
    
    // Lấy các trường text từ pivot
    $schoolName = $pivot->name ?? ($school->name ?? '');
    $schoolDescription = $pivot->description ?? ($school->description ?? '');
    $schoolContent = $pivot->content ?? ($school->content ?? '');
    $canonical = $pivot->canonical ?? '';
    $metaTitle = $pivot->meta_title ?? '';
    $metaKeyword = $pivot->meta_keyword ?? '';
    $metaDescription = $pivot->meta_description ?? '';
    
    // Đảm bảo dữ liệu JSON là array
    $intro = ($pivot && isset($pivot->intro)) ? (is_array($pivot->intro) ? $pivot->intro : []) : [];
    $announce = ($pivot && isset($pivot->announce)) ? (is_array($pivot->announce) ? $pivot->announce : []) : [];
    $advantage = ($pivot && isset($pivot->advantage)) ? (is_array($pivot->advantage) ? $pivot->advantage : []) : [];
    $suitable = ($pivot && isset($pivot->suitable)) ? (is_array($pivot->suitable) ? $pivot->suitable : []) : [];
    $majors = ($pivot && isset($pivot->majors)) ? (is_array($pivot->majors) ? $pivot->majors : []) : [];
    $studyMethod = ($pivot && isset($pivot->study_method)) ? (is_array($pivot->study_method) ? $pivot->study_method : []) : [];
    $feedback = ($pivot && isset($pivot->feedback)) ? (is_array($pivot->feedback) ? $pivot->feedback : []) : [];
    // Event giờ lưu post_catalogue_id (single value) thay vì array post_ids
    $eventPostCatalogueId = ($pivot && isset($pivot->event)) ? (is_array($pivot->event) ? ($pivot->event['post_catalogue_id'] ?? null) : (is_string($pivot->event) ? json_decode($pivot->event, true)['post_catalogue_id'] ?? null : null)) : null;
    $value = ($pivot && isset($pivot->value)) ? (is_array($pivot->value) ? $pivot->value : []) : [];
    
@endphp
<form action="{{ $url }}" method="post" class="box" id="schoolForm">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <!-- Thông tin cơ bản -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin cơ bản</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên trường<span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="name"
                                        value="{{ old('name', $schoolName) }}"
                                        class="form-control change-title"
                                        data-flag = "{{ (!empty($schoolName)) ? 1 : 0 }}"
                                        placeholder="Nhập tên trường"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Số chỉ tiêu tuyển sinh</label>
                                    <input 
                                        type="text"
                                        name="enrollment_quota"
                                        value="{{ old('enrollment_quota', ($school->enrollment_quota ?? '') ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập số chỉ tiêu (VD: 19)"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ảnh giới thiệu</label>
                                    <input type="text" name="intro_image" class="form-control upload-image" value="{{ old('intro_image', ($school->intro_image ?? '') ?? '') }}" placeholder="Chọn ảnh giới thiệu" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <textarea 
                                        name="description" 
                                        class="form-control"
                                        rows="4"
                                        placeholder="Nhập mô tả">{{ old('description', $schoolDescription) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb30">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Nội dung</label>
                                    <textarea 
                                        name="content" 
                                        class="ck-editor" 
                                        id="ckContent"
                                        data-height="300">{{ old('content', $schoolContent) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Khối 1: Statistics -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Số liệu thống kê</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_statistics" value="{{ old('is_show_statistics', ($school->is_show_statistics ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_statistics', ($school->is_show_statistics ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td style="width: 150px;"><strong>Số ngành học</strong></td>
                                            <td>
                                                <input 
                                                    type="number"
                                                    name="statistics_majors"
                                                    value="{{ old('statistics_majors', ($school->statistics_majors ?? '') ?? '') }}"
                                                    class="form-control"
                                                    placeholder="Nhập số ngành học"
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Số học viên</strong></td>
                                            <td>
                                                <input 
                                                    type="number"
                                                    name="statistics_students"
                                                    value="{{ old('statistics_students', ($school->statistics_students ?? '') ?? '') }}"
                                                    class="form-control"
                                                    placeholder="Nhập số học viên"
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Số khóa khai giảng</strong></td>
                                            <td>
                                                <input 
                                                    type="number"
                                                    name="statistics_courses"
                                                    value="{{ old('statistics_courses', ($school->statistics_courses ?? '') ?? '') }}"
                                                    class="form-control"
                                                    placeholder="Nhập số khóa khai giảng"
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td style="width: 150px;"><strong>Tỷ lệ hài lòng (%)</strong></td>
                                            <td>
                                                <input 
                                                    type="number"
                                                    name="statistics_satisfaction"
                                                    value="{{ old('statistics_satisfaction', ($school->statistics_satisfaction ?? '') ?? '') }}"
                                                    class="form-control"
                                                    placeholder="Nhập tỷ lệ hài lòng"
                                                    min="0"
                                                    max="100"
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tỷ lệ có việc làm (%)</strong></td>
                                            <td>
                                                <input 
                                                    type="number"
                                                    name="statistics_employment"
                                                    value="{{ old('statistics_employment', ($school->statistics_employment ?? '') ?? '') }}"
                                                    class="form-control"
                                                    placeholder="Nhập tỷ lệ có việc làm"
                                                    min="0"
                                                    max="100"
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Khối 2: Intro -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Giới thiệu</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_intro" value="{{ old('is_show_intro', ($school->is_show_intro ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_intro', ($school->is_show_intro ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên</label>
                                    <input 
                                        type="text"
                                        name="intro[name]"
                                        value="{{ old('intro.name', (isset($intro) && isset($intro['name'])) ? $intro['name'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <input 
                                        type="text"
                                        name="intro[description]"
                                        value="{{ old('intro.description', (isset($intro) && isset($intro['description'])) ? $intro['description'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập mô tả"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ngày thành lập</label>
                                    <input 
                                        type="text"
                                        name="intro[created]"
                                        value="{{ old('intro.created', (isset($intro) && isset($intro['created'])) ? $intro['created'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập ngày thành lập"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Top</label>
                                    <input 
                                        type="text"
                                        name="intro[top]"
                                        value="{{ old('intro.top', (isset($intro) && isset($intro['top'])) ? $intro['top'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập top"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Phần trăm</label>
                                    <input 
                                        type="text"
                                        name="intro[percent]"
                                        value="{{ old('intro.percent', (isset($intro) && isset($intro['percent'])) ? $intro['percent'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập phần trăm"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">File tải tài liệu</label>
                                    <input 
                                        type="text"
                                        name="download_file"
                                        value="{{ old('download_file', ($school->download_file ?? '') ?? '') }}"
                                        class="form-control upload-file"
                                        placeholder="Chọn file tải tài liệu"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Khối 3: Announce -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông báo tuyển sinh</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_announce" value="{{ old('is_show_announce', ($school->is_show_announce ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_announce', ($school->is_show_announce ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ảnh thông báo tuyển sinh</label>
                                    <input 
                                        type="text"
                                        name="announce_image"
                                        value="{{ old('announce_image', $school->announce_image ?? '') }}"
                                        class="form-control upload-image"
                                        placeholder="Chọn ảnh"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tiêu đề</label>
                                    <input 
                                        type="text"
                                        name="announce_title"
                                        value="{{ old('announce_title', ($school->announce_title ?? '') ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập tiêu đề"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <input 
                                        type="text"
                                        name="announce[description]"
                                        value="{{ old('announce.description', (isset($announce) && isset($announce['description'])) ? $announce['description'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập mô tả"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Nội dung</label>
                                    <textarea 
                                        name="announce[content]" 
                                        class="form-control"
                                        rows="4"
                                        placeholder="Nhập nội dung">{{ old('announce.content', (isset($announce) && isset($announce['content'])) ? $announce['content'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Đối tượng</label>
                                    <textarea 
                                        name="announce[target]" 
                                        class="ck-editor" 
                                        id="ckAnnounceTarget"
                                        data-height="200">{{ old('announce.target', (isset($announce) && isset($announce['target'])) ? $announce['target'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Loại hình</label>
                                    <textarea 
                                        name="announce[type]" 
                                        class="ck-editor" 
                                        id="ckAnnounceType"
                                        data-height="200">{{ old('announce.type', (isset($announce) && isset($announce['type'])) ? $announce['type'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Yêu cầu</label>
                                    <textarea 
                                        name="announce[request]" 
                                        class="ck-editor" 
                                        id="ckAnnounceRequest"
                                        data-height="200">{{ old('announce.request', (isset($announce) && isset($announce['request'])) ? $announce['request'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Địa chỉ</label>
                                    <textarea 
                                        name="announce[address]" 
                                        class="ck-editor" 
                                        id="ckAnnounceAddress"
                                        data-height="200">{{ old('announce.address', (isset($announce) && isset($announce['address'])) ? $announce['address'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Giá trị</label>
                                    <textarea 
                                        name="announce[value]" 
                                        class="ck-editor" 
                                        id="ckAnnounceValue"
                                        data-height="200">{{ old('announce.value', (isset($announce) && isset($announce['value'])) ? $announce['value'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Khối 4: Advantage -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Ưu điểm</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_advantage" value="{{ old('is_show_advantage', ($school->is_show_advantage ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_advantage', ($school->is_show_advantage ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tiêu đề</label>
                                    <input 
                                        type="text"
                                        name="advantage[title]"
                                        value="{{ old('advantage.title', (isset($advantage) && isset($advantage['title'])) ? $advantage['title'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập tiêu đề"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <input 
                                        type="text"
                                        name="advantage[description]"
                                        value="{{ old('advantage.description', (isset($advantage) && isset($advantage['description'])) ? $advantage['description'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập mô tả"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <h5>
                                        Danh sách items 
                                        <a href="javascript:void(0)" id="addAdvantageItemBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm</a>
                                        <a href="javascript:void(0)" class="toggle-items-btn" data-target="advantageItemsContainer" style="margin-left: 10px; color: #1ab394;">
                                            <i class="fa fa-chevron-up"></i> <span class="toggle-text">Thu gọn</span>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div id="advantageItemsContainer" class="items-container">
                            @if(old('advantage.items'))
                                @foreach(old('advantage.items') as $index => $item)
                                    @include('backend.school.component.advantage-item', ['index' => $index, 'item' => $item])
                                @endforeach
                            @elseif(isset($advantage) && isset($advantage['items']) && is_array($advantage['items']) && count($advantage['items']) > 0)
                                @foreach($advantage['items'] as $index => $item)
                                    @include('backend.school.component.advantage-item', ['index' => $index, 'item' => $item])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Khối 5: Suitable -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Phù hợp</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_suitable" value="{{ old('is_show_suitable', ($school->is_show_suitable ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_suitable', ($school->is_show_suitable ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên</label>
                                    <input 
                                        type="text"
                                        name="suitable[name]"
                                        value="{{ old('suitable.name', (isset($suitable) && isset($suitable['name'])) ? $suitable['name'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <input 
                                        type="text"
                                        name="suitable[description]"
                                        value="{{ old('suitable.description', (isset($suitable) && isset($suitable['description'])) ? $suitable['description'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập mô tả"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <h5>
                                        Danh sách items 
                                        <a href="javascript:void(0)" id="addSuitableItemBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm</a>
                                        <a href="javascript:void(0)" class="toggle-items-btn" data-target="suitableItemsContainer" style="margin-left: 10px; color: #1ab394;">
                                            <i class="fa fa-chevron-up"></i> <span class="toggle-text">Thu gọn</span>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div id="suitableItemsContainer" class="items-container">
                            @if(old('suitable.items'))
                                @foreach(old('suitable.items') as $index => $item)
                                    @include('backend.school.component.suitable-item', ['index' => $index, 'item' => $item])
                                @endforeach
                            @elseif(isset($suitable) && isset($suitable['items']) && is_array($suitable['items']) && count($suitable['items']) > 0)
                                @foreach($suitable['items'] as $index => $item)
                                    @include('backend.school.component.suitable-item', ['index' => $index, 'item' => $item])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Khối 6: Majors -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Chọn ngành</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_majors" value="{{ old('is_show_majors', ($school->is_show_majors ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_majors', ($school->is_show_majors ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        @php
                            // Lấy danh sách major_id đã chọn từ relationship hoặc từ majors data
                            $selectedMajorIds = [];
                            
                            // Ưu tiên lấy từ old (khi có validation error)
                            if (old('selected_major_ids')) {
                                $selectedMajorIds = old('selected_major_ids');
                            } else {
                                // Lấy từ relationship school->majors nếu có
                                if (isset($school) && $school->majors && $school->majors->count() > 0) {
                                    $selectedMajorIds = $school->majors->pluck('id')->toArray();
                                } else {
                                    // Fallback: lấy từ majors data trong JSON
                                    $majorsData = (isset($majors) && is_array($majors)) ? $majors : [];
                                    if (is_array($majorsData) && count($majorsData) > 0) {
                                        foreach ($majorsData as $majorData) {
                                            if (isset($majorData['major_id'])) {
                                                $selectedMajorIds[] = $majorData['major_id'];
                                            }
                                        }
                                    }
                                }
                            }
                            
                            $majorsData = old('majors', (isset($majors) && is_array($majors)) ? $majors : []);
                        @endphp
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Chọn ngành</label>
                                    <select 
                                        name="selected_major_ids[]"
                                        id="selected_major_ids" 
                                        class="form-control select2" 
                                        multiple
                                        data-placeholder="Chọn các ngành"
                                    >
                                        @if(isset($majorsList) && count($majorsList) > 0)
                                            @foreach($majorsList as $major)
                                                <option 
                                                    value="{{ $major->id }}"
                                                    {{ in_array($major->id, $selectedMajorIds) ? 'selected' : '' }}
                                                >
                                                    {{ $major->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="majorsDetailsContainer" style="margin-top: 20px;">
                            @if(is_array($majorsData) && count($majorsData) > 0)
                                @foreach($majorsData as $index => $majorData)
                                    @include('backend.school.component.major-item', ['index' => $index, 'majorData' => $majorData, 'majorsList' => $majorsList ?? []])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Khối 7: Study Method -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Hình thức học</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_study_method" value="{{ old('is_show_study_method', ($school->is_show_study_method ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_study_method', ($school->is_show_study_method ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên</label>
                                    <input 
                                        type="text"
                                        name="study_method[name]"
                                        value="{{ old('study_method.name', (isset($studyMethod) && isset($studyMethod['name'])) ? $studyMethod['name'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <input 
                                        type="text"
                                        name="study_method[description]"
                                        value="{{ old('study_method.description', (isset($studyMethod) && isset($studyMethod['description'])) ? $studyMethod['description'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập mô tả"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ảnh</label>
                                    <input 
                                        type="text"
                                        name="study_method[image]"
                                        value="{{ old('study_method.image', (isset($studyMethod) && isset($studyMethod['image'])) ? $studyMethod['image'] : '') }}"
                                        class="form-control upload-image"
                                        placeholder="Chọn ảnh"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Nội dung</label>
                                    <textarea 
                                        name="study_method[content]" 
                                        class="ck-editor" 
                                        id="ckStudyMethodContent"
                                        data-height="200">{{ old('study_method.content', (isset($studyMethod) && isset($studyMethod['content'])) ? $studyMethod['content'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <h5>
                                        Danh sách items 
                                        <a href="javascript:void(0)" id="addStudyMethodItemBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm</a>
                                        <a href="javascript:void(0)" class="toggle-items-btn" data-target="studyMethodItemsContainer" style="margin-left: 10px; color: #1ab394;">
                                            <i class="fa fa-chevron-up"></i> <span class="toggle-text">Thu gọn</span>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div id="studyMethodItemsContainer" class="items-container">
                            @if(old('study_method.items'))
                                @foreach(old('study_method.items') as $index => $item)
                                    @include('backend.school.component.study-method-item', ['index' => $index, 'item' => $item])
                                @endforeach
                            @elseif(isset($studyMethod) && isset($studyMethod['items']) && is_array($studyMethod['items']) && count($studyMethod['items']) > 0)
                                @foreach($studyMethod['items'] as $index => $item)
                                    @include('backend.school.component.study-method-item', ['index' => $index, 'item' => $item])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Khối 8: Feedback -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cảm nhận học viên</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_feedback" value="{{ old('is_show_feedback', ($school->is_show_feedback ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_feedback', ($school->is_show_feedback ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <textarea 
                                        name="feedback[description]"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Nhập mô tả"
                                    >{{ old('feedback.description', (isset($feedback) && isset($feedback['description'])) ? $feedback['description'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <h5>
                                        Danh sách cảm nhận 
                                        <a href="javascript:void(0)" id="addStudentFeedbackBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm</a>
                                        <a href="javascript:void(0)" class="toggle-items-btn" data-target="studentFeedbacksContainer" style="margin-left: 10px; color: #1ab394;">
                                            <i class="fa fa-chevron-up"></i> <span class="toggle-text">Thu gọn</span>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div id="studentFeedbacksContainer" class="items-container">
                            @if(old('feedback.items'))
                                @foreach(old('feedback.items') as $feedbackIndex => $item)
                                    @include('backend.school.component.student-feedback', ['feedbackIndex' => $feedbackIndex, 'feedback' => $item])
                                @endforeach
                            @elseif(isset($feedback) && isset($feedback['items']) && is_array($feedback['items']) && count($feedback['items']) > 0)
                                @foreach($feedback['items'] as $feedbackIndex => $item)
                                    @include('backend.school.component.student-feedback', ['feedbackIndex' => $feedbackIndex, 'feedback' => $item])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Khối 9: Event -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Sự kiện</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_event" value="{{ old('is_show_event', ($school->is_show_event ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_event', ($school->is_show_event ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Chọn chuyên mục</label>
                                    <select 
                                        name="event[post_catalogue_id]"
                                        id="event_post_catalogue_id" 
                                        class="form-control select2" 
                                        data-placeholder="Chọn chuyên mục"
                                    >
                                        <option value="">-- Chọn chuyên mục --</option>
                                        @if(isset($postCatalogues) && count($postCatalogues) > 0)
                                            @foreach($postCatalogues as $postCatalogue)
                                                <option 
                                                    value="{{ $postCatalogue->id }}"
                                                    {{ (old('event.post_catalogue_id') == $postCatalogue->id) || ($eventPostCatalogueId == $postCatalogue->id) ? 'selected' : '' }}
                                                >
                                                    {{ $postCatalogue->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Khối 10: Value -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Giá trị văn bằng</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_value" value="{{ old('is_show_value', ($school->is_show_value ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_value', ($school->is_show_value ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên</label>
                                    <input 
                                        type="text"
                                        name="value[name]"
                                        value="{{ old('value.name', (isset($value) && isset($value['name'])) ? $value['name'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ảnh</label>
                                    <input 
                                        type="text"
                                        name="value[image]"
                                        value="{{ old('value.image', (isset($value) && isset($value['image'])) ? $value['image'] : '') }}"
                                        class="form-control upload-image"
                                        placeholder="Chọn ảnh"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <input 
                                        type="text"
                                        name="value[description]"
                                        value="{{ old('value.description', (isset($value) && isset($value['description'])) ? $value['description'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập mô tả"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <h5>
                                        Danh sách giá trị 
                                        <a href="javascript:void(0)" id="addDegreeValueItemBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm</a>
                                        <a href="javascript:void(0)" class="toggle-items-btn" data-target="degreeValueItemsContainer" style="margin-left: 10px; color: #1ab394;">
                                            <i class="fa fa-chevron-up"></i> <span class="toggle-text">Thu gọn</span>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div id="degreeValueItemsContainer" class="items-container">
                            @if(old('value.items'))
                                @foreach(old('value.items') as $itemIndex => $item)
                                    @include('backend.school.component.degree-value-item', ['itemIndex' => $itemIndex, 'item' => $item])
                                @endforeach
                            @elseif(isset($value) && isset($value['items']) && is_array($value['items']) && count($value['items']) > 0)
                                @foreach($value['items'] as $itemIndex => $item)
                                    @include('backend.school.component.degree-value-item', ['itemIndex' => $itemIndex, 'item' => $item])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                @include('backend.dashboard.component.album', ['model' => ($school) ?? null])

                @php
                    // Tạo object để truyền vào SEO component với dữ liệu từ pivot
                    $seoModel = isset($school) ? (object) [
                        'meta_title' => $metaTitle,
                        'meta_keyword' => $metaKeyword,
                        'meta_description' => $metaDescription,
                        'canonical' => $canonical,
                    ] : null;
                @endphp
                @include('backend.dashboard.component.seo', ['model' => $seoModel])
            </div>
            <div class="col-lg-3">
                <div class="text-right mb15 fixed-bottom">
                    @if($config['method'] == 'create')
                        @include('components.btn-create')
                    @else
                        @include('components.btn-update',['model' => $school ?? null])
                    @endif   
                </div>
                @include('backend.school.component.aside')
            </div>
        </div>
    </div>
</form>

<style>
.items-container {
    max-height: 500px;
    overflow-y: auto;
    transition: max-height 0.3s ease;
}
.items-container.collapsed {
    max-height: 0;
    overflow: hidden;
}
.toggle-items-btn {
    cursor: pointer;
    text-decoration: none;
}
.toggle-items-btn:hover {
    text-decoration: underline;
}
.toggle-items-btn i {
    transition: transform 0.3s ease;
}
.toggle-items-btn.collapsed i {
    transform: rotate(180deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let advantageItemIndex = {{ count(old('advantage.items', (isset($advantage) && isset($advantage['items']) && is_array($advantage['items'])) ? $advantage['items'] : [])) }};
    let suitableItemIndex = {{ count(old('suitable.items', (isset($suitable) && isset($suitable['items']) && is_array($suitable['items'])) ? $suitable['items'] : [])) }};
    let majorItemIndex = {{ count(old('majors', (isset($majors) && is_array($majors)) ? $majors : [])) }};
    let studyMethodItemIndex = {{ count(old('study_method.items', (isset($studyMethod) && isset($studyMethod['items']) && is_array($studyMethod['items'])) ? $studyMethod['items'] : [])) }};
    let studentFeedbackIndex = {{ count(old('feedback.items', (isset($feedback) && isset($feedback['items']) && is_array($feedback['items'])) ? $feedback['items'] : [])) }};
    let degreeValueItemIndex = {{ count(old('value.items', (isset($value) && isset($value['items']) && is_array($value['items'])) ? $value['items'] : [])) }};
    
    // Initialize Select2 for majors and events
    setTimeout(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('#selected_major_ids').select2({
                placeholder: 'Chọn các ngành',
                allowClear: true
            }).on('change', function() {
                updateMajorsDetails();
            });
            
            $('#event_post_catalogue_id').select2({
                placeholder: 'Chọn chuyên mục',
                allowClear: true
            });
        }
    }, 500);

    // Update majors details when majors are selected
    function updateMajorsDetails() {
        const selectedIds = $('#selected_major_ids').val() || [];
        // Convert all to string for comparison
        const selectedIdsStr = selectedIds.map(id => String(id));
        const container = $('#majorsDetailsContainer');
        
        // Get existing major IDs from DOM
        const existingIds = [];
        container.find('.major-item').each(function() {
            const majorId = $(this).attr('data-major-id') || $(this).data('major-id');
            if (majorId) {
                existingIds.push(String(majorId));
            }
        });
        
        // Remove unselected majors
        container.find('.major-item').each(function() {
            const majorId = $(this).attr('data-major-id') || $(this).data('major-id');
            const majorIdStr = String(majorId);
            if (majorId && !selectedIdsStr.includes(majorIdStr)) {
                $(this).remove();
            }
        });
        
        // Add new selected majors
        selectedIds.forEach(function(majorId) {
            const majorIdStr = String(majorId);
            if (!existingIds.includes(majorIdStr)) {
                const majorOption = $('#selected_major_ids option[value="' + majorId + '"]');
                const majorName = majorOption.text();
                const template = `
                    <div class="major-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;" data-major-id="${majorId}">
                        <input type="hidden" name="majors[${majorItemIndex}][major_id]" value="${majorId}">
                        <h5>${majorName}</h5>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Hình thức xét tuyển</label>
                                    <input type="text" name="majors[${majorItemIndex}][admission_method]" class="form-control" placeholder="Nhập hình thức xét tuyển">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Thời gian</label>
                                    <input type="text" name="majors[${majorItemIndex}][duration]" class="form-control" placeholder="Nhập thời gian">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Học phí</label>
                                    <input type="text" name="majors[${majorItemIndex}][tuition]" class="form-control" placeholder="Nhập học phí">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Địa điểm</label>
                                    <input type="text" name="majors[${majorItemIndex}][location]" class="form-control" placeholder="Nhập địa điểm">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label class="control-label text-left">Học phí năm</label>
                                    <input type="text" name="majors[${majorItemIndex}][annual_tuition]" class="form-control" placeholder="Nhập học phí năm">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label class="control-label text-left">Số tín chỉ</label>
                                    <input type="text" name="majors[${majorItemIndex}][credits]" class="form-control" placeholder="Nhập số tín chỉ">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label class="control-label text-left">Học phí / 1 tín chỉ</label>
                                    <input type="text" name="majors[${majorItemIndex}][tuition_per_credit]" class="form-control" placeholder="Nhập học phí / 1 tín chỉ">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-danger removeMajorItemBtn"><i class="fa fa-trash"></i> Xóa</button>
                            </div>
                        </div>
                    </div>
                `;
                container.append(template);
                majorItemIndex++;
            }
        });
    }
    
    // Add Advantage Item
    document.getElementById('addAdvantageItemBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('advantageItemsContainer');
        const template = `
            <div class="advantage-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
                <div class="row mb15">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label class="control-label text-left">Tên</label>
                            <input type="text" name="advantage[items][${advantageItemIndex}][name]" class="form-control" placeholder="Nhập tên">
                        </div>
                    </div>
                </div>
                <div class="row mb15">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label class="control-label text-left">Mô tả</label>
                            <input type="text" name="advantage[items][${advantageItemIndex}][description]" class="form-control" placeholder="Nhập mô tả">
                        </div>
                    </div>
                </div>
                <div class="row mb15">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label class="control-label text-left">Icon</label>
                            <input type="text" name="advantage[items][${advantageItemIndex}][icon]" class="form-control upload-image" placeholder="Chọn ảnh icon" readonly>
                        </div>
                    </div>
                </div>
                <div class="row mb15">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label class="control-label text-left">Ghi chú</label>
                            <input type="text" name="advantage[items][${advantageItemIndex}][note]" class="form-control" placeholder="Nhập ghi chú">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-danger removeAdvantageItemBtn"><i class="fa fa-trash"></i> Xóa</button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        
        // Khởi tạo lại upload-image cho input mới
        const newItem = container.querySelector('.advantage-item:last-child');
        if (newItem) {
            const uploadInput = newItem.querySelector('.upload-image');
            if (uploadInput && typeof HT !== 'undefined' && typeof HT.upload === 'function') {
                HT.upload();
            }
        }
        
        advantageItemIndex++;
    });

    // Add Suitable Item
    document.getElementById('addSuitableItemBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('suitableItemsContainer');
        const template = `
            <div class="suitable-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
                <div class="row mb15">
                    <div class="col-lg-3">
                        <div class="form-row">
                            <label class="control-label text-left">Ảnh</label>
                            <input type="text" name="suitable[items][${suitableItemIndex}][image]" class="form-control upload-image" placeholder="Chọn ảnh" readonly>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-row">
                            <label class="control-label text-left">Tên</label>
                            <input type="text" name="suitable[items][${suitableItemIndex}][name]" class="form-control" placeholder="Nhập tên">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-row">
                            <label class="control-label text-left">Mô tả</label>
                            <input type="text" name="suitable[items][${suitableItemIndex}][description]" class="form-control" placeholder="Nhập mô tả">
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-row">
                            <label class="control-label text-left">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm removeSuitableItemBtn" style="width: 100%;">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        suitableItemIndex++;
    });

    // Add Study Method Item
    document.getElementById('addStudyMethodItemBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('studyMethodItemsContainer');
        const template = `
            <div class="study-method-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
                <div class="row mb15">
                    <div class="col-lg-3">
                        <div class="form-row">
                            <label class="control-label text-left">Ảnh</label>
                            <input type="text" name="study_method[items][${studyMethodItemIndex}][image]" class="form-control upload-image" placeholder="Chọn ảnh" readonly>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-row">
                            <label class="control-label text-left">Tên</label>
                            <input type="text" name="study_method[items][${studyMethodItemIndex}][name]" class="form-control" placeholder="Nhập tên">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-row">
                            <label class="control-label text-left">Mô tả</label>
                            <input type="text" name="study_method[items][${studyMethodItemIndex}][description]" class="form-control" placeholder="Nhập mô tả">
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-row">
                            <label class="control-label text-left">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm removeStudyMethodItemBtn" style="width: 100%;">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        studyMethodItemIndex++;
    });

    // Add Student Feedback
    document.getElementById('addStudentFeedbackBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('studentFeedbacksContainer');
        const template = `
            <div class="student-feedback-item mb15" data-feedback-index="${studentFeedbackIndex}">
                <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <div class="row mb15">
                        <div class="col-lg-3">
                            <div class="form-row">
                                <label class="control-label text-left">Ảnh đại diện</label>
                                <input type="text" name="feedback[items][${studentFeedbackIndex}][image]" class="form-control upload-image" placeholder="Chọn ảnh" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-row">
                                <label class="control-label text-left">Tên</label>
                                <input type="text" name="feedback[items][${studentFeedbackIndex}][name]" class="form-control" placeholder="Nhập tên">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-row">
                                <label class="control-label text-left">Chức vụ</label>
                                <input type="text" name="feedback[items][${studentFeedbackIndex}][position]" class="form-control" placeholder="Nhập chức vụ">
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-row">
                                <label class="control-label text-left">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm removeStudentFeedbackBtn" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-row">
                                <label class="control-label text-left">Mô tả</label>
                                <textarea name="feedback[items][${studentFeedbackIndex}][description]" class="form-control" rows="3" placeholder="Nhập mô tả"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        studentFeedbackIndex++;
    });

    // Add Degree Value Item
    document.getElementById('addDegreeValueItemBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('degreeValueItemsContainer');
        const template = `
            <div class="degree-value-item mb15" data-item-index="${degreeValueItemIndex}">
                <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="form-row">
                                <label class="control-label text-left">Icon</label>
                                <input type="text" name="value[items][${degreeValueItemIndex}][icon]" class="form-control upload-image" placeholder="Chọn icon" readonly>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-row">
                                <label class="control-label text-left">Tên giá trị</label>
                                <input type="text" name="value[items][${degreeValueItemIndex}][name]" class="form-control" placeholder="Nhập tên giá trị">
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-row">
                                <label class="control-label text-left">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm removeDegreeValueItemBtn" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        degreeValueItemIndex++;
    });

    // Remove handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeAdvantageItemBtn')) {
            e.preventDefault();
            e.target.closest('.advantage-item').remove();
        }
        if (e.target.closest('.removeSuitableItemBtn')) {
            e.preventDefault();
            e.target.closest('.suitable-item').remove();
        }
        if (e.target.closest('.removeMajorItemBtn')) {
            e.preventDefault();
            const btn = $(e.target.closest('.removeMajorItemBtn'));
            const majorItem = btn.closest('.major-item');
            const majorId = majorItem.attr('data-major-id') || majorItem.data('major-id');
            
            if (majorId) {
                // Xóa khối major-item trước
                majorItem.remove();
                
                // Sau đó xóa khỏi select2
                const currentVal = $('#selected_major_ids').val() || [];
                const newVal = currentVal.filter(id => String(id) !== String(majorId));
                $('#selected_major_ids').val(newVal).trigger('change');
            }
        }
        if (e.target.closest('.removeStudyMethodItemBtn')) {
            e.preventDefault();
            e.target.closest('.study-method-item').remove();
        }
        if (e.target.closest('.removeStudentFeedbackBtn')) {
            e.preventDefault();
            e.target.closest('.student-feedback-item').remove();
        }
        if (e.target.closest('.removeDegreeValueItemBtn')) {
            e.preventDefault();
            e.target.closest('.degree-value-item').remove();
        }
    });

    // Switchery handler
    $(document).on('change', '.js-switch', function() {
        var hiddenInput = $(this).siblings('input[type="hidden"]');
        if (hiddenInput.length) {
            hiddenInput.val(this.checked ? 2 : 1);
        }
    });

    // Toggle collapse/expand
    document.querySelectorAll('.toggle-items-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const container = document.getElementById(targetId);
            const icon = this.querySelector('i');
            const text = this.querySelector('.toggle-text');
            
            if (container) {
                container.classList.toggle('collapsed');
                this.classList.toggle('collapsed');
                
                if (container.classList.contains('collapsed')) {
                    icon.className = 'fa fa-chevron-down';
                    text.textContent = 'Mở rộng';
                } else {
                    icon.className = 'fa fa-chevron-up';
                    text.textContent = 'Thu gọn';
                }
            }
        });
    });
});
</script>

