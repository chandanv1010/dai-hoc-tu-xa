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
        Schema::table('major_language', function (Blueprint $table) {
            // Đổi event từ JSON sang integer (unsignedBigInteger)
            if (Schema::hasColumn('major_language', 'event')) {
                // Kiểm tra kiểu cột hiện tại
                $columnInfo = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'major_language' 
                    AND COLUMN_NAME = 'event'");
                
                if (!empty($columnInfo) && strtolower($columnInfo[0]->DATA_TYPE) === 'json') {
                    // Nếu là JSON, tạo cột tạm thời để convert dữ liệu
                    if (!Schema::hasColumn('major_language', 'event_temp')) {
                        $table->unsignedBigInteger('event_temp')->nullable()->after('feedback');
                    }
                    
                    // Lấy tất cả dữ liệu event hiện tại và convert
                    $records = DB::table('major_language')
                        ->whereNotNull('event')
                        ->get(['id', 'event']);
                    
                    // Convert dữ liệu JSON sang integer và lưu vào cột tạm
                    foreach ($records as $record) {
                        $eventValue = $record->event;
                        $eventId = null;
                        
                        // Lấy giá trị từ JSON string
                        if (is_string($eventValue)) {
                            $decoded = json_decode($eventValue, true);
                            if (is_array($decoded) && isset($decoded['post_catalogue_id'])) {
                                $eventId = (int)$decoded['post_catalogue_id'];
                            } elseif (is_numeric($eventValue)) {
                                $eventId = (int)$eventValue;
                            }
                        } elseif (is_array($eventValue) && isset($eventValue['post_catalogue_id'])) {
                            $eventId = (int)$eventValue['post_catalogue_id'];
                        } elseif (is_numeric($eventValue)) {
                            $eventId = (int)$eventValue;
                        }
                        
                        // Update với giá trị integer vào cột tạm
                        if ($eventId !== null && $eventId > 0) {
                            DB::table('major_language')
                                ->where('id', $record->id)
                                ->update(['event_temp' => $eventId]);
                        }
                    }
                    
                    // Xóa cột cũ và đổi tên cột tạm
                    $table->dropColumn('event');
                    DB::statement('ALTER TABLE `major_language` CHANGE COLUMN `event_temp` `event` BIGINT UNSIGNED NULL COMMENT \'ID sự kiện (post_catalogue_id)\'');
                } else {
                    // Nếu đã là integer hoặc kiểu khác, chỉ cần đảm bảo đúng kiểu
                    DB::statement('ALTER TABLE `major_language` MODIFY COLUMN `event` BIGINT UNSIGNED NULL COMMENT \'ID sự kiện (post_catalogue_id)\'');
                }
            } else {
                // Nếu chưa có cột, thêm mới
                $table->unsignedBigInteger('event')->nullable()->comment('ID sự kiện (post_catalogue_id)')->after('feedback');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_language', function (Blueprint $table) {
            // Khôi phục lại JSON nếu cần rollback
            if (Schema::hasColumn('major_language', 'event')) {
                $table->dropColumn('event');
            }
            $table->json('event')->nullable()->comment('Sự kiện (number[])')->after('feedback');
        });
    }
};
