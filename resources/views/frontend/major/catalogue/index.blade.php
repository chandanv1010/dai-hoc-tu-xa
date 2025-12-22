@extends('frontend.homepage.layout')
@section('content')
    <style>
        .major-catalogue-pagination .pagination {
            justify-content: center;
        }
        
        .major-catalogue-pagination .page-item .page-link {
            color: #333;
            border: 1px solid #dee2e6;
            margin: 0 5px;
            border-radius: 4px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .major-catalogue-pagination .page-item .page-link:hover {
            color: #008DC2;
            background-color: #f8f9fa;
            border-color: #008DC2;
        }

        .major-catalogue-pagination .page-item.active .page-link {
            background-color: #008DC2 !important;
            border-color: #008DC2 !important;
            color: #fff !important;
        }
        
        .major-catalogue-pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            cursor: auto;
            background-color: #fff;
            border-color: #dee2e6;
        }
    </style>
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
                                <div class="major-catalogue-pagination" style="margin-top: 40px; margin-bottom: 40px; text-align: center;">
                                    <ul class="pagination">
                                        @php
                                            $queryParams = request()->except('page');
                                        @endphp

                                        {{-- Previous Page Link --}}
                                        @if ($majors->onFirstPage())
                                            <li class="page-item disabled" aria-disabled="true">
                                                <span class="page-link" aria-hidden="true">&lsaquo;</span>
                                            </li>
                                        @else
                                            @php
                                                $prevPage = $majors->currentPage() - 1;
                                                $prevUrl = $prevPage == 1 ? route('fe.major.catalogue.index') : route('major.catalogue.page', ['page' => $prevPage]);
                                                if (!empty($queryParams)) {
                                                    $prevUrl .= '?' . http_build_query($queryParams);
                                                }
                                            @endphp
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $prevUrl }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                                            </li>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @foreach ($majors->getUrlRange(1, $majors->lastPage()) as $page => $url)
                                            @php
                                                $pageUrl = $page == 1 ? route('fe.major.catalogue.index') : route('major.catalogue.page', ['page' => $page]);
                                                if (!empty($queryParams)) {
                                                    $pageUrl .= '?' . http_build_query($queryParams);
                                                }
                                            @endphp
                                            @if ($page == $majors->currentPage())
                                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $pageUrl }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endforeach

                                        {{-- Next Page Link --}}
                                        @if ($majors->hasMorePages())
                                            @php
                                                $nextUrl = route('major.catalogue.page', ['page' => $majors->currentPage() + 1]);
                                                if (!empty($queryParams)) {
                                                    $nextUrl .= '?' . http_build_query($queryParams);
                                                }
                                            @endphp
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $nextUrl }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                                            </li>
                                        @else
                                            <li class="page-item disabled" aria-disabled="true">
                                                <span class="page-link" aria-hidden="true">&rsaquo;</span>
                                            </li>
                                        @endif
                                    </ul>
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
                    @include('frontend.major.catalogue.filter-sidebar')
                </div>
            </div>
        </div>
    </div>
@endsection
