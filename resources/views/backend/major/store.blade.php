@include('backend.dashboard.component.breadcrumb', ['title' => ($config['method'] == 'create') ? 'Thêm mới Ngành học' : 'Cập nhật Ngành học'])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('major.store') : route('major.update', $major->id ?? '');
    // Load data from JSON fields in major_language pivot (đã được cast tự động)
    $pivot = isset($major) && $major->languages && count($major->languages) > 0 ? $major->languages[0]->pivot : null;
    
    // Lấy các trường text từ pivot
    $majorName = $pivot->name ?? ($major->name ?? '');
    $majorDescription = $pivot->description ?? ($major->description ?? '');
    $majorContent = $pivot->content ?? ($major->content ?? '');
    $trainingSystem = $pivot->training_system ?? '';
    $studyMethod = $pivot->study_method ?? '';
    $admissionMethod = $pivot->admission_method ?? '';
    $enrollmentQuota = $pivot->enrollment_quota ?? '';
    $enrollmentPeriod = $pivot->enrollment_period ?? '';
        $admissionType = $pivot->admission_type ?? '';
        $degreeType = $pivot->degree_type ?? '';
        $trainingDuration = $pivot->training_duration ?? '';
        $canonical = $pivot->canonical ?? '';
    $metaTitle = $pivot->meta_title ?? '';
    $metaKeyword = $pivot->meta_keyword ?? '';
    $metaDescription = $pivot->meta_description ?? '';
    
    // Đảm bảo dữ liệu JSON là array, không phải string
    $feature = ($pivot && isset($pivot->feature)) ? (is_array($pivot->feature) ? $pivot->feature : []) : [];
    $target = ($pivot && isset($pivot->target)) ? (is_array($pivot->target) ? $pivot->target : []) : [];
    $address = ($pivot && isset($pivot->address)) ? (is_array($pivot->address) ? $pivot->address : []) : [];
    $overview = ($pivot && isset($pivot->overview)) ? (is_array($pivot->overview) ? $pivot->overview : null) : null;
    $who = ($pivot && isset($pivot->who)) ? (is_array($pivot->who) ? $pivot->who : []) : [];
    $priority = ($pivot && isset($pivot->priority)) ? (is_array($pivot->priority) ? $pivot->priority : []) : [];
    $learn = ($pivot && isset($pivot->learn)) ? (is_array($pivot->learn) ? $pivot->learn : null) : null;
    $chance = ($pivot && isset($pivot->chance)) ? (is_array($pivot->chance) ? $pivot->chance : null) : null;
    $school = ($pivot && isset($pivot->school)) ? (is_array($pivot->school) ? $pivot->school : null) : null;
    $value = ($pivot && isset($pivot->value)) ? (is_array($pivot->value) ? $pivot->value : null) : null;
    $feedback = ($pivot && isset($pivot->feedback)) ? (is_array($pivot->feedback) ? $pivot->feedback : null) : null;
    // Event: lưu trực tiếp là số ID (post_catalogue_id), không phải JSON
    $eventPostCatalogueId = null;
    if ($pivot && isset($pivot->event)) {
        $eventValue = $pivot->event;
        if (is_numeric($eventValue)) {
            $eventPostCatalogueId = (int)$eventValue;
        } elseif (is_string($eventValue)) {
            // Xử lý trường hợp cũ (JSON string) - tương thích ngược
            $decoded = json_decode($eventValue, true);
            if (is_array($decoded) && isset($decoded['post_catalogue_id'])) {
                $eventPostCatalogueId = (int)$decoded['post_catalogue_id'];
            } elseif (is_numeric($eventValue)) {
                $eventPostCatalogueId = (int)$eventValue;
            }
        } elseif (is_array($eventValue) && isset($eventValue['post_catalogue_id'])) {
            // Xử lý trường hợp cũ (JSON array) - tương thích ngược
            $eventPostCatalogueId = (int)$eventValue['post_catalogue_id'];
        }
    }
