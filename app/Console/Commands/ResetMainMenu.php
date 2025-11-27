<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\MenuCatalogueRepository;
use App\Repositories\MenuRepository;
use App\Services\MenuService;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ResetMainMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:reset-main-menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa menu cũ và tạo menu mới cho main-menu';

    protected $menuCatalogueRepository;
    protected $menuRepository;
    protected $menuService;

    /**
     * Create a new command instance.
     */
    public function __construct(
        MenuCatalogueRepository $menuCatalogueRepository,
        MenuRepository $menuRepository,
        MenuService $menuService
    ) {
        parent::__construct();
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->menuRepository = $menuRepository;
        $this->menuService = $menuService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu reset menu main-menu...');

        // Lấy language ID mặc định (language_id = 1)
        $languageId = 1;

        // Tìm menu catalogue với keyword "main-menu"
        $menuCatalogue = $this->menuCatalogueRepository->findByCondition([
            ['keyword', '=', 'main-menu']
        ], true);

        if (!$menuCatalogue || $menuCatalogue->isEmpty()) {
            $this->error('Không tìm thấy menu catalogue với keyword "main-menu"');
            return 1;
        }

        $menuCatalogue = $menuCatalogue->first();
        $this->info("Tìm thấy menu catalogue: {$menuCatalogue->name} (ID: {$menuCatalogue->id})");

        // Danh sách menu mới cần tạo
        $newMenus = [
            ['name' => 'Giới thiệu', 'canonical' => 'gioi-thieu'],
            ['name' => 'Trường Đào Tạo Từ Xa', 'canonical' => 'truong-dao-tao-tu-xa'],
            ['name' => 'Ngành đào tạo từ xa', 'canonical' => 'nganh-dao-tao-tu-xa'],
            ['name' => 'Đào tạo ngắn hạn', 'canonical' => 'dao-tao-ngan-han'],
            ['name' => 'Tin tức', 'canonical' => 'tin-tuc'],
            ['name' => 'Lịch Khai Giảng', 'canonical' => 'lich-khai-giang'],
            ['name' => 'Liên Hệ', 'canonical' => 'lien-he'],
        ];

        try {
            $this->info("Menu Catalogue ID: {$menuCatalogue->id}");
            $this->info('Đang xóa toàn bộ menu cũ của menu_catalogue_id: ' . $menuCatalogue->id);
            $this->info('Đang tạo menu mới...');
            
            // Sử dụng method resetMainMenu từ MenuService
            $result = $this->menuService->resetMainMenu(
                $menuCatalogue->id,
                $newMenus,
                $languageId
            );

            if ($result) {
                $this->info('');
                $this->info('✓ Hoàn thành! Đã tạo ' . count($newMenus) . ' menu mới:');
                $this->info('');
                foreach ($newMenus as $index => $menu) {
                    $this->line("  " . ($index + 1) . ". {$menu['name']} ({$menu['canonical']})");
                }
                $this->info('');
                $this->info('✓ Đã chạy nestedset để tính toán lft, rgt, level');
                return 0;
            } else {
                $this->error('Không thể reset menu');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

