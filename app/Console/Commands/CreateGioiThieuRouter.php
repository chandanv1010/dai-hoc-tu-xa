<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PostCatalogue;
use App\Models\Router;
use App\Models\Language;
use App\Models\User;
use App\Repositories\PostCatalogueRepository;
use App\Services\PostCatalogueService;
use Illuminate\Support\Facades\DB;

class CreateGioiThieuRouter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'router:create-gioi-thieu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo router cho trang giới thiệu (gioi-thieu)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang tìm post_catalogue với canonical "gioi-thieu"...');

        // Lấy language mặc định (tiếng Việt)
        $language = Language::where('canonical', 'vn')->first();
        if (!$language) {
            $language = Language::first();
        }

        if (!$language) {
            $this->error('Không tìm thấy language. Vui lòng tạo language trước.');
            return 1;
        }

        $languageId = $language->id;
        $this->info("Sử dụng language_id: {$languageId}");

        // Tìm post_catalogue với canonical 'gioi-thieu'
        $postCatalogue = PostCatalogue::join('post_catalogue_language as pcl', 'pcl.post_catalogue_id', '=', 'post_catalogues.id')
            ->where('pcl.canonical', 'gioi-thieu')
            ->where('pcl.language_id', $languageId)
            ->select('post_catalogues.*', 'pcl.canonical')
            ->first();

        if (!$postCatalogue) {
            $this->warn('Không tìm thấy post_catalogue với canonical "gioi-thieu".');
            $this->info('Đang tạo post_catalogue mới...');
            
            DB::beginTransaction();
            try {
                // Lấy user đầu tiên
                $user = User::first();
                if (!$user) {
                    $this->error('Không tìm thấy user. Vui lòng tạo user trước.');
                    return 1;
                }
                
                // Tạo post_catalogue
                $postCatalogue = PostCatalogue::create([
                    'parent_id' => 0,
                    'publish' => 2,
                    'follow' => 1,
                    'lft' => 0,
                    'rgt' => 0,
                    'user_id' => $user->id,
                ]);
                
                // Tạo post_catalogue_language
                DB::table('post_catalogue_language')->insert([
                    'post_catalogue_id' => $postCatalogue->id,
                    'language_id' => $languageId,
                    'name' => 'Giới Thiệu',
                    'canonical' => 'gioi-thieu',
                    'description' => 'Trang giới thiệu về chúng tôi',
                    'meta_title' => 'Giới Thiệu',
                    'meta_keyword' => 'giới thiệu',
                    'meta_description' => 'Trang giới thiệu về chúng tôi',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                DB::commit();
                $this->info("Đã tạo post_catalogue mới với ID: {$postCatalogue->id}");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Lỗi khi tạo post_catalogue: ' . $e->getMessage());
                return 1;
            }
        }

        $this->info("Tìm thấy post_catalogue ID: {$postCatalogue->id}");

        // Kiểm tra xem router đã tồn tại chưa
        $existingRouter = Router::where('canonical', 'gioi-thieu')
            ->where('language_id', $languageId)
            ->where('module_id', $postCatalogue->id)
            ->first();

        if ($existingRouter) {
            $this->info('Router đã tồn tại với ID: ' . $existingRouter->id);
            $this->info('Đang cập nhật router...');
            
            $existingRouter->update([
                'controllers' => 'App\Http\Controllers\Frontend\PostCatalogueController',
            ]);
            
            $this->info('Router đã được cập nhật thành công!');
            return 0;
        }

        // Tạo router mới
        $this->info('Đang tạo router mới...');
        
        $router = Router::create([
            'canonical' => 'gioi-thieu',
            'module_id' => $postCatalogue->id,
            'controllers' => 'App\Http\Controllers\Frontend\PostCatalogueController',
            'language_id' => $languageId,
        ]);

        $this->info('Router đã được tạo thành công với ID: ' . $router->id);
        $this->info('Bây giờ bạn có thể truy cập: ' . rtrim(config('app.url'), '/') . '/gioi-thieu.html');
        
        return 0;
    }
}
