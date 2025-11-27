<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Major;
use App\Models\Language;
use App\Models\User;
use App\Models\Post;
use App\Repositories\RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MajorSeeder extends Seeder
{
    protected $routerRepository;

    public function __construct(RouterRepository $routerRepository)
    {
        $this->routerRepository = $routerRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // Get default language (Vietnamese)
            $language = Language::where('canonical', 'vn')->first();
            if (!$language) {
                $this->command->error('Không tìm thấy ngôn ngữ mặc định (vn)');
                return;
            }
            $languageId = $language->id;

            // Get first admin user
            $user = User::first();
            if (!$user) {
                $this->command->error('Không tìm thấy user nào');
                return;
            }

            // Get some post IDs for events (limit to 5 posts)
            $eventPostIds = Post::where('publish', 2)
                ->orderBy('id', 'asc')
                ->limit(5)
                ->pluck('id')
                ->toArray();

            // Delete existing majors and related data
            $existingMajors = Major::all();
            foreach ($existingMajors as $existingMajor) {
                // Delete router
                DB::table('routers')->where('module_id', $existingMajor->id)->where('controllers', 'App\Http\Controllers\Frontend\MajorController')->delete();
                // Delete major (this will cascade delete major_language records)
                $existingMajor->delete();
            }
            
            // Also delete any orphaned routers
            DB::table('routers')->where('controllers', 'App\Http\Controllers\Frontend\MajorController')->delete();

            // Create NEU Major
            $neuMajor = Major::create([
                'subtitle' => 'Đại học Kinh tế Quốc dân',
                'image' => '',
                'publish' => 2,
                'user_id' => $user->id,
                'study_path_file' => '',
                'is_show_feature' => 2,
                'is_show_overview' => 2,
                'is_show_who' => 2,
                'is_show_priority' => 2,
                'is_show_learn' => 2,
                'is_show_chance' => 2,
                'is_show_school' => 2,
                'is_show_value' => 2,
                'is_show_feedback' => 2,
                'is_show_event' => 2,
            ]);

            // Prepare JSON data for NEU
            // feature: [{name, image}]
            $neuFeatures = [
                ['name' => 'Không thi đầu vào', 'image' => ''],
                ['name' => 'Học linh hoạt', 'image' => ''],
                ['name' => 'Bằng được công nhận', 'image' => ''],
                ['name' => 'Tiết kiệm chi phí', 'image' => ''],
                ['name' => 'Hỗ trợ 24/7', 'image' => ''],
            ];

            // target: string[]
            $neuTargets = [
                'Tốt nghiệp THPT hoặc tương đương',
                'Có bằng trung cấp, cao đẳng',
                'Người đang đi làm muốn nâng cao trình độ',
                'Người muốn chuyển đổi nghề nghiệp',
            ];

            // address: [{name, address}]
            $neuAddresses = [
                ['name' => 'Văn phòng tuyển sinh NEU', 'address' => '207 Giải Phóng, Hai Bà Trưng, Hà Nội'],
                ['name' => 'Trung tâm đào tạo từ xa', 'address' => 'Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội'],
                ['name' => 'Chi nhánh TP.HCM', 'address' => '123 Nguyễn Huệ, Quận 1, TP.HCM'],
            ];

            // overview: {name, description, image, items: [{image, name, description}, ...]}
            $neuOverview = [
                'name' => 'Toàn cảnh ngành Quản trị Kinh doanh tại NEU',
                'description' => '<p>Ngành Quản trị Kinh doanh tại NEU là một trong những ngành đào tạo hàng đầu về quản lý doanh nghiệp tại Việt Nam.</p>',
                'image' => '',
                'items' => [
                    ['image' => '', 'name' => 'Cơ hội việc làm rộng mở', 'description' => 'Sinh viên tốt nghiệp có nhiều cơ hội làm việc tại các doanh nghiệp lớn'],
                    ['image' => '', 'name' => 'Chương trình đào tạo chất lượng', 'description' => 'Chương trình được thiết kế theo chuẩn quốc tế'],
                    ['image' => '', 'name' => 'Đội ngũ giảng viên giàu kinh nghiệm', 'description' => 'Giảng viên có nhiều năm kinh nghiệm trong ngành'],
                ]
            ];

            // who: [{name, image, description, person}]
            $neuWho = [
                ['name' => 'Người có đam mê kinh doanh', 'image' => '', 'description' => '<p>Phù hợp với những người yêu thích kinh doanh và quản lý</p>', 'person' => 'Sinh viên tốt nghiệp THPT'],
                ['name' => 'Người muốn phát triển sự nghiệp', 'image' => '', 'description' => '<p>Dành cho những người muốn thăng tiến trong sự nghiệp</p>', 'person' => 'Người đi làm muốn nâng cao trình độ'],
            ];

            // priority: [{name, image, description}]
            $neuPriority = [
                ['name' => 'Học phí hợp lý', 'image' => '', 'description' => 'Học phí chỉ 15 triệu/năm, phù hợp với nhiều đối tượng'],
                ['name' => 'Thời gian linh hoạt', 'image' => '', 'description' => 'Có thể học vào buổi tối hoặc cuối tuần'],
                ['name' => 'Bằng cấp được công nhận', 'image' => '', 'description' => 'Văn bằng được Bộ GD&ĐT công nhận'],
            ];

            // learn: {description, items: [{name, items: [{name, image, description}]}]}
            $neuLearn = [
                'description' => 'Chương trình học cung cấp kiến thức toàn diện về quản trị kinh doanh',
                'items' => [
                    [
                        'name' => 'Kiến thức cơ bản',
                        'items' => [
                            ['name' => 'Quản trị học', 'image' => '', 'description' => 'Học các nguyên lý quản trị cơ bản'],
                            ['name' => 'Kinh tế học', 'image' => '', 'description' => 'Nắm vững kiến thức kinh tế'],
                            ['name' => 'Marketing căn bản', 'image' => '', 'description' => 'Hiểu về marketing và bán hàng'],
                        ]
                    ],
                    [
                        'name' => 'Kiến thức chuyên sâu',
                        'items' => [
                            ['name' => 'Quản trị nhân sự', 'image' => '', 'description' => 'Quản lý và phát triển nguồn nhân lực'],
                            ['name' => 'Quản trị tài chính', 'image' => '', 'description' => 'Quản lý tài chính doanh nghiệp'],
                            ['name' => 'Chiến lược kinh doanh', 'image' => '', 'description' => 'Xây dựng và thực thi chiến lược'],
                        ]
                    ],
                ]
            ];

            // chance: {description, tags: [{icon, name, color}], job: [{image, name, description, salary}]}
            $neuChance = [
                'description' => 'Sinh viên tốt nghiệp có nhiều cơ hội việc làm trong các lĩnh vực quản lý, marketing, tài chính',
                'tags' => [
                    ['icon' => '', 'name' => 'Quản lý', 'color' => '#FF5733'],
                    ['icon' => '', 'name' => 'Marketing', 'color' => '#33FF57'],
                    ['icon' => '', 'name' => 'Tài chính', 'color' => '#3357FF'],
                ],
                'job' => [
                    ['image' => '', 'name' => 'Giám đốc điều hành', 'description' => 'Quản lý và điều hành doanh nghiệp', 'salary' => '30-50 triệu/tháng'],
                    ['image' => '', 'name' => 'Trưởng phòng Marketing', 'description' => 'Quản lý hoạt động marketing', 'salary' => '20-35 triệu/tháng'],
                    ['image' => '', 'name' => 'Chuyên viên tài chính', 'description' => 'Phân tích và quản lý tài chính', 'salary' => '15-25 triệu/tháng'],
                ]
            ];

            // school: {description, image, note}
            $neuSchool = [
                'description' => 'NEU là trường đại học hàng đầu về kinh tế và quản trị kinh doanh tại Việt Nam',
                'image' => '',
                'note' => 'Học phí: 15 triệu/năm',
            ];

            // value: {name, image, description, items: [{icon, name}]}
            $neuValue = [
                'name' => 'Giá trị văn bằng NEU',
                'image' => '',
                'description' => 'Văn bằng được Bộ GD&ĐT công nhận, có giá trị quốc gia và quốc tế',
                'items' => [
                    ['icon' => '', 'name' => 'Được Bộ GD&ĐT công nhận'],
                    ['icon' => '', 'name' => 'Có giá trị quốc gia'],
                    ['icon' => '', 'name' => 'Có thể học tiếp lên Thạc sĩ'],
                ]
            ];

            // feedback: {description, items: [{image, name, position, description}]}
            $neuFeedback = [
                'description' => 'Cảm nhận của học viên về chương trình đào tạo',
                'items' => [
                    ['image' => '', 'name' => 'Nguyễn Văn A', 'position' => 'Cựu sinh viên', 'description' => 'Chương trình học rất bổ ích, giúp tôi phát triển sự nghiệp'],
                    ['image' => '', 'name' => 'Trần Thị B', 'position' => 'Sinh viên năm 3', 'description' => 'Giảng viên nhiệt tình, kiến thức thực tế'],
                ]
            ];

            // event: number[] - Lấy một số post IDs cho sự kiện
            $neuEvent = !empty($eventPostIds) ? array_slice($eventPostIds, 0, 3) : [];

            // Attach language data for NEU - không dùng json_encode vì attach() sẽ tự động xử lý
            // Nhưng attach() không tự động cast qua pivot model, nên cần dùng DB::table() trực tiếp
            DB::table('major_language')->insert([
                'major_id' => $neuMajor->id,
                'language_id' => $languageId,
                'name' => 'Ngành Quản trị Kinh doanh - NEU',
                'description' => 'Chương trình đào tạo Quản trị Kinh doanh tại Đại học Kinh tế Quốc dân (NEU)',
                'content' => '<p>Chương trình đào tạo Quản trị Kinh doanh tại NEU cung cấp kiến thức toàn diện về quản lý doanh nghiệp, marketing, tài chính và nhân sự.</p>',
                'training_system' => 'Đại học',
                'study_method' => 'Từ xa',
                'admission_method' => 'Xét tuyển',
                'enrollment_quota' => '500',
                'enrollment_period' => 'Tháng 1, 5, 9',
                'admission_type' => 'Xét tuyển học bạ',
                'degree_type' => 'Cử nhân',
                'training_duration' => '4 năm',
                'feature' => json_encode($neuFeatures, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'target' => json_encode($neuTargets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'address' => json_encode($neuAddresses, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'overview' => json_encode($neuOverview, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'who' => json_encode($neuWho, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'priority' => json_encode($neuPriority, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'learn' => json_encode($neuLearn, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'chance' => json_encode($neuChance, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'school' => json_encode($neuSchool, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'value' => json_encode($neuValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'feedback' => json_encode($neuFeedback, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'event' => json_encode($neuEvent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'canonical' => 'nganh-quan-tri-kinh-doanh-neu',
                'meta_title' => 'Ngành Quản trị Kinh doanh NEU',
                'meta_keyword' => 'quản trị kinh doanh, NEU, đại học kinh tế quốc dân',
                'meta_description' => 'Thông tin về ngành Quản trị Kinh doanh tại Đại học Kinh tế Quốc dân',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create router for NEU
            $this->routerRepository->create([
                'canonical' => 'nganh-quan-tri-kinh-doanh-neu',
                'module_id' => $neuMajor->id,
                'language_id' => $languageId,
                'controllers' => 'App\Http\Controllers\Frontend\MajorController',
            ]);

            // Create Bách Khoa Major
            $bkMajor = Major::create([
                'subtitle' => 'Đại học Bách Khoa Hà Nội',
                'image' => '',
                'publish' => 2,
                'user_id' => $user->id,
                'study_path_file' => '',
                'is_show_feature' => 2,
                'is_show_overview' => 2,
                'is_show_who' => 2,
                'is_show_priority' => 2,
                'is_show_learn' => 2,
                'is_show_chance' => 2,
                'is_show_school' => 2,
                'is_show_value' => 2,
                'is_show_feedback' => 2,
                'is_show_event' => 2,
            ]);

            // Prepare JSON data for Bách Khoa
            // feature: [{name, image}]
            $bkFeatures = [
                ['name' => 'Chương trình chuẩn quốc tế', 'image' => ''],
                ['name' => 'Thực hành nhiều', 'image' => ''],
                ['name' => 'Giảng viên giàu kinh nghiệm', 'image' => ''],
                ['name' => 'Cơ hội việc làm cao', 'image' => ''],
                ['name' => 'Liên kết doanh nghiệp', 'image' => ''],
            ];

            // target: string[]
            $bkTargets = [
                'Tốt nghiệp THPT hoặc tương đương',
                'Có bằng trung cấp, cao đẳng ngành CNTT',
                'Người đang làm việc trong ngành IT',
                'Người muốn chuyển sang ngành công nghệ',
            ];

            // address: [{name, address}]
            $bkAddresses = [
                ['name' => 'Văn phòng tuyển sinh Bách Khoa', 'address' => 'Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội'],
                ['name' => 'Khoa Công nghệ Thông tin', 'address' => 'Tòa nhà C1, Đại học Bách Khoa Hà Nội'],
                ['name' => 'Chi nhánh Đà Nẵng', 'address' => '54 Nguyễn Lương Bằng, Đà Nẵng'],
            ];

            // overview: {name, description, image, items: [{image, name, description}, ...]}
            $bkOverview = [
                'name' => 'Toàn cảnh ngành Công nghệ Thông tin tại Bách Khoa',
                'description' => '<p>Ngành Công nghệ Thông tin tại Bách Khoa là một trong những ngành đào tạo hàng đầu về công nghệ tại Việt Nam.</p>',
                'image' => '',
                'items' => [
                    ['image' => '', 'name' => 'Công nghệ hiện đại', 'description' => 'Học các công nghệ mới nhất trong lĩnh vực CNTT'],
                    ['image' => '', 'name' => 'Thực hành nhiều', 'description' => 'Chương trình chú trọng thực hành và dự án thực tế'],
                    ['image' => '', 'name' => 'Cơ hội việc làm cao', 'description' => 'Tỷ lệ có việc làm sau tốt nghiệp lên đến 95%'],
                ]
            ];

            // who: [{name, image, description, person}]
            $bkWho = [
                ['name' => 'Người yêu thích công nghệ', 'image' => '', 'description' => '<p>Phù hợp với những người đam mê lập trình và công nghệ</p>', 'person' => 'Sinh viên tốt nghiệp THPT'],
                ['name' => 'Người muốn làm việc trong ngành IT', 'image' => '', 'description' => '<p>Dành cho những người muốn phát triển sự nghiệp trong lĩnh vực công nghệ</p>', 'person' => 'Người đi làm muốn chuyển ngành'],
            ];

            // priority: [{name, image, description}]
            $bkPriority = [
                ['name' => 'Học phí hợp lý', 'image' => '', 'description' => 'Học phí chỉ 18 triệu/năm'],
                ['name' => 'Cơ sở vật chất hiện đại', 'image' => '', 'description' => 'Phòng lab đầy đủ thiết bị công nghệ'],
                ['name' => 'Liên kết với doanh nghiệp', 'image' => '', 'description' => 'Có nhiều cơ hội thực tập tại các công ty lớn'],
            ];

            // learn: {description, items: [{name, items: [{name, image, description}]}]}
            $bkLearn = [
                'description' => 'Chương trình học cung cấp kiến thức toàn diện về công nghệ thông tin',
                'items' => [
                    [
                        'name' => 'Lập trình cơ bản',
                        'items' => [
                            ['name' => 'Lập trình C/C++', 'image' => '', 'description' => 'Học ngôn ngữ lập trình cơ bản'],
                            ['name' => 'Lập trình Java', 'image' => '', 'description' => 'Lập trình hướng đối tượng với Java'],
                            ['name' => 'Lập trình Web', 'image' => '', 'description' => 'HTML, CSS, JavaScript'],
                        ]
                    ],
                    [
                        'name' => 'Công nghệ nâng cao',
                        'items' => [
                            ['name' => 'Trí tuệ nhân tạo', 'image' => '', 'description' => 'Machine Learning và AI'],
                            ['name' => 'Cloud Computing', 'image' => '', 'description' => 'Điện toán đám mây'],
                            ['name' => 'Blockchain', 'image' => '', 'description' => 'Công nghệ blockchain'],
                        ]
                    ],
                ]
            ];

            // chance: {description, tags: [{icon, name, color}], job: [{image, name, description, salary}]}
            $bkChance = [
                'description' => 'Sinh viên tốt nghiệp có nhiều cơ hội việc làm trong các công ty công nghệ, phần mềm',
                'tags' => [
                    ['icon' => '', 'name' => 'Lập trình viên', 'color' => '#FF5733'],
                    ['icon' => '', 'name' => 'Phát triển phần mềm', 'color' => '#33FF57'],
                    ['icon' => '', 'name' => 'Quản trị hệ thống', 'color' => '#3357FF'],
                ],
                'job' => [
                    ['image' => '', 'name' => 'Lập trình viên Full-stack', 'description' => 'Phát triển ứng dụng web và mobile', 'salary' => '20-40 triệu/tháng'],
                    ['image' => '', 'name' => 'Kỹ sư phần mềm', 'description' => 'Thiết kế và phát triển phần mềm', 'salary' => '25-45 triệu/tháng'],
                    ['image' => '', 'name' => 'Chuyên viên bảo mật', 'description' => 'Bảo mật thông tin và hệ thống', 'salary' => '30-50 triệu/tháng'],
                ]
            ];

            // school: {description, image, note}
            $bkSchool = [
                'description' => 'Bách Khoa là trường đại học hàng đầu về kỹ thuật và công nghệ tại Việt Nam',
                'image' => '',
                'note' => 'Học phí: 18 triệu/năm',
            ];

            // value: {name, image, description, items: [{icon, name}]}
            $bkValue = [
                'name' => 'Giá trị văn bằng Bách Khoa',
                'image' => '',
                'description' => 'Văn bằng được Bộ GD&ĐT công nhận, có giá trị quốc gia và quốc tế',
                'items' => [
                    ['icon' => '', 'name' => 'Được Bộ GD&ĐT công nhận'],
                    ['icon' => '', 'name' => 'Có giá trị quốc gia'],
                    ['icon' => '', 'name' => 'Có thể học tiếp lên Thạc sĩ'],
                ]
            ];

            // feedback: {description, items: [{image, name, position, description}]}
            $bkFeedback = [
                'description' => 'Cảm nhận của học viên về chương trình đào tạo',
                'items' => [
                    ['image' => '', 'name' => 'Lê Văn C', 'position' => 'Cựu sinh viên', 'description' => 'Chương trình học rất thực tế, giúp tôi có việc làm ngay sau tốt nghiệp'],
                    ['image' => '', 'name' => 'Phạm Thị D', 'position' => 'Sinh viên năm 4', 'description' => 'Kiến thức cập nhật, giảng viên nhiệt tình'],
                ]
            ];

            // event: number[] - Lấy một số post IDs cho sự kiện (lấy các post còn lại)
            $bkEvent = !empty($eventPostIds) && count($eventPostIds) > 3 ? array_slice($eventPostIds, 3) : (!empty($eventPostIds) ? $eventPostIds : []);

            // Insert language data for Bách Khoa - MySQL JSON column sẽ tự động xử lý, không cần json_encode
            DB::table('major_language')->insert([
                'major_id' => $bkMajor->id,
                'language_id' => $languageId,
                'name' => 'Ngành Công nghệ Thông tin - Bách Khoa',
                'description' => 'Chương trình đào tạo Công nghệ Thông tin tại Đại học Bách Khoa Hà Nội',
                'content' => '<p>Chương trình đào tạo Công nghệ Thông tin tại Bách Khoa cung cấp kiến thức về lập trình, hệ thống thông tin, và công nghệ phần mềm.</p>',
                'training_system' => 'Đại học',
                'study_method' => 'Từ xa',
                'admission_method' => 'Xét tuyển',
                'enrollment_quota' => '300',
                'enrollment_period' => 'Tháng 1, 5, 9',
                'admission_type' => 'Xét tuyển học bạ',
                'degree_type' => 'Cử nhân',
                'training_duration' => '4 năm',
                'feature' => json_encode($bkFeatures, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'target' => json_encode($bkTargets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'address' => json_encode($bkAddresses, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'overview' => json_encode($bkOverview, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'who' => json_encode($bkWho, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'priority' => json_encode($bkPriority, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'learn' => json_encode($bkLearn, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'chance' => json_encode($bkChance, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'school' => json_encode($bkSchool, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'value' => json_encode($bkValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'feedback' => json_encode($bkFeedback, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'event' => json_encode($bkEvent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'canonical' => 'nganh-cong-nghe-thong-tin-bach-khoa',
                'meta_title' => 'Ngành Công nghệ Thông tin Bách Khoa',
                'meta_keyword' => 'công nghệ thông tin, Bách Khoa, đại học bách khoa hà nội',
                'meta_description' => 'Thông tin về ngành Công nghệ Thông tin tại Đại học Bách Khoa Hà Nội',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create router for Bách Khoa
            $this->routerRepository->create([
                'canonical' => 'nganh-cong-nghe-thong-tin-bach-khoa',
                'module_id' => $bkMajor->id,
                'language_id' => $languageId,
                'controllers' => 'App\Http\Controllers\Frontend\MajorController',
            ]);

            DB::commit();
            $this->command->info('✓ Đã tạo thành công 2 ngành học: NEU và Bách Khoa với đầy đủ dữ liệu demo');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Lỗi: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
}
