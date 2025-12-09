<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;

class UpdateSchoolFilterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:update-filter-data {--force : Force update all schools even if they already have data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update filter data (graduation_system, training_majors, exam_location) for all schools';

    /**
     * Execute the console command.
     */
    public function handle()
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
        
        $force = $this->option('force');
        
        // Lấy tất cả các trường
        if ($force) {
            $schools = School::all();
            $this->info("Force updating all {$schools->count()} schools...");
        } else {
            // Chỉ cập nhật những trường chưa có dữ liệu
            $schools = School::where(function($query) {
                $query->whereNull('graduation_system')
                      ->orWhereNull('training_majors')
                      ->orWhereNull('exam_location')
                      ->orWhere('graduation_system', '')
                      ->orWhere('training_majors', '')
                      ->orWhere('exam_location', '');
            })->get();
            $this->info("Updating {$schools->count()} schools without filter data...");
        }
        
        if ($schools->count() == 0) {
            $this->info("No schools need to be updated.");
            return 0;
        }
        
        $bar = $this->output->createProgressBar($schools->count());
        $bar->start();
        
        $updated = 0;
        foreach ($schools as $school) {
            // Random chọn giá trị cho mỗi field
            $graduationSystem = $graduationSystems[array_rand($graduationSystems)];
            $trainingMajor = $trainingMajors[array_rand($trainingMajors)];
            $examLocation = $examLocations[array_rand($examLocations)];
            
            // Update school
            $school->graduation_system = $graduationSystem;
            $school->training_majors = $trainingMajor;
            $school->exam_location = $examLocation;
            $school->save();
            
            $updated++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Completed! Updated {$updated} schools with filter data.");
        $this->info("Filter options:");
        $this->line("- Hệ Tốt Nghiệp: " . implode(', ', $graduationSystems));
        $this->line("- Ngành Đào Tạo: " . count($trainingMajors) . " options");
        $this->line("- Địa Điểm Thi: " . implode(', ', $examLocations));
        
        return 0;
    }
}
