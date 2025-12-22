{{-- Filter Sidebar --}}
<div class="schools-filter-sidebar">
    <form method="GET" action="{{ write_url('cac-nganh-dao-tao-tu-xa') }}" id="major-filter-form">
        {{-- Nhóm Ngành --}}
        @if(isset($majorCatalogues) && $majorCatalogues->count() > 0)
            <div class="filter-group">
                <h3 class="filter-heading">Nhóm Ngành</h3>
                <div class="filter-body">
                    @php
                        $visibleItems = $majorCatalogues->slice(0, 5);
                        $hiddenItems = $majorCatalogues->slice(5);
                        $hasMore = $hiddenItems->count() > 0;
                    @endphp
                    
                    @foreach($visibleItems as $catalogue)
                        @php
                            $catalogueLanguage = $catalogue->languages->first();
                            $catalogueName = $catalogueLanguage && $catalogueLanguage->pivot ? ($catalogueLanguage->pivot->name ?? '') : '';
                            $isChecked = false;
                            if (request('catalogue_id')) {
                                $requestIds = is_array(request('catalogue_id')) ? request('catalogue_id') : [request('catalogue_id')];
                                $isChecked = in_array($catalogue->id, $requestIds);
                            }
                        @endphp
                        @if($catalogueName)
                            <div class="filter-choose">
                                <input 
                                    type="checkbox" 
                                    id="catalogue-{{ $catalogue->id }}"
                                    name="catalogue_id[]" 
                                    value="{{ $catalogue->id }}"
                                    class="input-checkbox filtering filterCatalogue"
                                    {{ $isChecked ? 'checked' : '' }}
                                >
                                <label for="catalogue-{{ $catalogue->id }}">{{ $catalogueName }} ({{ $catalogue->majors_count }})</label>
                            </div>
                        @endif
                    @endforeach
                    
                    @if($hasMore)
                        <div class="filter-items-hidden" id="catalogue-hidden" style="display: none;">
                            @foreach($hiddenItems as $catalogue)
                                @php
                                    $catalogueLanguage = $catalogue->languages->first();
                                    $catalogueName = $catalogueLanguage && $catalogueLanguage->pivot ? ($catalogueLanguage->pivot->name ?? '') : '';
                                    $isChecked = false;
                                    if (request('catalogue_id')) {
                                        $requestIds = is_array(request('catalogue_id')) ? request('catalogue_id') : [request('catalogue_id')];
                                        $isChecked = in_array($catalogue->id, $requestIds);
                                    }
                                @endphp
                                @if($catalogueName)
                                    <div class="filter-choose">
                                        <input 
                                            type="checkbox" 
                                            id="catalogue-hidden-{{ $catalogue->id }}"
                                            name="catalogue_id[]" 
                                            value="{{ $catalogue->id }}"
                                            class="input-checkbox filtering filterCatalogue"
                                            {{ $isChecked ? 'checked' : '' }}
                                        >
                                        <label for="catalogue-hidden-{{ $catalogue->id }}">{{ $catalogueName }} ({{ $catalogue->majors_count }})</label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="filter-toggle">
                            <a href="#" class="filter-toggle-link" data-target="catalogue-hidden">
                                <i class="fa fa-chevron-down"></i>
                                <span>Xem thêm ({{ $hiddenItems->count() }})</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- Trường --}}
        @if(isset($schools) && $schools->count() > 0)
            <div class="filter-group">
                <h3 class="filter-heading">Trường</h3>
                <div class="filter-body">
                    @php
                        $visibleItems = $schools->slice(0, 5);
                        $hiddenItems = $schools->slice(5);
                        $hasMore = $hiddenItems->count() > 0;
                    @endphp
                    
                    @foreach($visibleItems as $school)
                        @php
                            $schoolLanguage = $school->languages->first();
                            $schoolName = $schoolLanguage && $schoolLanguage->pivot ? ($schoolLanguage->pivot->name ?? '') : '';
                            $schoolShortName = $school->short_name ?? '';
                            $isChecked = false;
                            if (request('school_id')) {
                                $requestIds = is_array(request('school_id')) ? request('school_id') : [request('school_id')];
                                $isChecked = in_array($school->id, $requestIds);
                            }
                        @endphp
                        @if($schoolName)
                            <div class="filter-choose">
                                <input 
                                    type="checkbox" 
                                    id="school-{{ $school->id }}"
                                    name="school_id[]" 
                                    value="{{ $school->id }}"
                                    class="input-checkbox filtering filterSchool"
                                    {{ $isChecked ? 'checked' : '' }}
                                >
                                <label for="school-{{ $school->id }}">
                                    @if($schoolShortName)
                                        <span class="school-short-name">{{ $schoolShortName }}</span>
                                        <span class="school-name-separator">-</span>
                                    @endif
                                    <span class="school-full-name">{{ $schoolName }}</span>
                                </label>
                            </div>
                        @endif
                    @endforeach
                    
                    @if($hasMore)
                        <div class="filter-items-hidden" id="school-hidden" style="display: none;">
                            @foreach($hiddenItems as $school)
                                @php
                                    $schoolLanguage = $school->languages->first();
                                    $schoolName = $schoolLanguage && $schoolLanguage->pivot ? ($schoolLanguage->pivot->name ?? '') : '';
                                    $schoolShortName = $school->short_name ?? '';
                                    $isChecked = false;
                                    if (request('school_id')) {
                                        $requestIds = is_array(request('school_id')) ? request('school_id') : [request('school_id')];
                                        $isChecked = in_array($school->id, $requestIds);
                                    }
                                @endphp
                                @if($schoolName)
                                    <div class="filter-choose">
                                        <input 
                                            type="checkbox" 
                                            id="school-hidden-{{ $school->id }}"
                                            name="school_id[]" 
                                            value="{{ $school->id }}"
                                            class="input-checkbox filtering filterSchool"
                                            {{ $isChecked ? 'checked' : '' }}
                                        >
                                        <label for="school-hidden-{{ $school->id }}">
                                            @if($schoolShortName)
                                                <span class="school-short-name">{{ $schoolShortName }}</span>
                                                <span class="school-name-separator">-</span>
                                            @endif
                                            <span class="school-full-name">{{ $schoolName }}</span>
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="filter-toggle">
                            <a href="#" class="filter-toggle-link" data-target="school-hidden">
                                <i class="fa fa-chevron-down"></i>
                                <span>Xem thêm ({{ $hiddenItems->count() }})</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- Đối Tượng Tuyển Sinh --}}
        @if(isset($filterOptions['admission_subject']) && count($filterOptions['admission_subject']) > 0)
            <div class="filter-group">
                <h3 class="filter-heading">Đối Tượng Tuyển Sinh</h3>
                <div class="filter-body">
                    @php
                        $admissionSubjects = $filterOptions['admission_subject'];
                        $visibleItems = array_slice($admissionSubjects, 0, 5);
                        $hiddenItems = array_slice($admissionSubjects, 5);
                        $hasMore = count($hiddenItems) > 0;
                    @endphp
                    
                    @foreach($visibleItems as $index => $subject)
                        <div class="filter-choose">
                            <input 
                                type="checkbox" 
                                id="admission-{{ $index }}"
                                name="admission_subject[]" 
                                value="{{ $subject }}"
                                class="input-checkbox filtering filterAdmission"
                                {{ in_array($subject, $selectedFilters['admission_subject'] ?? []) ? 'checked' : '' }}
                            >
                            <label for="admission-{{ $index }}">{{ $subject }}</label>
                        </div>
                    @endforeach
                    
                    @if($hasMore)
                        <div class="filter-items-hidden" id="admission-subject-hidden" style="display: none;">
                            @foreach($hiddenItems as $index => $subject)
                                <div class="filter-choose">
                                    <input 
                                        type="checkbox" 
                                        id="admission-hidden-{{ $index }}"
                                        name="admission_subject[]" 
                                        value="{{ $subject }}"
                                        class="input-checkbox filtering filterAdmission"
                                        {{ in_array($subject, $selectedFilters['admission_subject'] ?? []) ? 'checked' : '' }}
                                    >
                                    <label for="admission-hidden-{{ $index }}">{{ $subject }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="filter-toggle">
                            <a href="#" class="filter-toggle-link" data-target="admission-subject-hidden">
                                <i class="fa fa-chevron-down"></i>
                                <span>Xem thêm ({{ count($hiddenItems) }})</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- Địa Điểm Thi --}}
        @if(isset($filterOptions['exam_location']) && count($filterOptions['exam_location']) > 0)
            <div class="filter-group">
                <h3 class="filter-heading">Địa Điểm Thi</h3>
                <div class="filter-body">
                    @php
                        $examLocations = $filterOptions['exam_location'];
                        $visibleItems = array_slice($examLocations, 0, 5);
                        $hiddenItems = array_slice($examLocations, 5);
                        $hasMore = count($hiddenItems) > 0;
                    @endphp
                    
                    @foreach($visibleItems as $index => $location)
                        <div class="filter-choose">
                            <input 
                                type="checkbox" 
                                id="location-{{ $index }}"
                                name="exam_location[]" 
                                value="{{ $location }}"
                                class="input-checkbox filtering filterLocation"
                                {{ in_array($location, $selectedFilters['exam_location'] ?? []) ? 'checked' : '' }}
                            >
                            <label for="location-{{ $index }}">{{ $location }}</label>
                        </div>
                    @endforeach
                    
                    @if($hasMore)
                        <div class="filter-items-hidden" id="exam-location-hidden" style="display: none;">
                            @foreach($hiddenItems as $index => $location)
                                <div class="filter-choose">
                                    <input 
                                        type="checkbox" 
                                        id="location-hidden-{{ $index }}"
                                        name="exam_location[]" 
                                        value="{{ $location }}"
                                        class="input-checkbox filtering filterLocation"
                                        {{ in_array($location, $selectedFilters['exam_location'] ?? []) ? 'checked' : '' }}
                                    >
                                    <label for="location-hidden-{{ $index }}">{{ $location }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="filter-toggle">
                            <a href="#" class="filter-toggle-link" data-target="exam-location-hidden">
                                <i class="fa fa-chevron-down"></i>
                                <span>Xem thêm ({{ count($hiddenItems) }})</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        <div class="filter-actions">
            <a href="{{ write_url('cac-nganh-dao-tao-tu-xa') }}" class="btn btn-default" style="width: 100%; display: block; text-align: center;">Xóa bộ lọc</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle show/hide cho filter items
    document.querySelectorAll('.filter-toggle-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const hiddenItems = document.getElementById(targetId);
            const icon = this.querySelector('i');
            const text = this.querySelector('span');
            
            if (hiddenItems) {
                if (hiddenItems.style.display === 'none') {
                    hiddenItems.style.display = 'block';
                    icon.className = 'fa fa-chevron-up';
                    text.textContent = text.textContent.replace('Xem thêm', 'Thu gọn');
                    link.classList.add('expanded');
                } else {
                    hiddenItems.style.display = 'none';
                    icon.className = 'fa fa-chevron-down';
                    text.textContent = text.textContent.replace('Thu gọn', 'Xem thêm');
                    link.classList.remove('expanded');
                }
            }
        });
    });
    
    // Function để filter majors
    function filterMajors() {
        const filterForm = document.getElementById('major-filter-form');
        if (!filterForm) return;
        
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        
        // Lấy tất cả checked checkboxes
        formData.getAll('catalogue_id[]').forEach(function(val) {
            params.append('catalogue_id[]', val);
        });
        formData.getAll('school_id[]').forEach(function(val) {
            params.append('school_id[]', val);
        });
        formData.getAll('admission_subject[]').forEach(function(val) {
            params.append('admission_subject[]', val);
        });
        formData.getAll('exam_location[]').forEach(function(val) {
            params.append('exam_location[]', val);
        });
        
        // Hiển thị loading
        const majorsGrid = document.querySelector('.majors-list-grid');
        const majorsPagination = document.querySelector('.major-catalogue-pagination');
        if (majorsGrid) {
            majorsGrid.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fa fa-spinner fa-spin fa-2x" style="color: #008DC2;"></i><p style="margin-top: 15px;">Đang tải...</p></div>';
        }
        if (majorsPagination) {
            majorsPagination.style.display = 'none';
        }
        
        // Gửi AJAX request
        fetch('{{ route("ajax.major.filter") }}?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Cập nhật content
            if (majorsGrid) {
                majorsGrid.innerHTML = data.data || '<div class="no-majors-message" style="text-align: center; padding: 60px 20px;"><p style="font-size: 18px; color: #666;">Không tìm thấy ngành học nào phù hợp với bộ lọc.</p></div>';
            }
            
            // Cập nhật pagination
            if (majorsPagination && data.pagination) {
                majorsPagination.innerHTML = data.pagination;
                majorsPagination.style.display = 'block';
            } else if (majorsPagination) {
                majorsPagination.style.display = 'none';
            }
            
            // Không scroll lên đầu trang - giữ nguyên vị trí hiện tại
            // window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Cập nhật URL mà không reload trang
            const newUrl = '{{ write_url("cac-nganh-dao-tao-tu-xa") }}' + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({ path: newUrl }, '', newUrl);
        })
        .catch(error => {
            console.error('Error:', error);
            if (majorsGrid) {
                majorsGrid.innerHTML = '<div class="no-majors-message" style="text-align: center; padding: 60px 20px;"><p style="font-size: 18px; color: #666;">Có lỗi xảy ra. Vui lòng thử lại.</p></div>';
            }
        });
    }
    
    // Auto filter khi click vào checkbox
    document.querySelectorAll('.input-checkbox.filtering').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            // Debounce để tránh gọi quá nhiều request
            clearTimeout(window.majorFilterTimeout);
            window.majorFilterTimeout = setTimeout(function() {
                filterMajors();
            }, 300); // Delay 300ms
        });
    });
});
</script>

