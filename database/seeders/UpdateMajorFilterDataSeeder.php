<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Major;
use Illuminate\Support\Facades\DB;

class UpdateMajorFilterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách các giá trị demo cho Đối Tượng Tuyển Sinh
        $admissionSubjects = [
            'THPT',
            'Trung Cấp',
            'Cao Đẳng',
            'Đại Học',
        ];
        
        // Danh sách các giá trị demo cho Địa Điểm Thi
        $examLocations = [
            'Hà Nội',
            'Đà Nẵng',
            'Hồ Chí Minh',
            'Nhật Bản',
        ];
        
        // Lấy tất cả các majors
        $majors = Major::all();
        
        echo "Updating filter data for {$majors->count()} majors...\n";
        
        $updated = 0;
        foreach ($majors as $index => $major) {
            // Random chọn giá trị cho mỗi field
            $admissionSubject = $admissionSubjects[array_rand($admissionSubjects)];
            $examLocation = $examLocations[array_rand($examLocations)];
            
            // Update major
            $major->admission_subject = $admissionSubject;
            $major->exam_location = $examLocation;
            $major->save();
            
            $updated++;
            
            if (($index + 1) % 10 == 0) {
                echo "Updated " . ($index + 1) . " majors...\n";
            }
        }
        
        echo "Completed! Updated {$updated} majors with filter data.\n";
        echo "Summary:\n";
        echo "- Đối Tượng Tuyển Sinh: " . implode(', ', $admissionSubjects) . "\n";
        echo "- Địa Điểm Thi: " . implode(', ', $examLocations) . "\n";
    }
}
