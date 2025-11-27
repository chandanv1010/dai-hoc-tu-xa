@extends('frontend.homepage.layout')
@section('content')
    <div class="search-results page-wrapper">
        <div class="uk-container uk-container-center mt40">
            <div class="panel-body">
                <h2 class="heading-1 mb20">
                    <span>Kết quả tìm kiếm cho: "{{ $keyword }}"</span>
                </h2>

                @php
                    $hasResults = false;
                    if (($posts && $posts->count() > 0) || 
                        ($schools && $schools->count() > 0) || 
                        ($majors && $majors->count() > 0)) {
                        $hasResults = true;
                    }
                @endphp

                @if($hasResults)
                    {{-- Bài viết --}}
                    @if($posts && $posts->count() > 0)
                        <div class="search-section mb40">
                            <h3 class="search-section-title">Bài viết ({{ $posts->total() }})</h3>
                            <div class="search-results-grid">
                                <div class="uk-grid uk-grid-medium">
                                    @foreach($posts as $post)
                                        @php
                                            $postLanguage = $post->languages->first();
                                            $postName = ($postLanguage && $postLanguage->pivot) ? ($postLanguage->pivot->name ?? '') : '';
                                            $postDescription = ($postLanguage && $postLanguage->pivot) ? ($postLanguage->pivot->description ?? '') : '';
                                            $postCanonical = ($postLanguage && $postLanguage->pivot) ? ($postLanguage->pivot->canonical ?? '') : '';
                                            $postUrl = $postCanonical ? write_url($postCanonical) : '#';
                                            $postImage = $post->image ?? '';
                                            $hasPostImage = !empty($postImage);
                                            $postImageUrl = $hasPostImage ? (function_exists('thumb') ? thumb($postImage, 400, 300) : asset($postImage)) : '';
                                            
                                            $postDate = $post->created_at ?? now();
                                            $formattedDate = $postDate ? date('d/m/Y', strtotime($postDate)) : '';
                                            
                                            $cleanDescription = strip_tags($postDescription);
                                            $shortDescription = mb_strlen($cleanDescription) > 150 ? mb_substr($cleanDescription, 0, 150) . '...' : $cleanDescription;
                                        @endphp
                                        <div class="uk-width-medium-1-2 uk-width-large-1-3">
                                            <div class="search-result-item">
                                                <a href="{{ $postUrl }}" class="search-result-image img-cover {{ !$hasPostImage ? 'no-image' : '' }}">
                                                    @if($hasPostImage)
                                                        <img src="{{ $postImageUrl }}" alt="{{ $postName }}">
                                                    @endif
                                                </a>
                                                <div class="search-result-content">
                                                    <h3 class="search-result-title">
                                                        <a href="{{ $postUrl }}" title="{{ $postName }}">{{ $postName }}</a>
                                                    </h3>
                                                    @if($shortDescription)
                                                        <p class="search-result-description">{!! $shortDescription !!}</p>
                                                    @endif
                                                    <div class="search-result-meta">
                                                        <span class="search-result-date">
                                                            <i class="fa fa-calendar"></i>
                                                            {{ $formattedDate }}
                                                        </span>
                                                        <a href="{{ $postUrl }}" class="search-result-link">Xem chi tiết →</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($posts->hasPages())
                                    <div class="search-pagination mt30">
                                        {{ $posts->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Trường học --}}
                    @if($schools && $schools->count() > 0)
                        <div class="search-section mb40">
                            <h3 class="search-section-title">Trường học ({{ $schools->total() }})</h3>
                            <div class="search-results-grid">
                                <div class="uk-grid uk-grid-medium">
                                    @foreach($schools as $school)
                                        @php
                                            $schoolLanguage = $school->languages->first();
                                            $schoolName = ($schoolLanguage && $schoolLanguage->pivot) ? ($schoolLanguage->pivot->name ?? '') : '';
                                            $schoolDescription = ($schoolLanguage && $schoolLanguage->pivot) ? ($schoolLanguage->pivot->description ?? '') : '';
                                            $schoolCanonical = ($schoolLanguage && $schoolLanguage->pivot) ? ($schoolLanguage->pivot->canonical ?? '') : '';
                                            $schoolUrl = $schoolCanonical ? write_url($schoolCanonical) : '#';
                                            $schoolImage = $school->image ?? '';
                                            $hasSchoolImage = !empty($schoolImage);
                                            
                                            $cleanDescription = strip_tags($schoolDescription);
                                            $shortDescription = mb_strlen($cleanDescription) > 150 ? mb_substr($cleanDescription, 0, 150) . '...' : $cleanDescription;
                                        @endphp
                                        <div class="uk-width-medium-1-2 uk-width-large-1-3">
                                            <div class="search-result-item">
                                                <a href="{{ $schoolUrl }}" class="search-result-image img-cover {{ !$hasSchoolImage ? 'no-image' : '' }}">
                                                    @if($hasSchoolImage)
                                                        <img src="{{ image($schoolImage) }}" alt="{{ $schoolName }}">
                                                    @endif
                                                </a>
                                                <div class="search-result-content">
                                                    <h3 class="search-result-title">
                                                        <a href="{{ $schoolUrl }}" title="{{ $schoolName }}">{{ $schoolName }}</a>
                                                    </h3>
                                                    @if($shortDescription)
                                                        <p class="search-result-description">{!! $shortDescription !!}</p>
                                                    @endif
                                                    <a href="{{ $schoolUrl }}" class="search-result-link">Xem chi tiết →</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($schools->hasPages())
                                    <div class="search-pagination mt30">
                                        {{ $schools->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Ngành học --}}
                    @if($majors && $majors->count() > 0)
                        <div class="search-section mb40">
                            <h3 class="search-section-title">Ngành học ({{ $majors->total() }})</h3>
                            <div class="search-results-grid">
                                <div class="uk-grid uk-grid-medium">
                                    @foreach($majors as $major)
                                        @php
                                            $majorLanguage = $major->languages->first();
                                            $majorName = ($majorLanguage && $majorLanguage->pivot) ? ($majorLanguage->pivot->name ?? '') : '';
                                            $majorDescription = ($majorLanguage && $majorLanguage->pivot) ? ($majorLanguage->pivot->description ?? '') : '';
                                            $majorCanonical = ($majorLanguage && $majorLanguage->pivot) ? ($majorLanguage->pivot->canonical ?? '') : '';
                                            $majorUrl = $majorCanonical ? write_url($majorCanonical) : '#';
                                            $majorImage = $major->image ?? '';
                                            $hasMajorImage = !empty($majorImage);
                                            
                                            $cleanDescription = strip_tags($majorDescription);
                                            $shortDescription = mb_strlen($cleanDescription) > 150 ? mb_substr($cleanDescription, 0, 150) . '...' : $cleanDescription;
                                        @endphp
                                        <div class="uk-width-medium-1-2 uk-width-large-1-3">
                                            <div class="search-result-item">
                                                <a href="{{ $majorUrl }}" class="search-result-image img-cover {{ !$hasMajorImage ? 'no-image' : '' }}">
                                                    @if($hasMajorImage)
                                                        <img src="{{ image($majorImage) }}" alt="{{ $majorName }}">
                                                    @endif
                                                </a>
                                                <div class="search-result-content">
                                                    <h3 class="search-result-title">
                                                        <a href="{{ $majorUrl }}" title="{{ $majorName }}">{{ $majorName }}</a>
                                                    </h3>
                                                    @if($shortDescription)
                                                        <p class="search-result-description">{!! $shortDescription !!}</p>
                                                    @endif
                                                    <a href="{{ $majorUrl }}" class="search-result-link">Xem chi tiết →</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($majors->hasPages())
                                    <div class="search-pagination mt30">
                                        {{ $majors->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <div class="search-empty pt20 pb20">
                        <p>Không có kết quả phù hợp với từ khóa "{{ $keyword }}".</p>
                        <p>Vui lòng thử lại với từ khóa khác.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
