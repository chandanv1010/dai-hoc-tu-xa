@php
    // Lấy thông tin từ languages relationship
    $schoolLanguage = $school->languages->first() ?? null;
    $schoolName = '';
    $schoolCanonical = '';
    $majorsCount = 0;
    
    if ($schoolLanguage) {
        $pivot = $schoolLanguage->pivot ?? null;
        if ($pivot) {
            $schoolName = $pivot->name ?? '';
            $schoolCanonical = $pivot->canonical ?? '';
            
            // Đếm số ngành từ majors JSON
            if (isset($pivot->majors) && is_array($pivot->majors)) {
                $majorsCount = count($pivot->majors);
            } elseif (isset($pivot->majors) && is_string($pivot->majors)) {
                $majorsData = json_decode($pivot->majors, true);
                if (is_array($majorsData)) {
                    $majorsCount = count($majorsData);
                }
            }
        }
    }
    
    // Lấy ảnh
    $schoolImage = $school->image ?? '';
    $schoolImageUrl = $schoolImage ? asset($schoolImage) : asset('frontend/resources/img/school-default.png');
    
    // Tạo URL
    $schoolUrl = $schoolCanonical ? write_url($schoolCanonical) : '#';
    
    // Icon mặc định (có thể thay đổi theo từng trường)
    $schoolIcon = $schoolImageUrl;
@endphp

<div class="school-card">
        <div class="school-card-icon">
            <img src="{{ $schoolIcon }}" alt="{{ $schoolName }}">
        </div>
        <div class="school-card-content">
            <h3 class="school-card-name">{{ $schoolName }}</h3>
            <div class="school-card-info">
                <div class="school-card-info-item">
                    <span class="info-label">Hệ Đào Tạo Từ Xa</span>
                </div>
                <div class="school-card-info-item">
                    <span class="info-label">Số ngành đào tạo: <strong>{{ $majorsCount }}</strong> ngành</span>
                </div>
            </div>
            <a href="{{ $schoolUrl }}" class="school-card-button">Xem chi tiết chương trình</a>
        </div>
    </div>
</div>

