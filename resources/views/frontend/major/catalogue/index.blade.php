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
    </div>
@endsection