@endphp
<form action="{{ $url }}" method="post" class="box" id="majorForm">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin cơ bản</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên chuyên ngành<span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="name"
                                        value="{{ old('name', $majorName) }}"
                                        class="form-control change-title"
                                        data-flag = "{{ (!empty($majorName)) ? 1 : 0 }}"
                                        placeholder="Nhập tên chuyên ngành"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tiêu đề phụ</label>
                                    <input 
                                        type="text"
                                        name="subtitle"
                                        value="{{ old('subtitle', ($major->subtitle ?? '') ?? '' ) }}"
                                        class="form-control"
                                        placeholder="Nhập tiêu đề phụ (màu trắng)"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Banner</label>
                                    <input 
                                        type="text"
                                        name="banner"
                                        value="{{ old('banner', ($major->banner ?? '') ?? '' ) }}"
                                        class="form-control upload-image"
                                        placeholder="Chọn banner"
                                        readonly
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb30">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <textarea 
                                        name="description" 
                                        class="form-control"
                                        rows="4"
                                        placeholder="Nhập mô tả">{{ old('description', $majorDescription) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Danh sách tính năng <a href="javascript:void(0)" id="addFeatureBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm tính năng</a></h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_feature" value="{{ old('is_show_feature', ($major->is_show_feature ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_feature', ($major->is_show_feature ?? 2) ?? 2) == 2 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div id="featuresContainer">
                            @if(old('feature'))
                                @foreach(old('feature') as $index => $feature)
                                    @include('backend.major.component.feature-item', ['index' => $index, 'feature' => $feature])
                                @endforeach
                            @elseif(isset($feature) && is_array($feature) && count($feature) > 0)
                                @foreach($feature as $index => $item)
                                    @include('backend.major.component.feature-item', ['index' => $index, 'feature' => $item])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Information Table Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin chi tiết</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td style="width: 150px;"><strong>Hệ đào tạo</strong></td>
                                            <td>
                                                <input 
                                                    type="text"
                                                    name="training_system"
                                                    value="{{ old('training_system', $trainingSystem) }}"
                                                    class="form-control"
                                                    placeholder="Nhập hệ đào tạo"
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Hình thức học</strong></td>
                                            <td>
                                                <input 
                                                    type="text"
                                                    name="study_method"
                                                    value="{{ old('study_method', $studyMethod) }}"
                                                    class="form-control"
                                                    placeholder="Nhập hình thức học"
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tổng số tín chỉ</strong></td>
                                            <td>
                                                <input 
                                                    type="text"
                                                    name="admission_method"
                                                    value="{{ old('admission_method', $admissionMethod) }}"
                                                    class="form-control"
                                                    placeholder="Nhập tổng số tín chỉ (VD: 65-137 Tín Chỉ Tùy Trường)"
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Chỉ tiêu tuyển</strong></td>
                                            <td>
                                                <input 
                                                    type="text"
                                                    name="enrollment_quota"
                                                    value="{{ old('enrollment_quota', $enrollmentQuota) }}"
                                                    class="form-control"
                                                    placeholder="Nhập chỉ tiêu tuyển"
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
                                            <td style="width: 150px;"><strong>Thời gian tuyển</strong></td>
                                            <td>
                                                <input 
                                                    type="text"
                                                    name="enrollment_period"
                                                    value="{{ old('enrollment_period', $enrollmentPeriod) }}"
                                                    class="form-control"
                                                    placeholder="Nhập thời gian tuyển"
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Hình thức tuyển sinh</strong></td>
                                            <td>
                                                <input 
                                                    type="text"
                                                    name="admission_type"
                                                    value="{{ old('admission_type', $admissionType) }}"
                                                    class="form-control"
                                                    placeholder="Nhập hình thức tuyển sinh"
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Loại văn bằng</strong></td>
                                            <td>
                                                <input 
                                                    type="text"
                                                    name="degree_type"
                                                    value="{{ old('degree_type', $degreeType) }}"
                                                    class="form-control"
                                                    placeholder="Nhập loại văn bằng"
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Thời gian đào tạo</strong></td>
                                            <td>
                                                <input 
                                                    type="text"
                                                    name="training_duration"
                                                    value="{{ old('training_duration', $trainingDuration) }}"
                                                    class="form-control"
                                                    placeholder="Nhập thời gian đào tạo"
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admission Targets and Reception Places Section -->
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <!-- Đối tượng tuyển sinh -->
                            <div class="col-lg-6">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5>Đối tượng tuyển sinh <a href="javascript:void(0)" id="addAdmissionTargetBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm</a></h5>
                                    </div>
                                    <div class="ibox-content">
                                        <div id="admissionTargetsContainer">
                                            @if(old('target'))
                                                @foreach(old('target') as $index => $item)
                                                    @include('backend.major.component.admission-target-item', ['index' => $index, 'target' => $item])
                                                @endforeach
                            @elseif(isset($target) && is_array($target) && count($target) > 0)
                                @foreach($target as $index => $item)
                                    @include('backend.major.component.admission-target-item', ['index' => $index, 'target' => $item])
                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Nơi tiếp nhận hồ sơ -->
                            <div class="col-lg-6">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5>Nơi tiếp nhận hồ sơ <a href="javascript:void(0)" id="addReceptionPlaceBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm</a></h5>
                                    </div>
                                    <div class="ibox-content">
                                        <div id="receptionPlacesContainer">
                                            @if(old('address'))
                                                @foreach(old('address') as $index => $place)
                                                    @include('backend.major.component.reception-place-item', ['index' => $index, 'place' => $place])
                                                @endforeach
                            @elseif(isset($address) && is_array($address) && count($address) > 0)
                                @foreach($address as $index => $place)
                                    @include('backend.major.component.reception-place-item', ['index' => $index, 'place' => $place])
                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Tổng quan chương trình</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-row">
                            <textarea 
                                name="content" 
                                class="ck-editor" 
                                id="ckContent"
                                data-height="500">{{ old('content', $majorContent) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Study Path File Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Lộ trình học</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-row">
                            <label for="" class="control-label text-left">Upload file lộ trình học</label>
                            <input 
                                type="text"
                                name="study_path_file"
                                value="{{ old('study_path_file', ($major->study_path_file ?? '') ?? '') }}"
                                class="form-control upload-file"
                                placeholder="Chọn file lộ trình học"
                                readonly
                                data-type="Files"
                            >
                        </div>
                    </div>
                </div>

                <!-- Overview Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Toàn Cảnh Ngành</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_overview" value="{{ old('is_show_overview', ($major->is_show_overview ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_overview', ($major->is_show_overview ?? 2) ?? 2) == 2 ? 'checked' : '' }}
                                   >
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tiêu đề</label>
                                    <input 
                                        type="text"
                                        name="overview[name]"
                                        value="{{ old('overview.name', (isset($overview) && isset($overview['name'])) ? $overview['name'] : '') }}"
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
                                    <textarea 
                                        name="overview[description]" 
                                        class="ck-editor" 
                                        id="ckOverviewContent"
                                        data-height="300">{{ old('overview.description', (isset($overview) && isset($overview['description'])) ? $overview['description'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ảnh đại diện</label>
                                    <input 
                                        type="text"
                                        name="overview[image]"
                                        value="{{ old('overview.image', (isset($overview) && isset($overview['image'])) ? $overview['image'] : '') }}"
                                        class="form-control upload-image"
                                        placeholder="Chọn ảnh"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <h5>
                                        Danh sách items 
                                        <a href="javascript:void(0)" id="addOverviewItemBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm</a>
                                        <a href="javascript:void(0)" class="toggle-items-btn" data-target="overviewItemsContainer" style="margin-left: 10px; color: #1ab394;">
                                            <i class="fa fa-chevron-up"></i> <span class="toggle-text">Thu gọn</span>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div id="overviewItemsContainer" class="items-container">
                            @if(old('overview.items'))
                                @foreach(old('overview.items') as $index => $item)
                                    @include('backend.major.component.overview-item', ['index' => $index, 'item' => $item])
                                @endforeach
                            @elseif(isset($overview) && isset($overview['items']) && is_array($overview['items']) && count($overview['items']) > 0)
                                @foreach($overview['items'] as $index => $item)
                                    @include('backend.major.component.overview-item', ['index' => $index, 'item' => $item])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Suitable For Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>
                            Ai phù hợp 
                            <a href="javascript:void(0)" id="addSuitableItemBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm</a>
                            <a href="javascript:void(0)" class="toggle-items-btn" data-target="suitableItemsContainer" style="margin-left: 10px; color: #1ab394;">
                                <i class="fa fa-chevron-up"></i> <span class="toggle-text">Thu gọn</span>
                            </a>
                        </h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_who" value="{{ old('is_show_who', ($major->is_show_who ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_who', ($major->is_show_who ?? 2) ?? 2) == 2 ? 'checked' : '' }}
                                   >
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label>Tiêu đề khối "Ai phù hợp"</label>
                            <input type="text" 
                                   name="who[title]" 
                                   class="form-control" 
                                   value="{{ old('who.title', isset($who['title']) ? $who['title'] : 'Ai Phù Hợp Theo Hình Thức Đào Tạo?') }}"
                                   placeholder="Ai Phù Hợp Theo Hình Thức Đào Tạo?">
                            <small class="form-text text-muted">Nếu để trống sẽ dùng giá trị mặc định: "Ai Phù Hợp Theo Hình Thức Đào Tạo?"</small>
                        </div>
                        <div id="suitableItemsContainer" class="items-container">
                            @if(old('who.items'))
                                @foreach(old('who.items') as $index => $item)
                                    @include('backend.major.component.suitable-item', ['index' => $index, 'item' => $item])
                                @endforeach
                            @elseif(old('who') && !isset(old('who')['title']))
                                @foreach(old('who') as $index => $item)
                                    @if(is_numeric($index))
                                        @include('backend.major.component.suitable-item', ['index' => $index, 'item' => $item])
                                    @endif
                                @endforeach
                            @elseif(isset($who) && is_array($who) && count($who) > 0)
                                @php
                                    $whoItems = isset($who['items']) && is_array($who['items']) ? $who['items'] : [];
                                    // Nếu không có 'items', có thể who là mảng trực tiếp các items
                                    if (empty($whoItems) && isset($who[0])) {
                                        $whoItems = $who;
                                    }
                                @endphp
                                @foreach($whoItems as $index => $item)
                                    @if(is_numeric($index))
                                        @include('backend.major.component.suitable-item', ['index' => $index, 'item' => $item])
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Advantages Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>
                            Ưu điểm 
                            <a href="javascript:void(0)" id="addAdvantageItemBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm</a>
                            <a href="javascript:void(0)" class="toggle-items-btn" data-target="advantageItemsContainer" style="margin-left: 10px; color: #1ab394;">
                                <i class="fa fa-chevron-up"></i> <span class="toggle-text">Thu gọn</span>
                            </a>
                        </h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_priority" value="{{ old('is_show_priority', ($major->is_show_priority ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_priority', ($major->is_show_priority ?? 2) ?? 2) == 2 ? 'checked' : '' }}
                                   >
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label>Tiêu đề khối "Ưu điểm"</label>
                            <input type="text" 
                                   name="priority[title]" 
                                   class="form-control" 
                                   value="{{ old('priority.title', isset($priority['title']) ? $priority['title'] : 'Ưu điểm khi học Đại học từ xa Ngành Ngôn Ngữ Anh') }}"
                                   placeholder="Ưu điểm khi học Đại học từ xa Ngành Ngôn Ngữ Anh">
                            <small class="form-text text-muted">Nếu để trống sẽ dùng giá trị mặc định</small>
                        </div>
                        <div id="advantageItemsContainer" class="items-container">
                            @if(old('priority.items'))
                                @foreach(old('priority.items') as $index => $item)
                                    @include('backend.major.component.advantage-item', ['index' => $index, 'item' => $item])
                                @endforeach
                            @elseif(old('priority') && !isset(old('priority')['title']))
                                @foreach(old('priority') as $index => $item)
                                    @if(is_numeric($index))
                                        @include('backend.major.component.advantage-item', ['index' => $index, 'item' => $item])
                                    @endif
                                @endforeach
                            @elseif(isset($priority) && is_array($priority) && count($priority) > 0)
                                @php
                                    $priorityItems = isset($priority['items']) && is_array($priority['items']) ? $priority['items'] : [];
                                    // Nếu không có 'items', có thể priority là mảng trực tiếp các items
                                    if (empty($priorityItems) && isset($priority[0])) {
                                        $priorityItems = $priority;
                                    }
                                @endphp
                                @foreach($priorityItems as $index => $item)
                                    @if(is_numeric($index))
                                        @include('backend.major.component.advantage-item', ['index' => $index, 'item' => $item])
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- What You Will Learn Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Bạn sẽ học được gì</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_learn" value="{{ old('is_show_learn', ($major->is_show_learn ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_learn', ($major->is_show_learn ?? 2) ?? 2) == 2 ? 'checked' : '' }}
                                   >
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tiêu đề</label>
                                    <input type="text" 
                                           name="learn[title]" 
                                           class="form-control" 
                                           value="{{ old('learn.title', isset($learn['title']) ? $learn['title'] : 'Bạn sẽ được học những gì?') }}"
                                           placeholder="Bạn sẽ được học những gì?">
                                    <small class="form-text text-muted">Nếu để trống sẽ dùng giá trị mặc định</small>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <textarea 
                                        name="learn[description]"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Nhập mô tả"
                                    >{{ old('learn.description', (isset($learn) && isset($learn['description'])) ? $learn['description'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <h5>
                                        Danh sách mục 
                                        <a href="javascript:void(0)" id="addWhatLearnCategoryBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm mục</a>
                                        <a href="javascript:void(0)" class="toggle-items-btn" data-target="whatLearnCategoriesContainer" style="margin-left: 10px; color: #1ab394;">
                                            <i class="fa fa-chevron-up"></i> <span class="toggle-text">Thu gọn</span>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div id="whatLearnCategoriesContainer" class="items-container">
                            @if(old('learn.items'))
                                @foreach(old('learn.items') as $categoryIndex => $category)
                                    @include('backend.major.component.what-learn-category', ['categoryIndex' => $categoryIndex, 'category' => $category])
                                @endforeach
                            @elseif(isset($learn) && isset($learn['items']) && is_array($learn['items']) && count($learn['items']) > 0)
                                @foreach($learn['items'] as $categoryIndex => $category)
                                    @include('backend.major.component.what-learn-category', ['categoryIndex' => $categoryIndex, 'category' => $category])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Career Opportunities Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cơ hội việc làm</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_chance" value="{{ old('is_show_chance', ($major->is_show_chance ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_chance', ($major->is_show_chance ?? 2) ?? 2) == 2 ? 'checked' : '' }}
                                   >
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <input 
                                        type="text"
                                        name="chance[description]"
                                        value="{{ old('chance.description', (isset($chance) && isset($chance['description'])) ? $chance['description'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập mô tả"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ảnh cơ hội việc làm</label>
                                    <input 
                                        type="text"
                                        name="career_image"
                                        value="{{ old('career_image', ($major->career_image ?? '') ?? '') }}"
                                        class="form-control upload-image"
                                        placeholder="Chọn ảnh cơ hội việc làm"
                                        readonly
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <h5>Danh sách tag <a href="javascript:void(0)" id="addCareerTagBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm tag</a></h5>
                                </div>
                            </div>
                        </div>
                        <div id="careerTagsContainer">
                            @if(old('chance.tags'))
                                @foreach(old('chance.tags') as $tagIndex => $tag)
                                    @include('backend.major.component.career-tag', ['tagIndex' => $tagIndex, 'tag' => $tag])
                                @endforeach
                            @elseif(isset($chance) && isset($chance['tags']) && is_array($chance['tags']) && count($chance['tags']) > 0)
                                @foreach($chance['tags'] as $tagIndex => $tag)
                                    @include('backend.major.component.career-tag', ['tagIndex' => $tagIndex, 'tag' => $tag])
                                @endforeach
                            @endif
                        </div>
                        <div class="row mb15 mt15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <h5>
                                        Danh sách nghề 
                                        <a href="javascript:void(0)" id="addCareerJobBtn" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm nghề</a>
                                        <a href="javascript:void(0)" class="toggle-items-btn" data-target="careerJobsContainer" style="margin-left: 10px; color: #1ab394;">
                                            <i class="fa fa-chevron-up"></i> <span class="toggle-text">Thu gọn</span>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div id="careerJobsContainer" class="items-container">
                            @if(old('chance.job'))
                                @foreach(old('chance.job') as $jobIndex => $job)
                                    @include('backend.major.component.career-job', ['jobIndex' => $jobIndex, 'job' => $job])
                                @endforeach
                            @elseif(isset($chance) && isset($chance['job']) && is_array($chance['job']) && count($chance['job']) > 0)
                                @foreach($chance['job'] as $jobIndex => $job)
                                    @include('backend.major.component.career-job', ['jobIndex' => $jobIndex, 'job' => $job])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Choose School Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Chọn trường</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_school" value="{{ old('is_show_school', ($major->is_show_school ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_school', ($major->is_show_school ?? 2) ?? 2) == 2 ? 'checked' : '' }}
                                   >
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mô tả</label>
                                    <textarea 
                                        name="school[description]"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Nhập mô tả"
                                    >{{ old('school.description', (isset($school) && isset($school['description'])) ? $school['description'] : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ảnh</label>
                                    <input 
                                        type="text"
                                        name="school[image]"
                                        value="{{ old('school.image', (isset($school) && isset($school['image'])) ? $school['image'] : '') }}"
                                        class="form-control upload-image"
                                        placeholder="Chọn ảnh"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Lưu ý</label>
                                    <input 
                                        type="text"
                                        name="school[note]"
                                        value="{{ old('school.note', (isset($school) && isset($school['note'])) ? $school['note'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập lưu ý"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Degree Value Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Giá trị văn bằng</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_value" value="{{ old('is_show_value', ($major->is_show_value ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_value', ($major->is_show_value ?? 2) ?? 2) == 2 ? 'checked' : '' }}
                                   >
                        </div>
                    </div>
                    <div class="ibox-content">
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
                                    <label for="" class="control-label text-left">Tên</label>
                                    <input 
                                        type="text"
                                        name="value[name]"
                                        value="{{ old('value.name', (isset($value) && isset($value['name'])) ? $value['name'] : '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên"
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
                                    @include('backend.major.component.degree-value-item', ['itemIndex' => $itemIndex, 'item' => $item])
                                @endforeach
                            @elseif(isset($value) && isset($value['items']) && is_array($value['items']) && count($value['items']) > 0)
                                @foreach($value['items'] as $itemIndex => $item)
                                    @include('backend.major.component.degree-value-item', ['itemIndex' => $itemIndex, 'item' => $item])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Student Feedback Section -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cảm nhận học viên</h5>
                        <div class="pull-right">
                            <input type="hidden" name="is_show_feedback" value="{{ old('is_show_feedback', ($major->is_show_feedback ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_feedback', ($major->is_show_feedback ?? 2) ?? 2) == 2 ? 'checked' : '' }}
                                   >
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
                                    @include('backend.major.component.student-feedback', ['feedbackIndex' => $feedbackIndex, 'feedback' => $item])
                                @endforeach
                            @elseif(isset($feedback) && isset($feedback['items']) && is_array($feedback['items']) && count($feedback['items']) > 0)
                                @foreach($feedback['items'] as $feedbackIndex => $item)
                                    @include('backend.major.component.student-feedback', ['feedbackIndex' => $feedbackIndex, 'feedback' => $item])
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
                            <input type="hidden" name="is_show_event" value="{{ old('is_show_event', ($major->is_show_event ?? 2) ?? 2) }}">
                            <input type="checkbox" 
                                   class="js-switch" 
                                   {{ old('is_show_event', ($major->is_show_event ?? 2) ?? 2) == 2 ? 'checked' : '' }}
                                   >
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
                                                @php
                                                    $catalogueId = is_object($postCatalogue) ? $postCatalogue->id : ($postCatalogue['id'] ?? '');
                                                    $catalogueName = is_object($postCatalogue) ? $postCatalogue->name : ($postCatalogue['name'] ?? '');
                                                    $isSelected = (old('event.post_catalogue_id') == $catalogueId) || ($eventPostCatalogueId == $catalogueId);
                                                @endphp
                                                @if(!empty($catalogueId) && !empty($catalogueName))
                                                    <option 
                                                        value="{{ $catalogueId }}"
                                                        {{ $isSelected ? 'selected' : '' }}
                                                    >
                                                        {{ $catalogueName }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cấu hình nâng cao (SEO) --}}
                @php
                    // Tạo object để truyền vào SEO component với dữ liệu từ pivot
                    $seoModel = isset($major) ? (object) [
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
                        @include('components.btn-update',['model' => $major ?? null])
                    @endif   
                </div>
                @include('backend.major.component.aside')
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
.what-learn-items-container {
    overflow: visible !important;
    max-height: none !important;
}
.what-learn-items-container.collapsed {
    display: none;
}
.toggle-learn-items-btn {
    cursor: pointer;
    text-decoration: none;
}
.toggle-learn-items-btn:hover {
    text-decoration: underline;
}
.toggle-learn-items-btn i {
    transition: transform 0.3s ease;
}
.toggle-learn-items-btn.collapsed i {
    transform: rotate(180deg);
}
.learn-item-content {
    display: block;
}
.learn-item-content.collapsed {
    display: none;
}
.toggle-item-icon {
    transition: transform 0.3s ease;
}
.what-learn-item.collapsed .toggle-item-icon {
    transform: rotate(-90deg);
}
.sortable-items .what-learn-item {
    cursor: move;
}
.sortable-items .what-learn-item.ui-sortable-helper {
    opacity: 0.8;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.ui-state-highlight {
    height: 100px;
    background: #f0f0f0;
    border: 2px dashed #1ab394;
    margin-bottom: 10px;
}
.what-learn-item .fa-grip-vertical {
    cursor: move !important;
    font-size: 16px;
    user-select: none;
}
.what-learn-item .fa-grip-vertical:hover {
    color: #1ab394;
}
.what-learn-item .learn-item-name {
    cursor: move !important;
}
.what-learn-item .learn-item-name:focus {
    cursor: text !important;
}
.what-learn-item .fa-grip-vertical {
    cursor: move;
    font-size: 16px;
}
.what-learn-item .fa-grip-vertical:hover {
    color: #1ab394;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let featureIndex = {{ count(old('feature', (isset($feature) && is_array($feature)) ? $feature : [])) }};
    let admissionTargetIndex = {{ count(old('target', (isset($target) && is_array($target)) ? $target : [])) }};
    let receptionPlaceIndex = {{ count(old('address', (isset($address) && is_array($address)) ? $address : [])) }};
    let overviewItemIndex = {{ count(old('overview.items', (isset($overview) && isset($overview['items']) && is_array($overview['items'])) ? $overview['items'] : [])) }};
    @php
        $whoItemsCount = 0;
        if (old('who.items')) {
            $whoItemsCount = count(old('who.items'));
        } elseif (old('who') && !isset(old('who')['title'])) {
            $whoItemsCount = count(array_filter(old('who'), 'is_numeric', ARRAY_FILTER_USE_KEY));
        } elseif (isset($who) && is_array($who)) {
            if (isset($who['items']) && is_array($who['items'])) {
                $whoItemsCount = count($who['items']);
            } elseif (isset($who[0])) {
                $whoItemsCount = count(array_filter($who, 'is_numeric', ARRAY_FILTER_USE_KEY));
            }
        }
    @endphp
    let suitableItemIndex = {{ $whoItemsCount }};
    @php
        $priorityItemsCount = 0;
        if (old('priority.items')) {
            $priorityItemsCount = count(old('priority.items'));
        } elseif (old('priority') && !isset(old('priority')['title'])) {
            $priorityItemsCount = count(array_filter(old('priority'), 'is_numeric', ARRAY_FILTER_USE_KEY));
        } elseif (isset($priority) && is_array($priority)) {
            if (isset($priority['items']) && is_array($priority['items'])) {
                $priorityItemsCount = count($priority['items']);
            } elseif (isset($priority[0])) {
                $priorityItemsCount = count(array_filter($priority, 'is_numeric', ARRAY_FILTER_USE_KEY));
            }
        }
    @endphp
    let advantageItemIndex = {{ $priorityItemsCount }};
    let whatLearnCategoryIndex = {{ count(old('learn.items', (isset($learn) && isset($learn['items']) && is_array($learn['items'])) ? $learn['items'] : [])) }};
    let whatLearnItemIndexes = {}; // Object to track item indexes per category
    
    // Khởi tạo whatLearnItemIndexes cho các category đã có sẵn
    @if(old('learn.items'))
        @foreach(old('learn.items') as $categoryIndex => $category)
            @php
                $itemCount = 0;
                if (isset($category['items']) && is_array($category['items'])) {
                    $itemCount = count($category['items']);
                }
            @endphp
            whatLearnItemIndexes[{{ $categoryIndex }}] = {{ $itemCount }};
        @endforeach
    @elseif(isset($learn) && isset($learn['items']) && is_array($learn['items']) && count($learn['items']) > 0)
        @foreach($learn['items'] as $categoryIndex => $category)
            @php
                $itemCount = 0;
                if (is_array($category) && isset($category['items']) && is_array($category['items'])) {
                    $itemCount = count($category['items']);
                }
            @endphp
            whatLearnItemIndexes[{{ $categoryIndex }}] = {{ $itemCount }};
        @endforeach
    @endif
    let careerTagIndex = {{ count(old('chance.tags', (isset($chance) && isset($chance['tags']) && is_array($chance['tags'])) ? $chance['tags'] : [])) }};
    let careerJobIndex = {{ count(old('chance.job', (isset($chance) && isset($chance['job']) && is_array($chance['job'])) ? $chance['job'] : [])) }};
    let degreeValueItemIndex = {{ count(old('value.items', (isset($value) && isset($value['items']) && is_array($value['items'])) ? $value['items'] : [])) }};
    let studentFeedbackIndex = {{ count(old('feedback.items', (isset($feedback) && isset($feedback['items']) && is_array($feedback['items'])) ? $feedback['items'] : [])) }};
    
    // Add feature link
    document.getElementById('addFeatureBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('featuresContainer');
        const featureHtml = `
            <div class="feature-item mb15" data-index="${featureIndex}">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="form-row">
                            <label class="control-label text-left">Tên tính năng</label>
                            <input 
                                type="text"
                                name="feature[${featureIndex}][name]"
                                class="form-control"
                                placeholder="Nhập tên tính năng"
                                required
                            >
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-row">
                            <label class="control-label text-left">Hình ảnh</label>
                            <input 
                                type="text"
                                name="feature[${featureIndex}][image]"
                                class="form-control upload-image"
                                placeholder="URL hình ảnh"
                            >
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-row">
                            <label class="control-label text-left">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm removeFeatureBtn" style="width: 100%;">
                                <i class="fa fa-trash"></i> Xóa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', featureHtml);
        featureIndex++;
    });

    // Add admission target
    document.getElementById('addAdmissionTargetBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('admissionTargetsContainer');
        const targetHtml = `
            <div class="admission-target-item mb15" data-index="${admissionTargetIndex}">
                <div class="row">
                    <div class="col-lg-10">
                        <div class="form-row">
                            <input 
                                type="text"
                                name="target[]"
                                class="form-control"
                                placeholder="Nhập đối tượng tuyển sinh"
                            >
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-row">
                            <button type="button" class="btn btn-danger btn-sm removeAdmissionTargetBtn" style="width: 100%;">
                                <i class="fa fa-trash"></i> Xóa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', targetHtml);
        admissionTargetIndex++;
    });

    // Add reception place
    document.getElementById('addReceptionPlaceBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('receptionPlacesContainer');
        const placeHtml = `
            <div class="reception-place-item mb15" data-index="${receptionPlaceIndex}">
                <div class="row mb10">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <input 
                                type="text"
                                name="address[${receptionPlaceIndex}][name]"
                                class="form-control"
                                placeholder="Nhập nơi nhận"
                            >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-10">
                        <div class="form-row">
                            <input 
                                type="text"
                                name="address[${receptionPlaceIndex}][address]"
                                class="form-control"
                                placeholder="Nhập địa chỉ"
                            >
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-row">
                            <button type="button" class="btn btn-danger btn-sm removeReceptionPlaceBtn" style="width: 100%;">
                                <i class="fa fa-trash"></i> Xóa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', placeHtml);
        receptionPlaceIndex++;
    });

    // Add overview item
    document.getElementById('addOverviewItemBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('overviewItemsContainer');
        const itemHtml = `
            <div class="overview-item mb15" data-index="${overviewItemIndex}">
                <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <div class="row mb15">
                        <div class="col-lg-12">
                            <div class="form-row">
                                <label class="control-label text-left">Ảnh</label>
                                <input 
                                    type="text"
                                    name="overview[items][${overviewItemIndex}][image]"
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
                                <label class="control-label text-left">Tiêu đề</label>
                                <input 
                                    type="text"
                                    name="overview[items][${overviewItemIndex}][name]"
                                    class="form-control"
                                    placeholder="Nhập tiêu đề"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="row mb15">
                        <div class="col-lg-11">
                            <div class="form-row">
                                <label class="control-label text-left">Mô tả</label>
                                <textarea 
                                    name="overview[items][${overviewItemIndex}][description]"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Nhập mô tả"
                                ></textarea>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-row">
                                <label class="control-label text-left">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm removeOverviewItemBtn" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
        overviewItemIndex++;
    });

    // Add suitable item
    document.getElementById('addSuitableItemBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('suitableItemsContainer');
        const itemHtml = `
            <div class="suitable-item mb15" data-index="${suitableItemIndex}">
                <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <div class="row mb15">
                        <div class="col-lg-12">
                            <div class="form-row">
                                <label class="control-label text-left">Hình ảnh</label>
                                <input 
                                    type="text"
                                    name="who[${suitableItemIndex}][image]"
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
                                <label class="control-label text-left">Tiêu đề</label>
                                <input 
                                    type="text"
                                    name="who[${suitableItemIndex}][name]"
                                    class="form-control"
                                    placeholder="Nhập tiêu đề"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="row mb15">
                        <div class="col-lg-11">
                            <div class="form-row">
                                <label class="control-label text-left">Nội dung</label>
                                <textarea 
                                    name="who[${suitableItemIndex}][description]"
                                    class="ck-editor suitable-content-editor"
                                    id="ckSuitableContent${suitableItemIndex}"
                                    data-height="200"
                                ></textarea>
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
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-row">
                                <label class="control-label text-left">Người phù hợp</label>
                                <input 
                                    type="text"
                                    name="who[${suitableItemIndex}][person]"
                                    class="form-control"
                                    placeholder="Nhập người phù hợp"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
        
        // Initialize CKEditor for the new textarea
        if (typeof CKEDITOR !== 'undefined') {
            const editorId = 'ckSuitableContent' + suitableItemIndex;
            CKEDITOR.replace(editorId, {
                height: 200,
                filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
                filebrowserImageBrowseUrl: '/ckfinder/ckfinder.html?type=Images',
                filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
                filebrowserImageUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'
            });
        }
        
        suitableItemIndex++;
    });

    // Add advantage item
    document.getElementById('addAdvantageItemBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('advantageItemsContainer');
        const itemHtml = `
            <div class="advantage-item mb15" data-index="${advantageItemIndex}">
                <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <div class="row mb15">
                        <div class="col-lg-11">
                            <div class="form-row">
                                <label class="control-label text-left">Tiêu đề</label>
                                <input 
                                    type="text"
                                    name="priority[items][${advantageItemIndex}][name]"
                                    class="form-control"
                                    placeholder="Nhập tiêu đề"
                                >
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-row">
                                <label class="control-label text-left">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm removeAdvantageItemBtn" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mb15">
                        <div class="col-lg-12">
                            <div class="form-row">
                                <label class="control-label text-left">Ảnh</label>
                                <input 
                                    type="text"
                                    name="priority[items][${advantageItemIndex}][image]"
                                    class="form-control upload-image"
                                    placeholder="Chọn ảnh"
                                    readonly
                                >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-row">
                                <label class="control-label text-left">Nội dung</label>
                                <textarea 
                                    name="priority[items][${advantageItemIndex}][description]"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Nhập nội dung"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
        advantageItemIndex++;
    });

    // Add degree value item
    document.getElementById('addDegreeValueItemBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('degreeValueItemsContainer');
        const itemHtml = `
            <div class="degree-value-item mb15" data-item-index="${degreeValueItemIndex}">
                <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="form-row">
                                <label class="control-label text-left">Icon</label>
                                <input 
                                    type="text"
                                    name="value[items][${degreeValueItemIndex}][icon]"
                                    class="form-control upload-image"
                                    placeholder="Chọn icon"
                                    readonly
                                >
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-row">
                                <label class="control-label text-left">Tên giá trị</label>
                                <input 
                                    type="text"
                                    name="value[items][${degreeValueItemIndex}][name]"
                                    class="form-control"
                                    placeholder="Nhập tên giá trị"
                                >
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
        container.insertAdjacentHTML('beforeend', itemHtml);
        degreeValueItemIndex++;
    });

    // Add student feedback
    document.getElementById('addStudentFeedbackBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('studentFeedbacksContainer');
        const feedbackHtml = `
            <div class="student-feedback-item mb15" data-feedback-index="${studentFeedbackIndex}">
                <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <div class="row mb15">
                        <div class="col-lg-3">
                            <div class="form-row">
                                <label class="control-label text-left">Ảnh đại diện</label>
                                <input 
                                    type="text"
                                    name="feedback[items][${studentFeedbackIndex}][image]"
                                    class="form-control upload-image"
                                    placeholder="Chọn ảnh"
                                    readonly
                                >
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-row">
                                <label class="control-label text-left">Tên</label>
                                <input 
                                    type="text"
                                    name="feedback[items][${studentFeedbackIndex}][name]"
                                    class="form-control"
                                    placeholder="Nhập tên"
                                >
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-row">
                                <label class="control-label text-left">Chức vụ</label>
                                <input 
                                    type="text"
                                    name="feedback[items][${studentFeedbackIndex}][position]"
                                    class="form-control"
                                    placeholder="Nhập chức vụ"
                                >
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
                                <textarea 
                                    name="feedback[items][${studentFeedbackIndex}][description]"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Nhập mô tả"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', feedbackHtml);
        studentFeedbackIndex++;
    });

    // Add what learn category
    document.getElementById('addWhatLearnCategoryBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('whatLearnCategoriesContainer');
        const categoryHtml = `
            <div class="what-learn-category mb15" data-category-index="${whatLearnCategoryIndex}">
                <div class="ibox" style="background: #f0f0f0; padding: 15px; border-radius: 5px; border: 2px solid #ddd;">
                    <div class="row mb15">
                        <div class="col-lg-11">
                            <div class="form-row">
                                <label class="control-label text-left">Tiêu đề mục</label>
                                <input 
                                    type="text"
                                    name="learn[items][${whatLearnCategoryIndex}][name]"
                                    class="form-control"
                                    placeholder="Nhập tiêu đề mục"
                                >
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-row">
                                <label class="control-label text-left">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm removeWhatLearnCategoryBtn" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mb15">
                        <div class="col-lg-12">
                            <div class="form-row">
                                <h6>
                                    Danh sách bài 
                                    <a href="javascript:void(0)" class="addWhatLearnItemBtn" data-category-index="${whatLearnCategoryIndex}" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm bài</a>
                                    <a href="javascript:void(0)" class="toggle-learn-items-btn" data-category-index="${whatLearnCategoryIndex}" style="margin-left: 10px; color: #1ab394;">
                                        <i class="fa fa-chevron-down"></i> <span class="toggle-text">Thu gọn</span>
                                    </a>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="what-learn-items-container sortable-items" data-category-index="${whatLearnCategoryIndex}" style="overflow: visible; max-height: none;">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', categoryHtml);
        whatLearnItemIndexes[whatLearnCategoryIndex] = 0;
        
        // Khởi tạo sortable cho category mới
        setTimeout(function() {
            initSortableForCategory(whatLearnCategoryIndex);
        }, 200);
        
        whatLearnCategoryIndex++;
    });

    // Add what learn item (using event delegation)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.addWhatLearnItemBtn')) {
            e.preventDefault();
            const btn = e.target.closest('.addWhatLearnItemBtn');
            const categoryIndex = btn.getAttribute('data-category-index');
            const container = btn.closest('.what-learn-category').querySelector('.what-learn-items-container');
            
            // Khởi tạo index nếu chưa có, hoặc tính lại dựa trên số items hiện có
            if (!whatLearnItemIndexes[categoryIndex]) {
                const existingItems = container.querySelectorAll('.what-learn-item');
                whatLearnItemIndexes[categoryIndex] = existingItems.length;
            }
            
            const itemIndex = whatLearnItemIndexes[categoryIndex];
            const itemHtml = `
                <div class="what-learn-item mb10" data-category-index="${categoryIndex}" data-item-index="${itemIndex}" style="cursor: move;">
                    <div class="ibox" style="background: #f9f9f9; padding: 10px; border-radius: 3px; margin-left: 20px; position: relative;">
                        <div class="row mb10" style="cursor: pointer;" onclick="toggleLearnItemContent(this)">
                            <div class="col-lg-11">
                                <div class="form-row">
                                    <label class="control-label text-left">
                                        <i class="fa fa-grip-vertical" style="color: #999; margin-right: 5px;"></i>
                                        <i class="fa fa-chevron-down toggle-item-icon" style="color: #1ab394; margin-right: 5px;"></i>
                                        Tên
                                    </label>
                                    <input 
                                        type="text"
                                        name="learn[items][${categoryIndex}][items][${itemIndex}][name]"
                                        class="form-control learn-item-name"
                                        placeholder="Nhập tên"
                                        onclick="event.stopPropagation();"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-row">
                                    <label class="control-label text-left">&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm removeWhatLearnItemBtn" style="width: 100%;" onclick="event.stopPropagation();">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="learn-item-content">
                            <div class="row mb10">
                                <div class="col-lg-12">
                                    <div class="form-row">
                                        <label class="control-label text-left">Hình ảnh</label>
                                        <input 
                                            type="text"
                                            name="learn[items][${categoryIndex}][items][${itemIndex}][image]"
                                            class="form-control upload-image"
                                            placeholder="Chọn ảnh"
                                            readonly
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-row">
                                        <label class="control-label text-left">Mô tả</label>
                                        <textarea 
                                            name="learn[items][${categoryIndex}][items][${itemIndex}][description]"
                                            class="form-control"
                                            rows="3"
                                            placeholder="Nhập mô tả"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
            whatLearnItemIndexes[categoryIndex]++;
            
            // Khởi tạo lại sortable cho container này
            setTimeout(function() {
                initSortableForCategory(categoryIndex);
            }, 100);
        }
    });
    
    // Function để toggle collapse/expand item
    window.toggleLearnItemContent = function(element) {
        const item = element.closest('.what-learn-item');
        const content = item.querySelector('.learn-item-content');
        const icon = element.querySelector('.toggle-item-icon');
        
        if (content) {
            content.classList.toggle('collapsed');
            item.classList.toggle('collapsed');
            if (content.classList.contains('collapsed')) {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    };
    
    // Function để khởi tạo sortable cho một category
    function initSortableForCategory(categoryIndex) {
        const container = document.querySelector(`.what-learn-items-container[data-category-index="${categoryIndex}"]`);
        if (!container) return;
        
        // Kiểm tra jQuery UI sortable đã load chưa
        if (typeof $ === 'undefined' || typeof $.fn.sortable === 'undefined') {
            console.warn('jQuery UI sortable not loaded');
            return;
        }
        
        // Destroy sortable cũ nếu đã có
        if ($(container).hasClass('ui-sortable')) {
            $(container).sortable('destroy');
        }
        
        // Khởi tạo sortable mới
        $(container).sortable({
            items: '.what-learn-item',
            handle: '.fa-grip-vertical, .learn-item-name',
            cursor: 'move',
            opacity: 0.8,
            tolerance: 'pointer',
            axis: 'y',
            placeholder: 'ui-state-highlight',
            cancel: 'input, textarea, button',
            update: function(event, ui) {
                // Cập nhật lại index và name attribute sau khi sort
                const categoryIdx = container.getAttribute('data-category-index');
                const items = container.querySelectorAll('.what-learn-item');
                items.forEach(function(item, newIndex) {
                    item.setAttribute('data-item-index', newIndex);
                    // Cập nhật tất cả input/textarea trong item
                    const inputs = item.querySelectorAll('input, textarea');
                    inputs.forEach(function(input) {
                        const name = input.getAttribute('name');
                        if (name) {
                            // Thay thế index cũ bằng index mới
                            const newName = name.replace(
                                /learn\[items\]\[(\d+)\]\[items\]\[(\d+)\]/,
                                `learn[items][${categoryIdx}][items][${newIndex}]`
                            );
                            input.setAttribute('name', newName);
                        }
                    });
                });
            }
        });
    }
    
    // Khởi tạo sortable cho tất cả category khi page load
    setTimeout(function() {
        document.querySelectorAll('.what-learn-items-container').forEach(function(container) {
            const categoryIndex = container.getAttribute('data-category-index');
            if (categoryIndex) {
                initSortableForCategory(categoryIndex);
            }
        });
    }, 500);

    // Add career tag
    document.getElementById('addCareerTagBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('careerTagsContainer');
        const tagHtml = `
            <div class="career-tag-item mb15" data-tag-index="${careerTagIndex}">
                <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <div class="row mb15">
                        <div class="col-lg-4">
                            <div class="form-row">
                                <label class="control-label text-left">Icon</label>
                                <input 
                                    type="text"
                                    name="chance[tags][${careerTagIndex}][icon]"
                                    class="form-control upload-image"
                                    placeholder="Chọn icon"
                                    readonly
                                >
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-row">
                                <label class="control-label text-left">Text</label>
                                <input 
                                    type="text"
                                    name="chance[tags][${careerTagIndex}][name]"
                                    class="form-control"
                                    placeholder="Nhập text"
                                >
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-row">
                                <label class="control-label text-left">Màu</label>
                                <input 
                                    type="color"
                                    name="chance[tags][${careerTagIndex}][color]"
                                    value="#000000"
                                    class="form-control"
                                    style="height: 38px;"
                                >
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-row">
                                <label class="control-label text-left">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm removeCareerTagBtn" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', tagHtml);
        careerTagIndex++;
    });

    // Add career job
    document.getElementById('addCareerJobBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('careerJobsContainer');
        const jobHtml = `
            <div class="career-job-item mb15" data-job-index="${careerJobIndex}">
                <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <div class="row mb15">
                        <div class="col-lg-3">
                            <div class="form-row">
                                <label class="control-label text-left">Icon</label>
                                <input 
                                    type="text"
                                    name="chance[job][${careerJobIndex}][image]"
                                    class="form-control upload-image"
                                    placeholder="Chọn icon"
                                    readonly
                                >
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-row">
                                <label class="control-label text-left">Tên nghề</label>
                                <input 
                                    type="text"
                                    name="chance[job][${careerJobIndex}][name]"
                                    class="form-control"
                                    placeholder="Nhập tên nghề"
                                >
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-row">
                                <label class="control-label text-left">Mô tả</label>
                                <input 
                                    type="text"
                                    name="chance[job][${careerJobIndex}][description]"
                                    class="form-control"
                                    placeholder="Nhập mô tả"
                                >
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-row">
                                <label class="control-label text-left">Lương</label>
                                <input 
                                    type="text"
                                    name="chance[job][${careerJobIndex}][salary]"
                                    class="form-control"
                                    placeholder="Nhập lương"
                                >
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-row">
                                <label class="control-label text-left">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm removeCareerJobBtn" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', jobHtml);
        careerJobIndex++;
    });

    // Remove buttons - using event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeFeatureBtn')) {
            e.preventDefault();
            e.target.closest('.feature-item').remove();
        }
        if (e.target.closest('.removeAdmissionTargetBtn')) {
            e.preventDefault();
            e.target.closest('.admission-target-item').remove();
        }
        if (e.target.closest('.removeReceptionPlaceBtn')) {
            e.preventDefault();
            e.target.closest('.reception-place-item').remove();
        }
        if (e.target.closest('.removeOverviewItemBtn')) {
            e.preventDefault();
            e.target.closest('.overview-item').remove();
        }
        if (e.target.closest('.removeSuitableItemBtn')) {
            e.preventDefault();
            const item = e.target.closest('.suitable-item');
            const editorId = item.querySelector('.suitable-content-editor')?.id;
            if (editorId && typeof CKEDITOR !== 'undefined' && CKEDITOR.instances[editorId]) {
                CKEDITOR.instances[editorId].destroy();
            }
            item.remove();
        }
        if (e.target.closest('.removeAdvantageItemBtn')) {
            e.preventDefault();
            e.target.closest('.advantage-item').remove();
        }
        if (e.target.closest('.removeWhatLearnCategoryBtn')) {
            e.preventDefault();
            const category = e.target.closest('.what-learn-category');
            const categoryIndex = category.getAttribute('data-category-index');
            delete whatLearnItemIndexes[categoryIndex];
            category.remove();
        }
        if (e.target.closest('.removeWhatLearnItemBtn')) {
            e.preventDefault();
            const item = e.target.closest('.what-learn-item');
            const container = item.closest('.what-learn-items-container');
            const categoryIndex = container ? container.getAttribute('data-category-index') : null;
            
            item.remove();
            
            // Cập nhật lại index và name attribute sau khi xóa
            if (container && categoryIndex) {
                const items = container.querySelectorAll('.what-learn-item');
                items.forEach(function(remainingItem, newIndex) {
                    remainingItem.setAttribute('data-item-index', newIndex);
                    const inputs = remainingItem.querySelectorAll('input, textarea');
                    inputs.forEach(function(input) {
                        const name = input.getAttribute('name');
                        if (name) {
                            const newName = name.replace(
                                /learn\[items\]\[(\d+)\]\[items\]\[(\d+)\]/,
                                `learn[items][${categoryIndex}][items][${newIndex}]`
                            );
                            input.setAttribute('name', newName);
                        }
                    });
                });
                // Cập nhật whatLearnItemIndexes
                whatLearnItemIndexes[categoryIndex] = items.length;
            }
        }
        // Toggle learn items container
        if (e.target.closest('.toggle-learn-items-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.toggle-learn-items-btn');
            const categoryIndex = btn.getAttribute('data-category-index');
            const container = document.querySelector(`.what-learn-items-container[data-category-index="${categoryIndex}"]`);
            const toggleText = btn.querySelector('.toggle-text');
            const toggleIcon = btn.querySelector('i');
            
            if (container) {
                if (container.classList.contains('collapsed')) {
                    container.classList.remove('collapsed');
                    toggleText.textContent = 'Thu gọn';
                    toggleIcon.classList.remove('fa-chevron-up');
                    toggleIcon.classList.add('fa-chevron-down');
                    btn.classList.remove('collapsed');
                } else {
                    container.classList.add('collapsed');
                    toggleText.textContent = 'Mở rộng';
                    toggleIcon.classList.remove('fa-chevron-down');
                    toggleIcon.classList.add('fa-chevron-up');
                    btn.classList.add('collapsed');
                }
            }
        }
        if (e.target.closest('.removeCareerTagBtn')) {
            e.preventDefault();
            e.target.closest('.career-tag-item').remove();
        }
        if (e.target.closest('.removeCareerJobBtn')) {
            e.preventDefault();
            e.target.closest('.career-job-item').remove();
        }
        if (e.target.closest('.removeDegreeValueItemBtn')) {
            e.preventDefault();
            e.target.closest('.degree-value-item').remove();
        }
        if (e.target.closest('.removeStudentFeedbackBtn')) {
            e.preventDefault();
            e.target.closest('.student-feedback-item').remove();
        }
    });

    // Initialize Select2 for event post catalogue
    $(document).ready(function() {
        setTimeout(function() {
            var $select = $('#event_post_catalogue_id');
            if ($select.length === 0) {
                console.warn('Event post catalogue select not found');
                return;
            }
            
            if (typeof $.fn.select2 !== 'undefined') {
                // Destroy select2 cũ nếu đã có
                if ($select.hasClass('select2-hidden-accessible')) {
                    try {
                        $select.select2('destroy');
                    } catch(e) {
                        console.warn('Error destroying select2:', e);
                    }
                }
                // Khởi tạo select2 mới
                $select.select2({
                    placeholder: 'Chọn chuyên mục',
                    allowClear: true,
                    width: '100%'
                });
                console.log('Select2 initialized for event post catalogue');
            } else {
                console.warn('Select2 library not loaded');
            }
        }, 1000);
    });

    // Switchery đã được khởi tạo bởi library.js
    // Chỉ cần thêm event handler để cập nhật hidden input
    $(document).on('change', '.js-switch', function() {
        var hiddenInput = $(this).siblings('input[type="hidden"]');
        if (hiddenInput.length) {
            hiddenInput.val(this.checked ? 2 : 1);
        }
    });

    // Xử lý toggle collapse/expand cho các container items
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

