<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class CreateMajorPermissions extends Command
{
    protected $signature = 'major:create-permissions';
    protected $description = 'Tạo permissions cho module Major';

    public function handle()
    {
        $this->info('Bắt đầu tạo permissions cho module Major...');

        $permissions = [
            [
                'name' => 'Xem danh sách ngành học',
                'canonical' => 'major.index'
            ],
            [
                'name' => 'Thêm mới ngành học',
                'canonical' => 'major.create'
            ],
            [
                'name' => 'Cập nhật ngành học',
                'canonical' => 'major.update'
            ],
            [
                'name' => 'Xóa ngành học',
                'canonical' => 'major.destroy'
            ]
        ];

        DB::beginTransaction();
        try {
            foreach ($permissions as $permission) {
                $existing = Permission::where('canonical', $permission['canonical'])->first();
                if (!$existing) {
                    Permission::create($permission);
                    $this->info("✓ Đã tạo permission: {$permission['canonical']}");
                } else {
                    $this->warn("⚠ Permission đã tồn tại: {$permission['canonical']}");
                }
            }
            DB::commit();
            $this->info('Hoàn thành! Đã tạo permissions cho module Major.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
