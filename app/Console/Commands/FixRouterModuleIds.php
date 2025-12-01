<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixRouterModuleIds extends Command
{
    protected $signature = 'router:fix-module-ids {--dry-run : Chỉ kiểm tra, không cập nhật}';
    protected $description = 'Kiểm tra và sửa lại module_id trong bảng routers cho khớp với canonical trong các bảng liên quan';

    // Mapping giữa controllers và cấu trúc bảng
    protected $controllerMapping = [
        'App\Http\Controllers\Frontend\PostController' => [
            'pivot_table' => 'post_language',
            'module_id_column' => 'post_id',
            'canonical_column' => 'canonical',
            'module_table' => 'posts',
        ],
        'App\Http\Controllers\Frontend\PostCatalogueController' => [
            'pivot_table' => 'post_catalogue_language',
            'module_id_column' => 'post_catalogue_id',
            'canonical_column' => 'canonical',
            'module_table' => 'post_catalogues',
        ],
        'App\Http\Controllers\Frontend\ProductController' => [
            'pivot_table' => 'product_language',
            'module_id_column' => 'product_id',
            'canonical_column' => 'canonical',
            'module_table' => 'products',
        ],
        'App\Http\Controllers\Frontend\ProductCatalogueController' => [
            'pivot_table' => 'product_catalogue_language',
            'module_id_column' => 'product_catalogue_id',
            'canonical_column' => 'canonical',
            'module_table' => 'product_catalogues',
        ],
        'App\Http\Controllers\Frontend\SchoolController' => [
            'pivot_table' => 'school_language',
            'module_id_column' => 'school_id',
            'canonical_column' => 'canonical',
            'module_table' => 'schools',
        ],
        'App\Http\Controllers\Frontend\MajorCatalogueController' => [
            'pivot_table' => 'major_catalogue_language',
            'module_id_column' => 'major_catalogue_id',
            'canonical_column' => 'canonical',
            'module_table' => 'major_catalogues',
        ],
        'App\Http\Controllers\Frontend\MajorController' => [
            'pivot_table' => 'major_language',
            'module_id_column' => 'major_id',
            'canonical_column' => 'canonical',
            'module_table' => 'majors',
        ],
        'App\Http\Controllers\Frontend\\MajorController' => [ // Fix cho trường hợp có double backslash
            'pivot_table' => 'major_language',
            'module_id_column' => 'major_id',
            'canonical_column' => 'canonical',
            'module_table' => 'majors',
        ],
    ];

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 CHẾ ĐỘ KIỂM TRA (DRY RUN) - Không cập nhật dữ liệu');
            $this->newLine();
        } else {
            $this->info('🔧 BẮT ĐẦU SỬA LẠI MODULE_ID TRONG BẢNG ROUTERS...');
            $this->newLine();
        }

        $totalMismatched = 0;
        $totalFixed = 0;
        $totalCorrect = 0;

        DB::beginTransaction();
        try {
            // Lấy tất cả routers
            $routers = DB::table('routers')->get();

            $this->info("Tổng số routers: " . $routers->count());
            $this->newLine();

            foreach ($routers as $router) {
                $controller = $router->controllers;
                $canonical = $router->canonical;
                $currentModuleId = $router->module_id;
                $languageId = $router->language_id;

                // Kiểm tra xem controller có trong mapping không
                if (!isset($this->controllerMapping[$controller])) {
                    $this->warn("  ⚠️  Router ID {$router->id}: Controller không được hỗ trợ: {$controller}");
                    continue;
                }

                $mapping = $this->controllerMapping[$controller];
                $pivotTable = $mapping['pivot_table'];
                $moduleIdColumn = $mapping['module_id_column'];
                $canonicalColumn = $mapping['canonical_column'];
                $moduleTable = $mapping['module_table'];

                // Tìm module_id đúng dựa trên canonical trong bảng pivot
                $correctModule = DB::table($pivotTable)
                    ->where($canonicalColumn, $canonical)
                    ->where('language_id', $languageId)
                    ->first();

                if (!$correctModule) {
                    $this->warn("  ⚠️  Router ID {$router->id}: Không tìm thấy canonical '{$canonical}' trong bảng {$pivotTable} (language_id: {$languageId})");
                    continue;
                }

                $correctModuleId = $correctModule->$moduleIdColumn;

                // Kiểm tra xem module_id có khớp không
                if ($currentModuleId == $correctModuleId) {
                    $totalCorrect++;
                    $this->line("  ✓ Router ID {$router->id}: Đúng (canonical: {$canonical}, module_id: {$currentModuleId})");
                    continue;
                }

                // Kiểm tra xem module_id cũ có tồn tại trong bảng module không
                $oldModuleExists = DB::table($moduleTable)
                    ->where('id', $currentModuleId)
                    ->exists();

                $totalMismatched++;
                
                if (!$dryRun) {
                    // Cập nhật module_id
                    DB::table('routers')
                        ->where('id', $router->id)
                        ->update([
                            'module_id' => $correctModuleId,
                            'updated_at' => now(),
                        ]);

                    $totalFixed++;
                    
                    if ($oldModuleExists) {
                        $this->info("  ✏️  Router ID {$router->id}: ĐÃ SỬA module_id từ {$currentModuleId} → {$correctModuleId} (canonical: {$canonical})");
                    } else {
                        $this->info("  ✏️  Router ID {$router->id}: ĐÃ SỬA module_id từ {$currentModuleId} (không tồn tại) → {$correctModuleId} (canonical: {$canonical})");
                    }
                } else {
                    // Chỉ hiển thị thông tin
                    if ($oldModuleExists) {
                        $this->warn("  ⚠️  Router ID {$router->id}: SẼ SỬA module_id từ {$currentModuleId} → {$correctModuleId} (canonical: {$canonical})");
                    } else {
                        $this->warn("  ⚠️  Router ID {$router->id}: SẼ SỬA module_id từ {$currentModuleId} (không tồn tại) → {$correctModuleId} (canonical: {$canonical})");
                    }
                }
            }

            if ($dryRun) {
                DB::rollBack();
                $this->newLine();
                $this->info("════════════════════════════════════════════════════════");
                $this->info("📊 KẾT QUẢ KIỂM TRA:");
                $this->info("  ✓ Đúng: {$totalCorrect} routers");
                $this->info("  ⚠️  Cần sửa: {$totalMismatched} routers");
                $this->info("════════════════════════════════════════════════════════");
                $this->newLine();
                $this->info("💡 Chạy lệnh không có --dry-run để thực hiện cập nhật");
            } else {
                DB::commit();
                $this->newLine();
                $this->info("════════════════════════════════════════════════════════");
                $this->info("✅ HOÀN THÀNH:");
                $this->info("  ✓ Đúng: {$totalCorrect} routers");
                $this->info("  ✏️  Đã sửa: {$totalFixed} routers");
                $this->info("════════════════════════════════════════════════════════");
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ LỖI: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

