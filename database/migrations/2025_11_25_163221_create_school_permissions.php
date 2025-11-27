<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tạo permissions cho School
        $permissions = [
            [
                'name' => 'Xem danh sách trường học',
                'canonical' => 'school.index',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thêm mới trường học',
                'canonical' => 'school.create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cập nhật trường học',
                'canonical' => 'school.update',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Xóa trường học',
                'canonical' => 'school.destroy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            $permissionId = DB::table('permissions')->insertGetId($permission);
            
            // Gán permission cho user_catalogue_id = 1 (giống như Major)
            DB::table('user_catalogue_permission')->insert([
                'user_catalogue_id' => 1,
                'permission_id' => $permissionId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa permissions và user_catalogue_permission
        $permissionCanonicals = ['school.index', 'school.create', 'school.update', 'school.destroy'];
        
        foreach ($permissionCanonicals as $canonical) {
            $permission = DB::table('permissions')->where('canonical', $canonical)->first();
            if ($permission) {
                DB::table('user_catalogue_permission')->where('permission_id', $permission->id)->delete();
                DB::table('permissions')->where('id', $permission->id)->delete();
            }
        }
    }
};
