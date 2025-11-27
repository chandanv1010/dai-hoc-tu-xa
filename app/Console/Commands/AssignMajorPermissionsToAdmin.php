<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use App\Models\UserCatalogue;
use Illuminate\Support\Facades\DB;

class AssignMajorPermissionsToAdmin extends Command
{
    protected $signature = 'major:assign-permissions-to-admin';
    protected $description = 'Gán tất cả permissions của Major cho User Catalogue Admin';

    public function handle()
    {
        $this->info('Bắt đầu gán permissions cho Admin...');

        // Tìm User Catalogue Admin (thường là id = 1 hoặc có tên chứa "admin")
        $adminCatalogue = UserCatalogue::where('id', 1)
            ->orWhere('name', 'like', '%admin%')
            ->orWhere('name', 'like', '%Admin%')
            ->first();

        if (!$adminCatalogue) {
            $this->error('Không tìm thấy User Catalogue Admin. Vui lòng kiểm tra lại.');
            return 1;
        }

        // Lấy tất cả permissions của Major
        $majorPermissions = Permission::where('canonical', 'like', 'major.%')->get();

        if ($majorPermissions->isEmpty()) {
            $this->error('Không tìm thấy permissions của Major. Vui lòng chạy: php artisan major:create-permissions');
            return 1;
        }

        DB::beginTransaction();
        try {
            // Lấy các permission IDs
            $permissionIds = $majorPermissions->pluck('id')->toArray();
            
            // Lấy các permissions hiện tại của admin
            $currentPermissions = $adminCatalogue->permissions()->pluck('permissions.id')->toArray();
            
            // Merge với permissions mới
            $allPermissions = array_unique(array_merge($currentPermissions, $permissionIds));
            
            // Sync permissions
            $adminCatalogue->permissions()->sync($allPermissions);
            
            DB::commit();
            
            $this->info("✓ Đã gán " . count($permissionIds) . " permissions cho User Catalogue: {$adminCatalogue->name} (ID: {$adminCatalogue->id})");
            $this->info('Hoàn thành!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
