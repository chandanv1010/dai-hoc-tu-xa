<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCatalogue;
use App\Models\Product;
use App\Models\Language;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $language = Language::where('canonical', 'vn')->first();
        if (!$language) {
            $this->command->error('Language "vn" not found!');
            return;
        }

        DB::beginTransaction();
        try {
            // Tạo danh mục "Khóa Luyện Mất Gốc"
            $catalogue = ProductCatalogue::create([
                'parent_id' => 0,
                'publish' => 1,
                'order' => 1,
                'user_id' => 1,
            ]);

            // Tạo pivot language cho catalogue
            $catalogue->languages()->attach($language->id, [
                'name' => 'Khóa Luyện Mất Gốc',
                'canonical' => 'khoa-luyen-mat-goc',
                'description' => 'Các khóa học dành cho học viên mất gốc tiếng Anh, giúp xây dựng lại nền tảng và đạt chứng chỉ VSTEP.',
                'meta_title' => 'Khóa Luyện Mất Gốc - Luyện thi VSTEP',
                'meta_description' => 'Các khóa học dành cho học viên mất gốc tiếng Anh, giúp xây dựng lại nền tảng và đạt chứng chỉ VSTEP.',
            ]);

            // Tạo router cho catalogue
            DB::table('routers')->insert([
                'canonical' => 'khoa-luyen-mat-goc',
                'module' => 'ProductCatalogueController',
                'controller' => 'Frontend\ProductCatalogueController',
                'action' => 'index',
                'language_id' => $language->id,
                'object_id' => $catalogue->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Cập nhật nested set
            $nestedset = new Nestedsetbie([
                'table' => 'product_catalogues',
                'foreignkey' => 'product_catalogue_id',
                'language_id' => $language->id,
            ]);
            $nestedset();

            // Danh sách khóa học demo
            $courses = [
                [
                    'name' => 'Khóa luyện mất gốc Online - Luyện thi Vstep',
                    'canonical' => 'khoa-luyen-mat-goc-online-luyen-thi-vstep',
                    'description' => 'Bạn đang cần chứng chỉ VSTEP (B1, B2) nhưng lại mất căn bản tiếng Anh? Khóa học này đưa bạn từ con số 0 đến đạt được chứng chỉ mong muốn.',
                    'price' => 1800000,
                    'image' => asset('userfiles/image/system/default-product.jpg'),
                ],
                [
                    'name' => 'Khóa luyện mất gốc Online có giáo viên dạy kèm',
                    'canonical' => 'khoa-luyen-mat-goc-online-co-giao-vien-day-kem',
                    'description' => 'Khóa học dành cho học viên mất gốc cần lộ trình rõ ràng và sự hỗ trợ trực tiếp từ giáo viên.',
                    'price' => 4300000,
                    'image' => asset('userfiles/image/system/default-product.jpg'),
                ],
                [
                    'name' => 'Khóa luyện mất gốc - B2 có giáo viên dạy kèm',
                    'canonical' => 'khoa-luyen-mat-goc-b2-co-giao-vien-day-kem',
                    'description' => 'Khóa học giúp học viên mất gốc nhanh chóng lấy lại nền tảng và đạt trình độ B2 với sự hỗ trợ của giáo viên.',
                    'price' => 6500000,
                    'image' => asset('userfiles/image/system/default-product.jpg'),
                ],
                [
                    'name' => 'Khóa luyện mất gốc - B2 online',
                    'canonical' => 'khoa-luyen-mat-goc-b2-online',
                    'description' => 'Khóa học giúp học viên mất gốc xây lại nền tảng và đạt trình độ B2 hoàn toàn online.',
                    'price' => 2500000,
                    'image' => asset('userfiles/image/system/default-product.jpg'),
                ],
            ];

            // Tạo các khóa học
            foreach ($courses as $index => $courseData) {
                $product = Product::create([
                    'publish' => 1,
                    'order' => $index + 1,
                    'user_id' => 1,
                    'price' => $courseData['price'],
                    'image' => $courseData['image'],
                ]);

                // Tạo pivot language cho product
                $product->languages()->attach($language->id, [
                    'name' => $courseData['name'],
                    'canonical' => $courseData['canonical'],
                    'description' => $courseData['description'],
                    'meta_title' => $courseData['name'],
                    'meta_description' => $courseData['description'],
                ]);

                // Gắn product vào catalogue
                $product->product_catalogues()->attach($catalogue->id);

                // Tạo router cho product
                DB::table('routers')->insert([
                    'canonical' => $courseData['canonical'],
                    'module' => 'ProductController',
                    'controller' => 'Frontend\ProductController',
                    'action' => 'index',
                    'language_id' => $language->id,
                    'object_id' => $product->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            $this->command->info('Demo courses created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error creating demo courses: ' . $e->getMessage());
        }
    }
}
