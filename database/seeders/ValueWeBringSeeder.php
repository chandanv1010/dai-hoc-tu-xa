<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PostCatalogue;
use App\Models\Post;
use App\Models\Language;
use App\Models\Router;
use App\Models\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;

class ValueWeBringSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageId = 1; // Vietnamese language
        
        // Xóa dữ liệu cũ nếu có - xóa trực tiếp từ DB
        // Xóa post_language có canonical bắt đầu bằng "gia-tri-linh-hoat"
        DB::table('post_language')
            ->where('canonical', 'like', 'gia-tri-linh-hoat%')
            ->where('language_id', $languageId)
            ->delete();
        
        // Xóa post_catalogue_language
        DB::table('post_catalogue_language')
            ->where('canonical', 'gia-tri-chung-toi-mang-den')
            ->where('language_id', $languageId)
            ->delete();
        
        $existingCatalogue = PostCatalogue::whereHas('languages', function($query) use ($languageId) {
            $query->where('languages.id', $languageId)
                  ->where('post_catalogue_language.canonical', 'gia-tri-chung-toi-mang-den');
        })->first();

        if ($existingCatalogue) {
            // Xóa router của catalogue
            DB::table('routers')->where('module_id', $existingCatalogue->id)
                ->where('language_id', $languageId)
                ->delete();
            
            // Xóa router của posts
            $postIds = $existingCatalogue->posts()->pluck('posts.id')->toArray();
            if (!empty($postIds)) {
                DB::table('routers')->whereIn('module_id', $postIds)
                    ->where('language_id', $languageId)
                    ->delete();
                DB::table('post_language')->whereIn('post_id', $postIds)->delete();
                Post::whereIn('id', $postIds)->delete();
            }
            
            // Xóa widget
            DB::table('widgets')->where('keyword', 'value-we-bring')->delete();
            
            // Xóa post_catalogue_post
            DB::table('post_catalogue_post')->where('post_catalogue_id', $existingCatalogue->id)->delete();
            
            // Xóa catalogue
            $existingCatalogue->delete();
        }
        
        // Xóa widget còn sót
        DB::table('widgets')->where('keyword', 'value-we-bring')->delete();

        // Tạo PostCatalogue
        $postCatalogue = PostCatalogue::create([
            'parent_id' => 0,
            'lft' => 0,
            'rgt' => 0,
            'level' => 0,
            'image' => '',
            'icon' => '',
            'album' => '',
            'publish' => 2, // Published
            'follow' => 1,
            'order' => 0,
            'user_id' => 1,
            'short_name' => ''
        ]);

        if ($postCatalogue->id > 0) {
            // Tạo language pivot cho catalogue
            $catalogueLanguageData = [
                'post_catalogue_id' => $postCatalogue->id,
                'language_id' => $languageId,
                'name' => 'Giá trị chúng tôi mang đến',
                'canonical' => 'gia-tri-chung-toi-mang-den',
                'description' => 'Tại sao Hệ Đào Tạo Từ Xa là lựa chọn hoàn hảo cho bạn',
                'content' => '',
                'meta_title' => 'Giá trị chúng tôi mang đến',
                'meta_keyword' => 'giá trị, đào tạo từ xa, lợi ích',
                'meta_description' => 'Tại sao Hệ Đào Tạo Từ Xa là lựa chọn hoàn hảo cho bạn'
            ];

            $postCatalogue->languages()->attach($languageId, $catalogueLanguageData);

            // Tạo router cho catalogue
            $routerData = [
                'canonical' => 'gia-tri-chung-toi-mang-den',
                'module_id' => $postCatalogue->id,
                'language_id' => $languageId,
                'controllers' => 'App\Http\Controllers\Frontend\PostCatalogueController',
            ];
            
            $existingRouter = Router::where('canonical', $routerData['canonical'])
                ->where('language_id', $languageId)
                ->first();
            
            if (!$existingRouter) {
                Router::create($routerData);
            }

            // Chạy nestedset
            $nestedset = new Nestedsetbie([
                'table' => 'post_catalogues',
                'foreignkey' => 'post_catalogue_id',
                'language_id' => $languageId,
            ]);
            $nestedset->Get('level ASC, order ASC');
            $nestedset->Recursive(0, $nestedset->Set());
            $nestedset->Action();

            // Tạo 5 Posts
            $postsData = [
                [
                    'name' => 'Linh hoạt',
                    'canonical' => 'gia-tri-linh-hoat',
                    'content' => "Chủ động thời gian — học mọi lúc, mọi nơi. Phù hợp với người đi làm bận rộn.\nPhù hợp với người đã có 1 bằng đại học và muốn theo học thêm văn bằng đại học thứ 2 từ xa\n*** Thời gian học tối thiểu 2,2 - 3 năm"
                ],
                [
                    'name' => 'Linh hoạt',
                    'canonical' => 'gia-tri-linh-hoat-2',
                    'content' => "Chủ động thời gian — học mọi lúc, mọi nơi. Phù hợp với người đi làm bận rộn.\nPhù hợp với người đã có 1 bằng đại học và muốn theo học thêm văn bằng đại học thứ 2 từ xa\n*** Thời gian học tối thiểu 2,2 - 3 năm"
                ],
                [
                    'name' => 'Linh hoạt',
                    'canonical' => 'gia-tri-linh-hoat-3',
                    'content' => "Chủ động thời gian — học mọi lúc, mọi nơi. Phù hợp với người đi làm bận rộn.\nPhù hợp với người đã có 1 bằng đại học và muốn theo học thêm văn bằng đại học thứ 2 từ xa\n*** Thời gian học tối thiểu 2,2 - 3 năm"
                ],
                [
                    'name' => 'Linh hoạt',
                    'canonical' => 'gia-tri-linh-hoat-4',
                    'content' => "Chủ động thời gian — học mọi lúc, mọi nơi. Phù hợp với người đi làm bận rộn.\nPhù hợp với người đã có 1 bằng đại học và muốn theo học thêm văn bằng đại học thứ 2 từ xa\n*** Thời gian học tối thiểu 2,2 - 3 năm"
                ],
                [
                    'name' => 'Linh hoạt',
                    'canonical' => 'gia-tri-linh-hoat-5',
                    'content' => "Chủ động thời gian — học mọi lúc, mọi nơi. Phù hợp với người đi làm bận rộn.\nPhù hợp với người đã có 1 bằng đại học và muốn theo học thêm văn bằng đại học thứ 2 từ xa\n*** Thời gian học tối thiểu 2,2 - 3 năm"
                ],
            ];

            foreach ($postsData as $index => $postData) {
                $post = Post::create([
                    'publish' => 2,
                    'follow' => 1,
                    'image' => '',
                    'album' => '',
                    'post_catalogue_id' => $postCatalogue->id,
                    'video' => '',
                    'template' => '',
                    'status_menu' => 0,
                    'short_name' => '',
                    'user_id' => 1,
                    'order' => $index + 1
                ]);

                if ($post->id > 0) {
                    // Tạo language pivot cho post
                    $postLanguageData = [
                        'post_id' => $post->id,
                        'language_id' => $languageId,
                        'name' => $postData['name'],
                        'canonical' => $postData['canonical'],
                        'description' => '',
                        'content' => $postData['content'],
                        'meta_title' => $postData['name'],
                        'meta_keyword' => 'giá trị, linh hoạt',
                        'meta_description' => $postData['name']
                    ];

                    $post->languages()->attach($languageId, $postLanguageData);

                    // Attach post to catalogue
                    $postCatalogue->posts()->attach($post->id);

                    // Tạo router cho post
                    $postRouterData = [
                        'canonical' => $postData['canonical'],
                        'module_id' => $post->id,
                        'language_id' => $languageId,
                        'controllers' => 'App\Http\Controllers\Frontend\PostController',
                    ];
                    
                    $existingPostRouter = Router::where('canonical', $postRouterData['canonical'])
                        ->where('language_id', $languageId)
                        ->first();
                    
                    if (!$existingPostRouter) {
                        Router::create($postRouterData);
                    }
                }
            }

            // Tạo Widget - model_id sẽ được tự động cast thành JSON bởi Widget model
            $widgetData = [
                'keyword' => 'value-we-bring',
                'name' => 'Giá trị chúng tôi mang đến',
                'description' => ['Widget hiển thị giá trị chúng tôi mang đến'], // Array cho multi-language
                'publish' => 2,
                'model' => 'App\Models\PostCatalogue',
                'model_id' => [$postCatalogue->id], // Array - model sẽ tự động cast thành JSON
                'short_code' => '',
                'album' => [],
            ];

            $existingWidget = Widget::where('keyword', 'value-we-bring')->first();

            if (!$existingWidget) {
                // Bỏ language_id khỏi widgetData
                unset($widgetData['language_id']);
                Widget::create($widgetData);
            }

            $this->command->info('✓ Đã tạo thành công PostCatalogue "Giá trị chúng tôi mang đến" với 5 Posts');
        }
    }
}
