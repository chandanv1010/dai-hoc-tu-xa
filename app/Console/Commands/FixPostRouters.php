<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Repositories\RouterRepository;

class FixPostRouters extends Command
{
    protected $signature = 'router:fix-posts';
    protected $description = 'Fix routers cho posts - xóa router cũ và tạo lại với module_id đúng';

    protected $routerRepository;

    public function __construct(RouterRepository $routerRepository)
    {
        parent::__construct();
        $this->routerRepository = $routerRepository;
    }

    public function handle()
    {
        $this->info('Bắt đầu fix routers cho posts...');

        $languageId = 1;
        $controller = 'App\Http\Controllers\Frontend\PostController';

        DB::beginTransaction();
        try {
            // Lấy tất cả posts với language
            $posts = DB::table('posts')
                ->join('post_language as pl', 'posts.id', '=', 'pl.post_id')
                ->where('pl.language_id', $languageId)
                ->select('posts.id', 'pl.canonical', 'pl.post_id')
                ->get();

            $this->info('Tìm thấy ' . $posts->count() . ' posts');

            $fixed = 0;
            $created = 0;
            $deleted = 0;

            foreach ($posts as $post) {
                $canonical = $post->canonical;
                $postId = $post->id;

                if (empty($canonical)) {
                    continue;
                }

                // Tìm router hiện tại với canonical này
                $existingRouters = DB::table('routers')
                    ->where('canonical', $canonical)
                    ->where('controllers', $controller)
                    ->where('language_id', $languageId)
                    ->get();

                // Xóa các router có canonical trùng nhưng module_id không khớp
                foreach ($existingRouters as $router) {
                    if ($router->module_id != $postId) {
                        // Kiểm tra xem module_id cũ có phải là post không
                        $oldPost = DB::table('posts')->find($router->module_id);
                        if (!$oldPost || $oldPost->id != $postId) {
                            DB::table('routers')->where('id', $router->id)->delete();
                            $deleted++;
                            $this->info("  ✓ Đã xóa router cũ ID: {$router->id} (canonical: {$canonical}, module_id: {$router->module_id})");
                        }
                    }
                }

                // Kiểm tra xem đã có router đúng chưa
                $correctRouter = DB::table('routers')
                    ->where('canonical', $canonical)
                    ->where('module_id', $postId)
                    ->where('controllers', $controller)
                    ->where('language_id', $languageId)
                    ->first();

                if (!$correctRouter) {
                    // Tạo router mới
                    $routerData = [
                        'canonical' => $canonical,
                        'module_id' => $postId,
                        'language_id' => $languageId,
                        'controllers' => $controller,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Kiểm tra lại xem có router nào với canonical này không (sau khi xóa)
                    $checkRouter = DB::table('routers')
                        ->where('canonical', $canonical)
                        ->where('language_id', $languageId)
                        ->first();

                    if ($checkRouter) {
                        // Update router hiện tại
                        DB::table('routers')
                            ->where('id', $checkRouter->id)
                            ->update([
                                'module_id' => $postId,
                                'controllers' => $controller,
                                'updated_at' => now(),
                            ]);
                        $fixed++;
                        $this->info("  ✓ Đã update router ID: {$checkRouter->id} cho post ID: {$postId} (canonical: {$canonical})");
                    } else {
                        // Tạo mới
                        DB::table('routers')->insert($routerData);
                        $created++;
                        $this->info("  ✓ Đã tạo router mới cho post ID: {$postId} (canonical: {$canonical})");
                    }
                } else {
                    $this->line("  - Router đã đúng cho post ID: {$postId}");
                }
            }

            DB::commit();

            $this->info('');
            $this->info("✓ Hoàn thành!");
            $this->info("  - Đã xóa: {$deleted} router cũ");
            $this->info("  - Đã sửa: {$fixed} router");
            $this->info("  - Đã tạo: {$created} router mới");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

