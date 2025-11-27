<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\Language;
use App\Models\User;
use App\Models\Post;
use App\Models\Major;
use App\Repositories\RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolSeeder extends Seeder
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

            // Get some post IDs for events
            $eventPostIds = Post::where('publish', 2)
                ->orderBy('id', 'asc')
                ->limit(5)
                ->pluck('id')
                ->toArray();

            // Get some major IDs
            $majorIds = Major::where('publish', 2)
                ->orderBy('id', 'asc')
                ->limit(3)
                ->pluck('id')
                ->toArray();

            // Delete existing schools and related data
            $existingSchools = School::all();
            foreach ($existingSchools as $existingSchool) {
                // Delete router
                DB::table('routers')->where('module_id', $existingSchool->id)->where('controllers', 'App\Http\Controllers\Frontend\SchoolController')->delete();
                // Delete school (this will cascade delete school_language records)
                $existingSchool->delete();
            }
            
            // Also delete any orphaned routers
            DB::table('routers')->where('controllers', 'App\Http\Controllers\Frontend\SchoolController')->delete();

            // School 1: Đại học Kinh tế Quốc dân
            $school1 = School::create([
                'image' => '',
                'publish' => 2,
                'user_id' => $user->id,
                'statistics_majors' => 25,
                'statistics_students' => 15000,
                'statistics_courses' => 120,
                'statistics_satisfaction' => 95,
                'statistics_employment' => 92,
                'is_show_statistics' => 2,
                'is_show_intro' => 2,
                'is_show_announce' => 2,
                'is_show_advantage' => 2,
                'is_show_suitable' => 2,
                'is_show_majors' => 2,
                'is_show_study_method' => 2,
                'is_show_feedback' => 2,
                'is_show_event' => 2,
                'is_show_value' => 2,
            ]);

            $school1Intro = [
                'name' => 'Đại học Kinh tế Quốc dân',
                'description' => 'Trường đại học hàng đầu về đào tạo kinh tế và quản trị kinh doanh tại Việt Nam',
                'created' => '1956',
                'top' => 'Top 5',
                'percent' => '95%',
            ];

            $school1Announce = [
                'description' => 'Thông báo tuyển sinh năm 2025',
                'content' => '<p>Trường Đại học Kinh tế Quốc dân thông báo tuyển sinh các chương trình đào tạo từ xa năm 2025.</p>',
                'target' => '<p>Tốt nghiệp THPT hoặc tương đương</p>',
                'type' => '<p>Đại học, Thạc sĩ</p>',
                'request' => '<p>Hồ sơ đầy đủ, đúng quy định</p>',
                'address' => 'Số 207 Giải Phóng, Hai Bà Trưng, Hà Nội',
                'value' => '<p>Bằng cấp được công nhận, có giá trị quốc gia</p>',
            ];

            $school1Advantage = [
                'title' => 'Ưu điểm nổi bật',
                'description' => 'Những ưu điểm khi học tại NEU',
                'items' => [
                    ['name' => 'Chương trình chất lượng', 'description' => 'Chương trình đào tạo được kiểm định chất lượng', 'icon' => '', 'note' => 'Được Bộ GD&ĐT công nhận'],
                    ['name' => 'Giảng viên giàu kinh nghiệm', 'description' => 'Đội ngũ giảng viên có trình độ cao', 'icon' => '', 'note' => '100% có bằng thạc sĩ trở lên'],
                    ['name' => 'Cơ sở vật chất hiện đại', 'description' => 'Phòng học, thư viện đầy đủ tiện nghi', 'icon' => '', 'note' => 'Thư viện điện tử 24/7'],
                ],
            ];

            $school1Suitable = [
                'name' => 'Phù hợp với',
                'description' => 'Chương trình phù hợp với nhiều đối tượng',
                'items' => [
                    ['image' => '', 'name' => 'Sinh viên mới tốt nghiệp', 'description' => 'Muốn nâng cao trình độ'],
                    ['image' => '', 'name' => 'Người đi làm', 'description' => 'Cần bằng cấp để thăng tiến'],
                    ['image' => '', 'name' => 'Người chuyển nghề', 'description' => 'Muốn đổi sang ngành kinh tế'],
                ],
            ];

            $school1Majors = [];
            if (!empty($majorIds)) {
                foreach (array_slice($majorIds, 0, 2) as $index => $majorId) {
                    $school1Majors[] = [
                        'major_id' => $majorId,
                        'admission_method' => 'Xét tuyển học bạ',
                        'duration' => '4 năm',
                        'tuition' => '15.000.000 VNĐ/năm',
                        'location' => 'Hà Nội',
                        'annual_tuition' => '15.000.000 VNĐ',
                        'credits' => '120 tín chỉ',
                        'tuition_per_credit' => '125.000 VNĐ/tín chỉ',
                    ];
                }
            }

            $school1StudyMethod = [
                'name' => 'Hình thức học tập',
                'description' => 'Đa dạng hình thức học tập phù hợp với mọi đối tượng',
                'image' => '',
                'content' => '<p>Chương trình đào tạo từ xa linh hoạt, học viên có thể học mọi lúc mọi nơi.</p>',
                'items' => [
                    ['image' => '', 'name' => 'Học online', 'description' => 'Học trực tuyến qua hệ thống E-Learning'],
                    ['image' => '', 'name' => 'Học offline', 'description' => 'Học tập trung tại cơ sở'],
                    ['image' => '', 'name' => 'Học kết hợp', 'description' => 'Kết hợp online và offline'],
                ],
            ];

            $school1Feedback = [
                'description' => 'Cảm nhận của học viên về chương trình đào tạo',
                'items' => [
                    ['image' => '', 'name' => 'Nguyễn Văn A', 'position' => 'Sinh viên khóa 2023', 'description' => 'Chương trình học rất hay, giảng viên nhiệt tình'],
                    ['image' => '', 'name' => 'Trần Thị B', 'position' => 'Sinh viên khóa 2022', 'description' => 'Học từ xa rất tiện lợi, phù hợp với người đi làm'],
                ],
            ];

            $school1Event = !empty($eventPostIds) ? array_slice($eventPostIds, 0, 3) : [];

            $school1Value = [
                'name' => 'Giá trị văn bằng',
                'image' => '',
                'description' => 'Bằng cấp có giá trị quốc gia và quốc tế',
                'items' => [
                    ['icon' => '', 'name' => 'Được Bộ GD&ĐT công nhận'],
                    ['icon' => '', 'name' => 'Có giá trị quốc gia'],
                    ['icon' => '', 'name' => 'Được các doanh nghiệp đánh giá cao'],
                ],
            ];

            DB::table('school_language')->insert([
                'school_id' => $school1->id,
                'language_id' => $languageId,
                'name' => 'Đại học Kinh tế Quốc dân',
                'description' => 'Trường đại học hàng đầu về đào tạo kinh tế và quản trị kinh doanh tại Việt Nam',
                'content' => '<p>Đại học Kinh tế Quốc dân (NEU) là một trong những trường đại học hàng đầu về đào tạo kinh tế và quản trị kinh doanh tại Việt Nam. Với hơn 60 năm phát triển, NEU đã đào tạo hàng nghìn cử nhân, thạc sĩ và tiến sĩ cho đất nước.</p>',
                'canonical' => 'dai-hoc-kinh-te-quoc-dan',
                'meta_title' => 'Đại học Kinh tế Quốc dân - NEU',
                'meta_keyword' => 'NEU, đại học kinh tế quốc dân, đào tạo từ xa',
                'meta_description' => 'Thông tin về Đại học Kinh tế Quốc dân, chương trình đào tạo từ xa',
                'intro' => json_encode($school1Intro, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'announce' => json_encode($school1Announce, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'advantage' => json_encode($school1Advantage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'suitable' => json_encode($school1Suitable, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'majors' => json_encode($school1Majors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'study_method' => json_encode($school1StudyMethod, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'feedback' => json_encode($school1Feedback, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'event' => json_encode($school1Event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'value' => json_encode($school1Value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->routerRepository->create([
                'canonical' => 'dai-hoc-kinh-te-quoc-dan',
                'module_id' => $school1->id,
                'language_id' => $languageId,
                'controllers' => 'App\Http\Controllers\Frontend\SchoolController',
            ]);

            // School 2: Đại học Bách Khoa Hà Nội
            $school2 = School::create([
                'image' => '',
                'publish' => 2,
                'user_id' => $user->id,
                'statistics_majors' => 30,
                'statistics_students' => 20000,
                'statistics_courses' => 150,
                'statistics_satisfaction' => 96,
                'statistics_employment' => 94,
                'is_show_statistics' => 2,
                'is_show_intro' => 2,
                'is_show_announce' => 2,
                'is_show_advantage' => 2,
                'is_show_suitable' => 2,
                'is_show_majors' => 2,
                'is_show_study_method' => 2,
                'is_show_feedback' => 2,
                'is_show_event' => 2,
                'is_show_value' => 2,
            ]);

            $school2Intro = [
                'name' => 'Đại học Bách Khoa Hà Nội',
                'description' => 'Trường đại học hàng đầu về đào tạo kỹ thuật và công nghệ tại Việt Nam',
                'created' => '1956',
                'top' => 'Top 3',
                'percent' => '96%',
            ];

            $school2Announce = [
                'description' => 'Thông báo tuyển sinh năm 2025',
                'content' => '<p>Trường Đại học Bách Khoa Hà Nội thông báo tuyển sinh các chương trình đào tạo từ xa năm 2025.</p>',
                'target' => '<p>Tốt nghiệp THPT hoặc tương đương, có nền tảng toán lý hóa tốt</p>',
                'type' => '<p>Đại học, Thạc sĩ Kỹ thuật</p>',
                'request' => '<p>Hồ sơ đầy đủ, có bằng tốt nghiệp THPT</p>',
                'address' => 'Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội',
                'value' => '<p>Bằng cấp được công nhận, có giá trị quốc gia và quốc tế</p>',
            ];

            $school2Advantage = [
                'title' => 'Ưu điểm nổi bật',
                'description' => 'Những ưu điểm khi học tại Bách Khoa',
                'items' => [
                    ['name' => 'Chương trình chuẩn quốc tế', 'description' => 'Chương trình đào tạo theo chuẩn ABET', 'icon' => '', 'note' => 'Được công nhận quốc tế'],
                    ['name' => 'Thực hành nhiều', 'description' => 'Chú trọng thực hành và dự án thực tế', 'icon' => '', 'note' => '70% thời gian thực hành'],
                    ['name' => 'Cơ hội việc làm cao', 'description' => 'Tỷ lệ có việc làm sau tốt nghiệp cao', 'icon' => '', 'note' => '95% có việc làm trong 6 tháng'],
                ],
            ];

            $school2Suitable = [
                'name' => 'Phù hợp với',
                'description' => 'Chương trình phù hợp với nhiều đối tượng',
                'items' => [
                    ['image' => '', 'name' => 'Sinh viên yêu thích kỹ thuật', 'description' => 'Muốn học về công nghệ'],
                    ['image' => '', 'name' => 'Kỹ sư đang làm việc', 'description' => 'Cần nâng cao trình độ'],
                    ['image' => '', 'name' => 'Người muốn chuyển nghề', 'description' => 'Muốn làm việc trong ngành công nghệ'],
                ],
            ];

            $school2Majors = [];
            if (!empty($majorIds)) {
                foreach (array_slice($majorIds, 0, 2) as $index => $majorId) {
                    $school2Majors[] = [
                        'major_id' => $majorId,
                        'admission_method' => 'Xét tuyển học bạ + Thi đầu vào',
                        'duration' => '4.5 năm',
                        'tuition' => '18.000.000 VNĐ/năm',
                        'location' => 'Hà Nội, Đà Nẵng',
                        'annual_tuition' => '18.000.000 VNĐ',
                        'credits' => '135 tín chỉ',
                        'tuition_per_credit' => '133.000 VNĐ/tín chỉ',
                    ];
                }
            }

            $school2StudyMethod = [
                'name' => 'Hình thức học tập',
                'description' => 'Đa dạng hình thức học tập',
                'image' => '',
                'content' => '<p>Chương trình đào tạo kết hợp lý thuyết và thực hành, chú trọng kỹ năng thực tế.</p>',
                'items' => [
                    ['image' => '', 'name' => 'Học lý thuyết', 'description' => 'Học các môn lý thuyết cơ bản'],
                    ['image' => '', 'name' => 'Thực hành tại phòng lab', 'description' => 'Thực hành tại các phòng thí nghiệm hiện đại'],
                    ['image' => '', 'name' => 'Thực tập doanh nghiệp', 'description' => 'Thực tập tại các doanh nghiệp đối tác'],
                ],
            ];

            $school2Feedback = [
                'description' => 'Cảm nhận của học viên',
                'items' => [
                    ['image' => '', 'name' => 'Lê Văn C', 'position' => 'Sinh viên khóa 2023', 'description' => 'Chương trình học rất thực tế, giúp em có nhiều kỹ năng'],
                    ['image' => '', 'name' => 'Phạm Thị D', 'position' => 'Sinh viên khóa 2022', 'description' => 'Giảng viên rất nhiệt tình, cơ sở vật chất tốt'],
                ],
            ];

            $school2Event = !empty($eventPostIds) ? array_slice($eventPostIds, 0, 2) : [];

            $school2Value = [
                'name' => 'Giá trị văn bằng',
                'image' => '',
                'description' => 'Bằng cấp có giá trị cao',
                'items' => [
                    ['icon' => '', 'name' => 'Được công nhận quốc tế'],
                    ['icon' => '', 'name' => 'Có giá trị tại các doanh nghiệp lớn'],
                    ['icon' => '', 'name' => 'Được đánh giá cao trong ngành'],
                ],
            ];

            DB::table('school_language')->insert([
                'school_id' => $school2->id,
                'language_id' => $languageId,
                'name' => 'Đại học Bách Khoa Hà Nội',
                'description' => 'Trường đại học hàng đầu về đào tạo kỹ thuật và công nghệ tại Việt Nam',
                'content' => '<p>Đại học Bách Khoa Hà Nội là trường đại học hàng đầu về đào tạo kỹ thuật và công nghệ tại Việt Nam. Với hơn 60 năm phát triển, trường đã đào tạo hàng nghìn kỹ sư cho đất nước.</p>',
                'canonical' => 'dai-hoc-bach-khoa-ha-noi',
                'meta_title' => 'Đại học Bách Khoa Hà Nội',
                'meta_keyword' => 'Bách Khoa, đại học bách khoa, đào tạo kỹ thuật',
                'meta_description' => 'Thông tin về Đại học Bách Khoa Hà Nội, chương trình đào tạo kỹ thuật',
                'intro' => json_encode($school2Intro, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'announce' => json_encode($school2Announce, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'advantage' => json_encode($school2Advantage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'suitable' => json_encode($school2Suitable, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'majors' => json_encode($school2Majors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'study_method' => json_encode($school2StudyMethod, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'feedback' => json_encode($school2Feedback, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'event' => json_encode($school2Event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'value' => json_encode($school2Value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->routerRepository->create([
                'canonical' => 'dai-hoc-bach-khoa-ha-noi',
                'module_id' => $school2->id,
                'language_id' => $languageId,
                'controllers' => 'App\Http\Controllers\Frontend\SchoolController',
            ]);

            // School 3: Đại học Ngoại thương
            $school3 = School::create([
                'image' => '',
                'publish' => 2,
                'user_id' => $user->id,
                'statistics_majors' => 20,
                'statistics_students' => 12000,
                'statistics_courses' => 100,
                'statistics_satisfaction' => 94,
                'statistics_employment' => 91,
                'is_show_statistics' => 2,
                'is_show_intro' => 2,
                'is_show_announce' => 2,
                'is_show_advantage' => 2,
                'is_show_suitable' => 2,
                'is_show_majors' => 2,
                'is_show_study_method' => 2,
                'is_show_feedback' => 2,
                'is_show_event' => 2,
                'is_show_value' => 2,
            ]);

            $school3Intro = [
                'name' => 'Đại học Ngoại thương',
                'description' => 'Trường đại học hàng đầu về đào tạo kinh tế đối ngoại và thương mại quốc tế',
                'created' => '1960',
                'top' => 'Top 5',
                'percent' => '94%',
            ];

            $school3Announce = [
                'description' => 'Thông báo tuyển sinh năm 2025',
                'content' => '<p>Trường Đại học Ngoại thương thông báo tuyển sinh các chương trình đào tạo từ xa năm 2025.</p>',
                'target' => '<p>Tốt nghiệp THPT, có năng khiếu ngoại ngữ</p>',
                'type' => '<p>Đại học, Thạc sĩ Kinh tế</p>',
                'request' => '<p>Hồ sơ đầy đủ, có chứng chỉ ngoại ngữ</p>',
                'address' => '91 Chùa Láng, Đống Đa, Hà Nội',
                'value' => '<p>Bằng cấp được công nhận, có giá trị quốc gia</p>',
            ];

            $school3Advantage = [
                'title' => 'Ưu điểm nổi bật',
                'description' => 'Những ưu điểm khi học tại Ngoại thương',
                'items' => [
                    ['name' => 'Chương trình quốc tế', 'description' => 'Chương trình đào tạo theo chuẩn quốc tế', 'icon' => '', 'note' => 'Liên kết với các trường quốc tế'],
                    ['name' => 'Ngoại ngữ tốt', 'description' => 'Chú trọng đào tạo ngoại ngữ', 'icon' => '', 'note' => 'Học 2 ngoại ngữ'],
                    ['name' => 'Cơ hội việc làm rộng', 'description' => 'Cơ hội làm việc tại các công ty đa quốc gia', 'icon' => '', 'note' => '95% có việc làm tốt'],
                ],
            ];

            $school3Suitable = [
                'name' => 'Phù hợp với',
                'description' => 'Chương trình phù hợp với nhiều đối tượng',
                'items' => [
                    ['image' => '', 'name' => 'Sinh viên yêu thích kinh tế', 'description' => 'Muốn học về thương mại quốc tế'],
                    ['image' => '', 'name' => 'Người làm việc xuất nhập khẩu', 'description' => 'Cần nâng cao trình độ'],
                    ['image' => '', 'name' => 'Người muốn làm việc quốc tế', 'description' => 'Muốn làm việc tại các công ty đa quốc gia'],
                ],
            ];

            $school3Majors = [];
            if (!empty($majorIds)) {
                foreach (array_slice($majorIds, 0, 1) as $index => $majorId) {
                    $school3Majors[] = [
                        'major_id' => $majorId,
                        'admission_method' => 'Xét tuyển học bạ',
                        'duration' => '4 năm',
                        'tuition' => '16.000.000 VNĐ/năm',
                        'location' => 'Hà Nội, TP.HCM',
                        'annual_tuition' => '16.000.000 VNĐ',
                        'credits' => '120 tín chỉ',
                        'tuition_per_credit' => '133.000 VNĐ/tín chỉ',
                    ];
                }
            }

            $school3StudyMethod = [
                'name' => 'Hình thức học tập',
                'description' => 'Đa dạng hình thức học tập',
                'image' => '',
                'content' => '<p>Chương trình đào tạo chú trọng ngoại ngữ và kỹ năng thực tế.</p>',
                'items' => [
                    ['image' => '', 'name' => 'Học online', 'description' => 'Học trực tuyến qua hệ thống E-Learning'],
                    ['image' => '', 'name' => 'Thực hành', 'description' => 'Thực hành tại các doanh nghiệp'],
                ],
            ];

            $school3Feedback = [
                'description' => 'Cảm nhận của học viên',
                'items' => [
                    ['image' => '', 'name' => 'Hoàng Văn E', 'position' => 'Sinh viên khóa 2023', 'description' => 'Chương trình học rất hay, ngoại ngữ tốt'],
                ],
            ];

            $school3Event = !empty($eventPostIds) ? array_slice($eventPostIds, 0, 2) : [];

            $school3Value = [
                'name' => 'Giá trị văn bằng',
                'image' => '',
                'description' => 'Bằng cấp có giá trị cao',
                'items' => [
                    ['icon' => '', 'name' => 'Được công nhận quốc tế'],
                    ['icon' => '', 'name' => 'Có giá trị tại các công ty đa quốc gia'],
                ],
            ];

            DB::table('school_language')->insert([
                'school_id' => $school3->id,
                'language_id' => $languageId,
                'name' => 'Đại học Ngoại thương',
                'description' => 'Trường đại học hàng đầu về đào tạo kinh tế đối ngoại và thương mại quốc tế',
                'content' => '<p>Đại học Ngoại thương là trường đại học hàng đầu về đào tạo kinh tế đối ngoại và thương mại quốc tế tại Việt Nam.</p>',
                'canonical' => 'dai-hoc-ngoai-thuong',
                'meta_title' => 'Đại học Ngoại thương',
                'meta_keyword' => 'Ngoại thương, đại học ngoại thương, thương mại quốc tế',
                'meta_description' => 'Thông tin về Đại học Ngoại thương, chương trình đào tạo thương mại quốc tế',
                'intro' => json_encode($school3Intro, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'announce' => json_encode($school3Announce, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'advantage' => json_encode($school3Advantage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'suitable' => json_encode($school3Suitable, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'majors' => json_encode($school3Majors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'study_method' => json_encode($school3StudyMethod, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'feedback' => json_encode($school3Feedback, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'event' => json_encode($school3Event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'value' => json_encode($school3Value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->routerRepository->create([
                'canonical' => 'dai-hoc-ngoai-thuong',
                'module_id' => $school3->id,
                'language_id' => $languageId,
                'controllers' => 'App\Http\Controllers\Frontend\SchoolController',
            ]);

            // School 4: Đại học Sư phạm Hà Nội
            $school4 = School::create([
                'image' => '',
                'publish' => 2,
                'user_id' => $user->id,
                'statistics_majors' => 18,
                'statistics_students' => 10000,
                'statistics_courses' => 80,
                'statistics_satisfaction' => 93,
                'statistics_employment' => 90,
                'is_show_statistics' => 2,
                'is_show_intro' => 2,
                'is_show_announce' => 2,
                'is_show_advantage' => 2,
                'is_show_suitable' => 2,
                'is_show_majors' => 2,
                'is_show_study_method' => 2,
                'is_show_feedback' => 2,
                'is_show_event' => 2,
                'is_show_value' => 2,
            ]);

            $school4Intro = [
                'name' => 'Đại học Sư phạm Hà Nội',
                'description' => 'Trường đại học hàng đầu về đào tạo giáo viên và cán bộ giáo dục',
                'created' => '1951',
                'top' => 'Top 10',
                'percent' => '93%',
            ];

            $school4Announce = [
                'description' => 'Thông báo tuyển sinh năm 2025',
                'content' => '<p>Trường Đại học Sư phạm Hà Nội thông báo tuyển sinh các chương trình đào tạo từ xa năm 2025.</p>',
                'target' => '<p>Tốt nghiệp THPT, yêu thích nghề giáo</p>',
                'type' => '<p>Đại học Sư phạm</p>',
                'request' => '<p>Hồ sơ đầy đủ, có đam mê với nghề giáo</p>',
                'address' => '136 Xuân Thủy, Cầu Giấy, Hà Nội',
                'value' => '<p>Bằng cấp được công nhận, có thể làm giáo viên</p>',
            ];

            $school4Advantage = [
                'title' => 'Ưu điểm nổi bật',
                'description' => 'Những ưu điểm khi học tại Sư phạm Hà Nội',
                'items' => [
                    ['name' => 'Chương trình chuẩn', 'description' => 'Chương trình đào tạo theo chuẩn Bộ GD&ĐT', 'icon' => '', 'note' => 'Được Bộ GD&ĐT công nhận'],
                    ['name' => 'Thực tập nhiều', 'description' => 'Có nhiều cơ hội thực tập tại các trường', 'icon' => '', 'note' => 'Thực tập 6 tháng'],
                ],
            ];

            $school4Suitable = [
                'name' => 'Phù hợp với',
                'description' => 'Chương trình phù hợp với nhiều đối tượng',
                'items' => [
                    ['image' => '', 'name' => 'Sinh viên yêu thích nghề giáo', 'description' => 'Muốn trở thành giáo viên'],
                ],
            ];

            $school4Majors = [];
            if (!empty($majorIds)) {
                foreach (array_slice($majorIds, 0, 1) as $index => $majorId) {
                    $school4Majors[] = [
                        'major_id' => $majorId,
                        'admission_method' => 'Xét tuyển học bạ',
                        'duration' => '4 năm',
                        'tuition' => '12.000.000 VNĐ/năm',
                        'location' => 'Hà Nội',
                        'annual_tuition' => '12.000.000 VNĐ',
                        'credits' => '120 tín chỉ',
                        'tuition_per_credit' => '100.000 VNĐ/tín chỉ',
                    ];
                }
            }

            $school4StudyMethod = [
                'name' => 'Hình thức học tập',
                'description' => 'Đa dạng hình thức học tập',
                'image' => '',
                'content' => '<p>Chương trình đào tạo chú trọng kỹ năng sư phạm và thực hành giảng dạy.</p>',
                'items' => [
                    ['image' => '', 'name' => 'Học lý thuyết', 'description' => 'Học các môn lý thuyết sư phạm'],
                    ['image' => '', 'name' => 'Thực tập', 'description' => 'Thực tập tại các trường phổ thông'],
                ],
            ];

            $school4Feedback = [
                'description' => 'Cảm nhận của học viên',
                'items' => [
                    ['image' => '', 'name' => 'Vũ Thị F', 'position' => 'Sinh viên khóa 2023', 'description' => 'Chương trình học rất hay, giúp em có nhiều kỹ năng sư phạm'],
                ],
            ];

            $school4Event = !empty($eventPostIds) ? array_slice($eventPostIds, 0, 1) : [];

            $school4Value = [
                'name' => 'Giá trị văn bằng',
                'image' => '',
                'description' => 'Bằng cấp có giá trị',
                'items' => [
                    ['icon' => '', 'name' => 'Được Bộ GD&ĐT công nhận'],
                    ['icon' => '', 'name' => 'Có thể làm giáo viên'],
                ],
            ];

            DB::table('school_language')->insert([
                'school_id' => $school4->id,
                'language_id' => $languageId,
                'name' => 'Đại học Sư phạm Hà Nội',
                'description' => 'Trường đại học hàng đầu về đào tạo giáo viên và cán bộ giáo dục',
                'content' => '<p>Đại học Sư phạm Hà Nội là trường đại học hàng đầu về đào tạo giáo viên và cán bộ giáo dục tại Việt Nam.</p>',
                'canonical' => 'dai-hoc-su-pham-ha-noi',
                'meta_title' => 'Đại học Sư phạm Hà Nội',
                'meta_keyword' => 'Sư phạm, đại học sư phạm, đào tạo giáo viên',
                'meta_description' => 'Thông tin về Đại học Sư phạm Hà Nội, chương trình đào tạo giáo viên',
                'intro' => json_encode($school4Intro, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'announce' => json_encode($school4Announce, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'advantage' => json_encode($school4Advantage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'suitable' => json_encode($school4Suitable, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'majors' => json_encode($school4Majors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'study_method' => json_encode($school4StudyMethod, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'feedback' => json_encode($school4Feedback, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'event' => json_encode($school4Event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'value' => json_encode($school4Value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->routerRepository->create([
                'canonical' => 'dai-hoc-su-pham-ha-noi',
                'module_id' => $school4->id,
                'language_id' => $languageId,
                'controllers' => 'App\Http\Controllers\Frontend\SchoolController',
            ]);

            // School 5: Đại học Y Hà Nội
            $school5 = School::create([
                'image' => '',
                'publish' => 2,
                'user_id' => $user->id,
                'statistics_majors' => 15,
                'statistics_students' => 8000,
                'statistics_courses' => 60,
                'statistics_satisfaction' => 97,
                'statistics_employment' => 98,
                'is_show_statistics' => 2,
                'is_show_intro' => 2,
                'is_show_announce' => 2,
                'is_show_advantage' => 2,
                'is_show_suitable' => 2,
                'is_show_majors' => 2,
                'is_show_study_method' => 2,
                'is_show_feedback' => 2,
                'is_show_event' => 2,
                'is_show_value' => 2,
            ]);

            $school5Intro = [
                'name' => 'Đại học Y Hà Nội',
                'description' => 'Trường đại học hàng đầu về đào tạo y khoa và dược học tại Việt Nam',
                'created' => '1902',
                'top' => 'Top 1',
                'percent' => '97%',
            ];

            $school5Announce = [
                'description' => 'Thông báo tuyển sinh năm 2025',
                'content' => '<p>Trường Đại học Y Hà Nội thông báo tuyển sinh các chương trình đào tạo từ xa năm 2025.</p>',
                'target' => '<p>Tốt nghiệp THPT, có nền tảng sinh hóa tốt</p>',
                'type' => '<p>Đại học Y, Dược</p>',
                'request' => '<p>Hồ sơ đầy đủ, có bằng tốt nghiệp THPT</p>',
                'address' => '1 Tôn Thất Tùng, Đống Đa, Hà Nội',
                'value' => '<p>Bằng cấp được công nhận, có thể hành nghề y dược</p>',
            ];

            $school5Advantage = [
                'title' => 'Ưu điểm nổi bật',
                'description' => 'Những ưu điểm khi học tại Y Hà Nội',
                'items' => [
                    ['name' => 'Chương trình chuẩn quốc tế', 'description' => 'Chương trình đào tạo theo chuẩn quốc tế', 'icon' => '', 'note' => 'Được công nhận quốc tế'],
                    ['name' => 'Thực hành tại bệnh viện', 'description' => 'Có nhiều cơ hội thực hành tại các bệnh viện', 'icon' => '', 'note' => 'Thực hành tại 10 bệnh viện'],
                    ['name' => 'Cơ hội việc làm cao', 'description' => 'Tỷ lệ có việc làm sau tốt nghiệp rất cao', 'icon' => '', 'note' => '98% có việc làm'],
                ],
            ];

            $school5Suitable = [
                'name' => 'Phù hợp với',
                'description' => 'Chương trình phù hợp với nhiều đối tượng',
                'items' => [
                    ['image' => '', 'name' => 'Sinh viên yêu thích y khoa', 'description' => 'Muốn trở thành bác sĩ'],
                    ['image' => '', 'name' => 'Bác sĩ đang làm việc', 'description' => 'Cần nâng cao trình độ'],
                ],
            ];

            $school5Majors = [];
            if (!empty($majorIds)) {
                foreach (array_slice($majorIds, 0, 1) as $index => $majorId) {
                    $school5Majors[] = [
                        'major_id' => $majorId,
                        'admission_method' => 'Thi đầu vào',
                        'duration' => '6 năm',
                        'tuition' => '25.000.000 VNĐ/năm',
                        'location' => 'Hà Nội',
                        'annual_tuition' => '25.000.000 VNĐ',
                        'credits' => '180 tín chỉ',
                        'tuition_per_credit' => '139.000 VNĐ/tín chỉ',
                    ];
                }
            }

            $school5StudyMethod = [
                'name' => 'Hình thức học tập',
                'description' => 'Đa dạng hình thức học tập',
                'image' => '',
                'content' => '<p>Chương trình đào tạo chú trọng lý thuyết và thực hành tại bệnh viện.</p>',
                'items' => [
                    ['image' => '', 'name' => 'Học lý thuyết', 'description' => 'Học các môn lý thuyết y khoa'],
                    ['image' => '', 'name' => 'Thực hành tại bệnh viện', 'description' => 'Thực hành tại các bệnh viện đối tác'],
                ],
            ];

            $school5Feedback = [
                'description' => 'Cảm nhận của học viên',
                'items' => [
                    ['image' => '', 'name' => 'Đỗ Văn G', 'position' => 'Sinh viên khóa 2023', 'description' => 'Chương trình học rất hay, thực hành nhiều'],
                ],
            ];

            $school5Event = !empty($eventPostIds) ? array_slice($eventPostIds, 0, 2) : [];

            $school5Value = [
                'name' => 'Giá trị văn bằng',
                'image' => '',
                'description' => 'Bằng cấp có giá trị cao',
                'items' => [
                    ['icon' => '', 'name' => 'Được công nhận quốc tế'],
                    ['icon' => '', 'name' => 'Có thể hành nghề y dược'],
                    ['icon' => '', 'name' => 'Được đánh giá cao trong ngành'],
                ],
            ];

            DB::table('school_language')->insert([
                'school_id' => $school5->id,
                'language_id' => $languageId,
                'name' => 'Đại học Y Hà Nội',
                'description' => 'Trường đại học hàng đầu về đào tạo y khoa và dược học tại Việt Nam',
                'content' => '<p>Đại học Y Hà Nội là trường đại học hàng đầu về đào tạo y khoa và dược học tại Việt Nam. Với hơn 120 năm phát triển, trường đã đào tạo hàng nghìn bác sĩ và dược sĩ cho đất nước.</p>',
                'canonical' => 'dai-hoc-y-ha-noi',
                'meta_title' => 'Đại học Y Hà Nội',
                'meta_keyword' => 'Y Hà Nội, đại học y, đào tạo y khoa',
                'meta_description' => 'Thông tin về Đại học Y Hà Nội, chương trình đào tạo y khoa',
                'intro' => json_encode($school5Intro, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'announce' => json_encode($school5Announce, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'advantage' => json_encode($school5Advantage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'suitable' => json_encode($school5Suitable, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'majors' => json_encode($school5Majors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'study_method' => json_encode($school5StudyMethod, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'feedback' => json_encode($school5Feedback, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'event' => json_encode($school5Event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'value' => json_encode($school5Value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->routerRepository->create([
                'canonical' => 'dai-hoc-y-ha-noi',
                'module_id' => $school5->id,
                'language_id' => $languageId,
                'controllers' => 'App\Http\Controllers\Frontend\SchoolController',
            ]);

            DB::commit();
            $this->command->info('Đã tạo thành công 5 trường học với đầy đủ thông tin!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Lỗi khi tạo dữ liệu: ' . $e->getMessage());
            throw $e;
        }
    }
}
