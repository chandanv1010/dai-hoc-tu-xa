<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\WidgetRepository;
use App\Repositories\PostCatalogueRepository;
use Illuminate\Support\Facades\DB;

class CreateNewsOutstandingWidget extends Command
{
    protected $signature = 'widget:create-news-outstanding';
    protected $description = 'Tạo widget cho "TIN TỨC NỔI BẬT"';

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
        $this->info('Bắt đầu tạo widget news-outstanding...');

        $languageId = 1;

        DB::beginTransaction();
        try {
            // Tìm PostCatalogue theo canonical
            $postCatalogue = DB::table('post_catalogues')
                ->join('post_catalogue_language as pcl', 'pcl.post_catalogue_id', '=', 'post_catalogues.id')
                ->where('pcl.language_id', $languageId)
                ->where('pcl.canonical', 'tin-tuc-noi-bat')
                ->select('post_catalogues.id')
                ->first();

            if (!$postCatalogue) {
                $this->error('Không tìm thấy PostCatalogue với canonical "tin-tuc-noi-bat"');
                $this->info('Vui lòng chạy lệnh: php artisan post:create-news-outstanding trước');
                DB::rollBack();
                return 1;
            }

            $postCatalogueId = $postCatalogue->id;
            $this->info("Tìm thấy PostCatalogue ID: {$postCatalogueId}");

            // Kiểm tra widget đã tồn tại chưa
            $existingWidget = $this->widgetRepository->findByCondition([
                ['keyword', '=', 'news-outstanding']
            ], true);

            if ($existingWidget && !$existingWidget->isEmpty()) {
                $this->warn('Widget "news-outstanding" đã tồn tại');
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
                'name' => 'Tin tức nổi bật',
                'keyword' => 'news-outstanding',
                'short_code' => 'news-outstanding',
                'model' => 'PostCatalogue',
                'model_id' => $postCatalogueId,
                'description' => [
                    $languageId => 'Widget hiển thị tin tức nổi bật'
                ],
                'album' => [],
                'publish' => 2, // Active
                'note' => 'Widget cho khối "Tin tức nổi bật" trên homepage'
            ];

            $widget = $this->widgetRepository->create($widgetData);

            if ($widget) {
                $this->info('✓ Đã tạo widget với ID: ' . $widget->id);
                $this->info('  - Keyword: news-outstanding');
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

