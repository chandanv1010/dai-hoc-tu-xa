@extends('frontend.homepage.layout')
@section('content')
    <!-- Breadcrumb Section -->
    <div class="page-breadcrumb-large">
        <div class="breadcrumb-overlay"></div>
        <div class="uk-container uk-container-center">
            <div class="breadcrumb-content">
                <h1 class="breadcrumb-title">Các Ngành Đào Tạo Từ Xa</h1>
            </div>
        </div>
    </div>

    <!-- Major Catalogue Content -->
    <div class="panel-majors-list">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                <!-- Left Column: 3/4 -->
                <div class="uk-width-medium-3-4">
                    <div class="majors-list-wrapper">
                        @if($majors->isNotEmpty())
                            <!-- Majors Grid -->
                            <div class="majors-list-grid">
                                <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                                    @foreach($majors as $major)
                                        @include('frontend.component.major-item', ['major' => $major])
                                    @endforeach
                                </div>
                            </div>

                            <!-- Pagination -->
                            @if($majors->hasPages())
                                <div class="major-catalogue-pagination" style="margin-top: 40px; text-align: center;">
                                    {{ $majors->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        @else
                            <div class="no-majors-message" style="text-align: center; padding: 60px 20px;">
                                <p style="font-size: 18px; color: #666;">Không có ngành học nào trong danh mục này.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: 1/4 - Filter Sidebar -->
                <div class="uk-width-medium-1-4">
                    <div class="major-filter-sidebar">
                        <h3 class="filter-sidebar-title">Bộ lọc</h3>
                        <form method="GET" action="{{ url('cac-nganh-dao-tao-tu-xa.html') }}" class="filter-form" id="major-filter-form">
                            <!-- Filter by Major Catalogue (Nhóm ngành) -->
                            <div class="filter-group">
                                <label class="filter-label">Lọc theo nhóm ngành</label>
                                <div class="filter-checkbox-group">
                                    @if($majorCatalogues && $majorCatalogues->count() > 0)
                                        @foreach($majorCatalogues as $catalogue)
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
                                                <label class="filter-checkbox-item">
                                                    <input type="checkbox" name="catalogue_id[]" value="{{ $catalogue->id }}" {{ $isChecked ? 'checked' : '' }}>
                                                    <span class="checkbox-label">{{ $catalogueName }}</span>
                                                </label>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Filter by School -->
                            <div class="filter-group">
                                <label class="filter-label">Lọc theo trường</label>
                                <div class="filter-checkbox-group">
                                    @if($schools && $schools->count() > 0)
                                        @foreach($schools as $school)
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
                                                <label class="filter-checkbox-item">
                                                    <input type="checkbox" name="school_id[]" value="{{ $school->id }}" {{ $isChecked ? 'checked' : '' }}>
                                                    <span class="checkbox-label">
                                                        @if($schoolShortName)
                                                            <span class="school-short-name">{{ $schoolShortName }}</span>
                                                            <span class="school-name-separator">-</span>
                                                        @endif
                                                        <span class="school-full-name">{{ $schoolName }}</span>
                                                    </span>
                                                </label>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Filter by Duration -->
                            <div class="filter-group">
                                <label class="filter-label">Lọc theo số năm đào tạo</label>
                                <div class="filter-checkbox-group">
                                    @if($durations && $durations->count() > 0)
                                        @foreach($durations as $duration)
                                            @php
                                                $isChecked = false;
                                                if (request('duration')) {
                                                    $requestDurations = is_array(request('duration')) ? request('duration') : [request('duration')];
                                                    $isChecked = in_array($duration, $requestDurations);
                                                }
                                            @endphp
                                            <label class="filter-checkbox-item">
                                                <input type="checkbox" name="duration[]" value="{{ $duration }}" {{ $isChecked ? 'checked' : '' }}>
                                                <span class="checkbox-label">{{ $duration }}</span>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Filter Actions -->
                            <div class="filter-actions">
                                <a href="{{ url('cac-nganh-dao-tao-tu-xa.html') }}" class="btn-filter-reset">
                                    <i class="fa fa-refresh"></i>
                                    Xóa bộ lọc
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('major-filter-form');
            const checkboxes = filterForm.querySelectorAll('input[type="checkbox"]');
            
            // Auto-submit khi checkbox thay đổi
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    // Delay nhỏ để tránh submit quá nhiều lần
                    setTimeout(function() {
                        filterForm.submit();
                    }, 100);
                });
            });
        });
    </script>
@endsection
