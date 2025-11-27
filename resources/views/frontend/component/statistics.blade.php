@php
    // Lấy dữ liệu từ system config
    $stats = [
        [
            'number' => $system['stat_1_number'] ?? '11',
            'label' => $system['stat_1_label'] ?? 'Trường Đào Tạo Từ Xa',
            'suffix' => ''
        ],
        [
            'number' => $system['stat_2_number'] ?? '31',
            'label' => $system['stat_2_label'] ?? 'Ngành Học',
            'suffix' => ''
        ],
        [
            'number' => $system['stat_3_number'] ?? '70000',
            'label' => $system['stat_3_label'] ?? 'Học Viên Theo Học',
            'suffix' => '+'
        ],
        [
            'number' => $system['stat_4_number'] ?? '1000',
            'label' => $system['stat_4_label'] ?? 'Khóa Khai giảng',
            'suffix' => '+'
        ],
        [
            'number' => $system['stat_5_number'] ?? '98',
            'label' => $system['stat_5_label'] ?? 'Học Viên Hài Lòng',
            'suffix' => '%'
        ],
        [
            'number' => $system['stat_6_number'] ?? '97',
            'label' => $system['stat_6_label'] ?? 'Có việc sau tốt nghiệp',
            'suffix' => '%'
        ],
    ];
@endphp

<div class="panel-statistics">
    <div class="uk-container uk-container-center">
        <div class="statistics-wrapper">
            <div class="statistics-list">
                @foreach($stats as $index => $stat)
                    <div class="statistics-item" data-target="{{ $stat['number'] }}" data-suffix="{{ $stat['suffix'] }}">
                        <div class="stat-number">
                            <span class="counter-value">0</span><span class="counter-suffix">{{ $stat['suffix'] }}</span>
                        </div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

