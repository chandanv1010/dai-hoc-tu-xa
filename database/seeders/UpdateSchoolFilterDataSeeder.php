<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class UpdateSchoolFilterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách các giá trị demo cho Hệ Tốt Nghiệp
        $graduationSystems = [
            'Đại học',
            'Thạc sĩ',
            'Tiến sĩ',
            'Cao đẳng',
            'Trung cấp',
        ];
        
        // Danh sách các giá trị demo cho Ngành Đào Tạo
        $trainingMajors = [
            'Kinh tế',
            'Công nghệ thông tin',
            'Luật',
            'Sư phạm',
            'Y tế',
            'Kỹ thuật',
            'Nông nghiệp',
            'Du lịch',
            'Ngôn ngữ',
            'Kiến trúc',
            'Quản trị kinh doanh',
            'Tài chính - Ngân hàng',
            'Marketing',
            'Quan hệ công chúng',
        ];
        
        // Danh sách các giá trị demo cho Địa Điểm Thi (chỉ 4 giá trị)
        $examLocations = [
            'Hà Nội',
            'Đà Nẵng',
            'Hồ Chí Minh',
            'Nhật Bản',
        ];
        
        // Lấy tất cả các trường (kể cả những trường chưa có dữ liệu filter)
        $schools = School::all();
        
        echo "Updating filter data for {$schools->count()} schools...\n";
        
        $updated = 0;
        foreach ($schools as $index => $school) {
            // Random chọn giá trị cho mỗi field
            $graduationSystem = $graduationSystems[array_rand($graduationSystems)];
            $trainingMajor = $trainingMajors[array_rand($trainingMajors)];
            $examLocation = $examLocations[array_rand($examLocations)];
            
            // Update school - cập nhật tất cả, kể cả những trường đã có dữ liệu
            $school->graduation_system = $graduationSystem;
            $school->training_majors = $trainingMajor;
            $school->exam_location = $examLocation;
            $school->save();
            
            $updated++;
            
            if (($index + 1) % 10 == 0) {
                echo "Updated " . ($index + 1) . " schools...\n";
            }
        }
        
        echo "Completed! Updated {$updated} schools with filter data.\n";
        echo "Summary:\n";
        echo "- Hệ Tốt Nghiệp: " . implode(', ', $graduationSystems) . "\n";
        echo "- Ngành Đào Tạo: " . count($trainingMajors) . " options\n";
        echo "- Địa Điểm Thi: " . implode(', ', $examLocations) . "\n";
    }
}

