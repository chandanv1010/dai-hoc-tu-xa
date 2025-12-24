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

                            {{-- Pagination đã bị loại bỏ - hiển thị tất cả ngành không phân trang --}}
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
