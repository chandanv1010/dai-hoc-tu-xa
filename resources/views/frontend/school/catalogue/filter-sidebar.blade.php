{{-- Filter Sidebar --}}
<div class="schools-filter-sidebar">
    <form method="GET" action="{{ write_url('cac-truong-dao-tao-tu-xa') }}" id="schools-filter-form">
        {{-- Hệ Tốt Nghiệp --}}
        @if(isset($filterOptions['graduation_system']) && count($filterOptions['graduation_system']) > 0)
            <div class="filter-group">
                <h3 class="filter-heading">Hệ Tốt Nghiệp</h3>
                <div class="filter-body">
                    @php
                        $graduationSystems = $filterOptions['graduation_system'];
                        $visibleItems = array_slice($graduationSystems, 0, 5);
                        $hiddenItems = array_slice($graduationSystems, 5);
                        $hasMore = count($hiddenItems) > 0;
                    @endphp
                    
                    @foreach($visibleItems as $index => $system)
                        <div class="filter-choose">
                            <input 
                                type="checkbox" 
                                id="graduation-{{ $index }}"
                                name="graduation_system[]" 
                                value="{{ $system }}"
                                class="input-checkbox filtering filterGraduation"
                                {{ in_array($system, $selectedFilters['graduation_system'] ?? []) ? 'checked' : '' }}
                            >
                            <label for="graduation-{{ $index }}">{{ $system }}</label>
                        </div>
                    @endforeach
                    
                    @if($hasMore)
                        <div class="filter-items-hidden" id="graduation-system-hidden" style="display: none;">
                            @foreach($hiddenItems as $index => $system)
                                <div class="filter-choose">
                                    <input 
                                        type="checkbox" 
                                        id="graduation-hidden-{{ $index }}"
                                        name="graduation_system[]" 
                                        value="{{ $system }}"
                                        class="input-checkbox filtering filterGraduation"
                                        {{ in_array($system, $selectedFilters['graduation_system'] ?? []) ? 'checked' : '' }}
                                    >
                                    <label for="graduation-hidden-{{ $index }}">{{ $system }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="filter-toggle">
                            <a href="#" class="filter-toggle-link" data-target="graduation-system-hidden">
                                <i class="fa fa-chevron-down"></i>
                                <span>Xem thêm ({{ count($hiddenItems) }})</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- Ngành Đào Tạo --}}
        @if(isset($filterOptions['majors']) && $filterOptions['majors']->count() > 0)
            <div class="filter-group">
                <h3 class="filter-heading">Ngành Đào Tạo</h3>
                <div class="filter-body">
                    @php
                        $majors = $filterOptions['majors'];
                        $visibleItems = $majors->slice(0, 5);
                        $hiddenItems = $majors->slice(5);
                        $hasMore = $hiddenItems->count() > 0;
                    @endphp
                    
                    @foreach($visibleItems as $index => $major)
                        <div class="filter-choose">
                            <input 
                                type="checkbox" 
                                id="major-{{ $major->id }}"
                                name="major_id[]" 
                                value="{{ $major->id }}"
                                class="input-checkbox filtering filterMajor"
                                {{ in_array($major->id, $selectedFilters['major_id'] ?? []) ? 'checked' : '' }}
                            >
                            <label for="major-{{ $major->id }}">{{ $major->name }}</label>
                        </div>
                    @endforeach
                    
                    @if($hasMore)
                        <div class="filter-items-hidden" id="majors-hidden" style="display: none;">
                            @foreach($hiddenItems as $major)
                                <div class="filter-choose">
                                    <input 
                                        type="checkbox" 
                                        id="major-hidden-{{ $major->id }}"
                                        name="major_id[]" 
                                        value="{{ $major->id }}"
                                        class="input-checkbox filtering filterMajor"
                                        {{ in_array($major->id, $selectedFilters['major_id'] ?? []) ? 'checked' : '' }}
                                    >
                                    <label for="major-hidden-{{ $major->id }}">{{ $major->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="filter-toggle">
                            <a href="#" class="filter-toggle-link" data-target="majors-hidden">
                                <i class="fa fa-chevron-down"></i>
                                <span>Xem thêm ({{ $hiddenItems->count() }})</span>
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
            <a href="{{ write_url('cac-truong-dao-tao-tu-xa') }}" class="btn btn-default" style="width: 100%; display: block; text-align: center;">Xóa bộ lọc</a>
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
    
    // Function để filter schools
    function filterSchools() {
        const filterForm = document.getElementById('schools-filter-form');
        if (!filterForm) return;
        
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        
        // Lấy tất cả checked checkboxes
        formData.getAll('graduation_system[]').forEach(function(val) {
            params.append('graduation_system[]', val);
        });
        formData.getAll('major_id[]').forEach(function(val) {
            params.append('major_id[]', val);
        });
        formData.getAll('exam_location[]').forEach(function(val) {
            params.append('exam_location[]', val);
        });
        
        // Hiển thị loading
        const schoolsGrid = document.querySelector('.schools-catalogue-grid');
        const schoolsPagination = document.querySelector('.schools-pagination');
        if (schoolsGrid) {
            schoolsGrid.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fa fa-spinner fa-spin fa-2x" style="color: #008DC2;"></i><p style="margin-top: 15px;">Đang tải...</p></div>';
        }
        if (schoolsPagination) {
            schoolsPagination.style.display = 'none';
        }
        
        // Gửi AJAX request
        fetch('{{ route("ajax.school.filter") }}?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Cập nhật content
            if (schoolsGrid) {
                schoolsGrid.innerHTML = data.data || '<div class="schools-empty"><p>Không tìm thấy trường đào tạo từ xa nào phù hợp với bộ lọc.</p></div>';
            }
            
            // Cập nhật pagination
            if (schoolsPagination && data.pagination) {
                schoolsPagination.innerHTML = data.pagination;
                schoolsPagination.style.display = 'block';
            } else if (schoolsPagination) {
                schoolsPagination.style.display = 'none';
            }
            
            // Không scroll lên đầu trang - giữ nguyên vị trí hiện tại
            // window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Cập nhật URL mà không reload trang
            const newUrl = '{{ write_url("cac-truong-dao-tao-tu-xa") }}' + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({ path: newUrl }, '', newUrl);
        })
        .catch(error => {
            console.error('Error:', error);
            if (schoolsGrid) {
                schoolsGrid.innerHTML = '<div class="schools-empty"><p>Có lỗi xảy ra. Vui lòng thử lại.</p></div>';
            }
        });
    }
    
    // Auto filter khi click vào checkbox
    document.querySelectorAll('.input-checkbox.filtering').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            // Debounce để tránh gọi quá nhiều request
            clearTimeout(window.filterTimeout);
            window.filterTimeout = setTimeout(function() {
                filterSchools();
            }, 300); // Delay 300ms
        });
    });
});
</script>

