<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\WidgetRepository;
use App\Repositories\PostCatalogueRepository;
use Illuminate\Support\Facades\DB;

class CreateTrainingProgramWidget extends Command
{
    protected $signature = 'widget:create-training-program';
    protected $description = 'Tạo widget cho "Chương trình Đào tạo dành cho Ai?"';

    protected $widgetRepository;
    protected $postCatalogueRepository;

    public function __construct(
        WidgetRepository $widgetRepository,
        PostCatalogueRepository $postCatalogueRepository
    ) {
        parent::__construct();
        $this->widgetRepository = $widgetRepository;
        $this->postCatalogueRepository = $postCatalogueRepository;
    }

    public function handle()
    {
        $this->info('Bắt đầu tạo widget...');

        $languageId = 1;

        DB::beginTransaction();
        try {
            // Tìm PostCatalogue theo canonical
            $postCatalogue = DB::table('post_catalogues')
                ->join('post_catalogue_language as pcl', 'pcl.post_catalogue_id', '=', 'post_catalogues.id')
                ->where('pcl.language_id', $languageId)
                ->where('pcl.canonical', 'chuong-trinh-dao-tao-danh-cho-ai')
                ->select('post_catalogues.id')
                ->first();

            if (!$postCatalogue) {
                $this->error('Không tìm thấy PostCatalogue với canonical "chuong-trinh-dao-tao-danh-cho-ai"');
                $this->info('Vui lòng chạy lệnh: php artisan post:create-training-program trước');
                DB::rollBack();
                return 1;
            }

            $postCatalogueId = $postCatalogue->id;
            $this->info("Tìm thấy PostCatalogue ID: {$postCatalogueId}");

            // Kiểm tra widget đã tồn tại chưa
            $existingWidget = $this->widgetRepository->findByCondition([
                ['keyword', '=', 'training-program']
            ], true);

            if ($existingWidget && !$existingWidget->isEmpty()) {
                $this->warn('Widget "training-program" đã tồn tại');
                $this->info('Cập nhật model_id...');
                
                $widget = $existingWidget->first();
                $this->widgetRepository->update($widget->id, [
                    'model_id' => $postCatalogueId,
                    'model' => 'PostCatalogue',
                    'publish' => 2
                ]);
                
                $this->info('✓ Đã cập nhật widget');
                DB::commit();
                return 0;
            }

            // Tạo widget mới
            $widgetData = [
                'name' => 'Chương trình Đào tạo dành cho Ai?',
                'keyword' => 'training-program',
                'short_code' => 'training-program',
                'model' => 'PostCatalogue',
                'model_id' => $postCatalogueId,
                'description' => [
                    $languageId => 'Widget hiển thị thông tin về chương trình đào tạo dành cho ai'
                ],
                'album' => [],
                'publish' => 2, // Active
                'note' => 'Widget cho khối "Chương trình Đào tạo dành cho Ai?" trên homepage'
            ];

            $widget = $this->widgetRepository->create($widgetData);

            if ($widget) {
                $this->info('✓ Đã tạo widget với ID: ' . $widget->id);
                $this->info('  - Keyword: training-program');
                $this->info('  - Model: PostCatalogue');
                $this->info('  - Model ID: ' . $postCatalogueId);
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

