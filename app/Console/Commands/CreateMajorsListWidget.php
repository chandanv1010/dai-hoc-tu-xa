<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\WidgetRepository;
use Illuminate\Support\Facades\DB;

class CreateMajorsListWidget extends Command
{
    protected $signature = 'widget:create-majors-list';
    protected $description = 'Tạo widget cho "Các Ngành Đào Tạo Từ Xa"';

    protected $widgetRepository;

    public function __construct(
        WidgetRepository $widgetRepository
    ) {
        parent::__construct();
        $this->widgetRepository = $widgetRepository;
    }

    public function handle()
    {
        $this->info('Bắt đầu tạo widget majors-list...');

        $languageId = 1;

        DB::beginTransaction();
        try {
            // Kiểm tra widget đã tồn tại chưa
            $existingWidget = $this->widgetRepository->findByCondition([
                ['keyword', '=', 'majors-list']
            ], true);

            if ($existingWidget && !$existingWidget->isEmpty()) {
                $this->warn('Widget "majors-list" đã tồn tại');
                $this->info('Cập nhật widget...');
                
                $widget = $existingWidget->first();
                $this->widgetRepository->update($widget->id, [
                    'publish' => 2,
                    'description' => [
                        $languageId => 'Chương trình đại học từ xa mang đến nhiều lựa chọn để đáp ứng nhu cầu của người đi làm, học viên và công chức. Mỗi chương trình được thiết kế theo tiêu chuẩn, cập nhật kiến thức thực tế, giúp người học dễ dàng thăng tiến và phát triển sự nghiệp.'
                    ]
                ]);
                
                $this->info('✓ Đã cập nhật widget');
                DB::commit();
                return 0;
            }

            // Tạo widget mới
            $widgetData = [
                'name' => 'Các Ngành Đào Tạo Từ Xa',
                'keyword' => 'majors-list',
                'short_code' => 'majors-list',
                'model' => '',
                'model_id' => null,
                'description' => [
                    $languageId => 'Chương trình đại học từ xa mang đến nhiều lựa chọn để đáp ứng nhu cầu của người đi làm, học viên và công chức. Mỗi chương trình được thiết kế theo tiêu chuẩn, cập nhật kiến thức thực tế, giúp người học dễ dàng thăng tiến và phát triển sự nghiệp.'
                ],
                'album' => [],
                'publish' => 2, // Active
                'note' => 'Widget cho khối "Các Ngành Đào Tạo Từ Xa" trên homepage'
            ];

            $widget = $this->widgetRepository->create($widgetData);

            if ($widget) {
                $this->info('✓ Đã tạo widget với ID: ' . $widget->id);
                $this->info('  - Keyword: majors-list');
                $this->info('  - Model: (không kết nối)');
            }

            DB::commit();
            $this->info('');
            $this->info('✓ Hoàn thành!');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
