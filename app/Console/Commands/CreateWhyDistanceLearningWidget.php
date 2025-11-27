<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\WidgetRepository;
use App\Repositories\PostCatalogueRepository;
use Illuminate\Support\Facades\DB;

class CreateWhyDistanceLearningWidget extends Command
{
    protected $signature = 'widget:create-why-distance-learning';
    protected $description = 'Tạo widget cho "Vì Sao Nên Học Hệ Từ Xa?"';

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

        // Tìm PostCatalogue "Vì Sao Nên Học Hệ Từ Xa?"
        $postCatalogue = DB::table('post_catalogues')
            ->join('post_catalogue_language', 'post_catalogues.id', '=', 'post_catalogue_language.post_catalogue_id')
            ->where('post_catalogue_language.language_id', 1)
            ->where('post_catalogue_language.canonical', 'vi-sao-nen-hoc-he-tu-xa')
            ->where('post_catalogues.publish', 2)
            ->select('post_catalogues.id')
            ->first();

        if (!$postCatalogue) {
            $this->error('Không tìm thấy PostCatalogue "Vì Sao Nên Học Hệ Từ Xa?". Vui lòng chạy lệnh post:create-why-distance-learning trước.');
            return 1;
        }

        // Kiểm tra widget đã tồn tại chưa
        $existingWidget = $this->widgetRepository->findByCondition([
            ['keyword', '=', 'why-distance-learning']
        ], true);

        if ($existingWidget && !$existingWidget->isEmpty()) {
            $widget = $existingWidget->first();
            $this->info('Widget đã tồn tại, đang cập nhật...');
            
            $this->widgetRepository->update($widget->id, [
                'model' => 'PostCatalogue',
                'model_id' => $postCatalogue->id,
                'publish' => 2
            ]);
            
            $this->info('✓ Đã cập nhật widget với ID: ' . $widget->id);
        } else {
            // Tạo widget mới
            $widgetData = [
                'name' => 'Vì Sao Nên Học Hệ Từ Xa?',
                'keyword' => 'why-distance-learning',
                'model' => 'PostCatalogue',
                'model_id' => $postCatalogue->id,
                'short_code' => '',
                'description' => json_encode([]),
                'album' => json_encode([]),
                'publish' => 2,
                'user_id' => 1
            ];

            $widget = $this->widgetRepository->create($widgetData);
            $this->info('✓ Đã tạo widget với ID: ' . $widget->id);
        }

        $this->info('');
        $this->info('✓ Hoàn thành!');
        return 0;
    }
}

