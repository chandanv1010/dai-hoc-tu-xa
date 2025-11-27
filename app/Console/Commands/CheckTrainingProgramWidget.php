<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\WidgetRepository;
use Illuminate\Support\Facades\DB;

class CheckTrainingProgramWidget extends Command
{
    protected $signature = 'widget:check-training-program';
    protected $description = 'Kiểm tra widget training-program';

    protected $widgetRepository;

    public function __construct(WidgetRepository $widgetRepository)
    {
        parent::__construct();
        $this->widgetRepository = $widgetRepository;
    }

    public function handle()
    {
        $widget = $this->widgetRepository->findByCondition([
            ['keyword', '=', 'training-program']
        ], true);

        if ($widget && !$widget->isEmpty()) {
            $w = $widget->first();
            $this->info('Widget found:');
            $this->info('  ID: ' . $w->id);
            $this->info('  Keyword: ' . $w->keyword);
            $this->info('  Model: ' . $w->model);
            $this->info('  Model ID: ' . $w->model_id);
            $this->info('  Publish: ' . $w->publish);
            
            // Kiểm tra PostCatalogue
            $postCatalogue = DB::table('post_catalogues')
                ->where('id', $w->model_id)
                ->first();
            
            if ($postCatalogue) {
                $this->info('  PostCatalogue exists: ID ' . $postCatalogue->id);
                
                // Kiểm tra posts
                $postsCount = DB::table('post_catalogue_post')
                    ->where('post_catalogue_id', $postCatalogue->id)
                    ->count();
                
                $this->info('  Posts count: ' . $postsCount);
            } else {
                $this->error('  PostCatalogue not found!');
            }
        } else {
            $this->error('Widget not found!');
        }
        
        return 0;
    }
}

