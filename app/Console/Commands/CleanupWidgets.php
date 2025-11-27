<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\WidgetRepository;
use Illuminate\Support\Facades\DB;

class CleanupWidgets extends Command
{
    protected $signature = 'widget:cleanup';
    protected $description = 'Xóa tất cả widget, chỉ giữ lại widget distance-learning';

    protected $widgetRepository;

    public function __construct(
        WidgetRepository $widgetRepository
    ) {
        parent::__construct();
        $this->widgetRepository = $widgetRepository;
    }

    public function handle()
    {
        $this->info('Bắt đầu dọn dẹp widgets...');

        DB::beginTransaction();
        try {
            // Đếm số widget hiện tại
            $totalWidgets = DB::table('widgets')->whereNull('deleted_at')->count();
            $this->info("Tổng số widget hiện tại: {$totalWidgets}");

            // Xóa tất cả widget trừ distance-learning
            $deleted = DB::table('widgets')
                ->where('keyword', '!=', 'distance-learning')
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);

            $this->info("Đã xóa {$deleted} widget");

            // Kiểm tra widget distance-learning còn tồn tại không
            $distanceLearningWidget = DB::table('widgets')
                ->where('keyword', 'distance-learning')
                ->whereNull('deleted_at')
                ->first();

            if ($distanceLearningWidget) {
                $this->info("✓ Widget 'distance-learning' vẫn còn tồn tại (ID: {$distanceLearningWidget->id})");
            } else {
                $this->warn("⚠ Widget 'distance-learning' không tồn tại!");
            }

            // Đếm số widget còn lại
            $remainingWidgets = DB::table('widgets')->whereNull('deleted_at')->count();
            $this->info("Số widget còn lại: {$remainingWidgets}");

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

