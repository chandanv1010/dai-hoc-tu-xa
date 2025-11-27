@php
    // Lấy thông tin từ languages relationship
    $majorLanguage = $major->languages->first() ?? null;
    $majorName = '';
    $majorCanonical = '';
    $trainingDuration = '';
    $schoolsList = [];
    
    if ($majorLanguage) {
        $pivot = $majorLanguage->pivot ?? null;
        if ($pivot) {
            $majorName = $pivot->name ?? '';
            $majorCanonical = $pivot->canonical ?? '';
            $trainingDuration = $pivot->training_duration ?? '';
        }
    }
    
    // Lấy danh sách schools từ relationship (dùng short_name)
    if ($major->schools && $major->schools->count() > 0) {
        foreach ($major->schools as $school) {
            // Ưu tiên dùng short_name, nếu không có thì dùng name
            $shortName = $school->short_name ?? '';
            if (empty($shortName)) {
                $schoolLanguage = $school->languages->first() ?? null;
                if ($schoolLanguage && $schoolLanguage->pivot) {
                    $shortName = $schoolLanguage->pivot->name ?? '';
                }
            }
            if (!empty($shortName)) {
                $schoolsList[] = $shortName;
            }
        }
    }
    
    // Lấy ảnh
    $majorImage = $major->image ?? '';
    $majorImageUrl = $majorImage ? asset($majorImage) : asset('frontend/resources/img/major-default.png');
    
    // Tạo URL
    $majorUrl = $majorCanonical ? write_url($majorCanonical) : '#';
    
    // Format schools list
    $schoolsText = !empty($schoolsList) ? implode(', ', $schoolsList) : '';
@endphp

<div class="uk-width-medium-1-3 uk-width-large-1-3">
    <div class="major-card">
        <div class="major-card-image">
            <img src="{{ $majorImageUrl }}" alt="{{ $majorName }}">
        </div>
        <div class="major-card-content">
            <h3 class="major-card-name">{{ $majorName }}</h3>
            <div class="major-card-info">
                @if($schoolsText)
                    <div class="major-card-info-item">
                        <i class="fa fa-graduation-cap"></i>
                        <div class="info-content">
                            <span class="info-label">Trường Đào Tạo:</span>
                            <strong class="info-value">{{ $schoolsText }}</strong>
                        </div>
                    </div>
                @endif
                @if($trainingDuration)
                    <div class="major-card-info-item">
                        <i class="fa fa-clock-o"></i>
                        <div class="info-content">
                            <span class="info-label">Thời Gian Đào Tạo:</span>
                            <strong class="info-value">{{ $trainingDuration }}</strong>
                        </div>
                    </div>
                @endif
            </div>
            <a href="{{ $majorUrl }}" class="major-card-button">Xem chi tiết chương trình</a>
        </div>
    </div>
</div>

