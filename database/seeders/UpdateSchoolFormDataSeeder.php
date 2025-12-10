<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class UpdateSchoolFormDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dữ liệu demo cho Form Tải Lộ Trình Học
        $formTaiLoTrinhHocDemo = [
            'title' => 'NHẬN LỘ TRÌNH ĐÀO TẠO CHI TIẾT',
            'description' => '(Bao gồm: Khung chương trình, thời gian đào tạo, số lượng tín chỉ từng môn, học phí ...)',
            'script' => '<form id="form-tai-lo-trinh" class="consultation-form">
    <div class="form-group">
        <label for="ho-ten">Họ và Tên <span class="required">*</span></label>
        <input type="text" id="ho-ten" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="so-dien-thoai">Số điện thoại <span class="required">*</span></label>
        <input type="tel" id="so-dien-thoai" name="phone" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="nganh-hoc">Ngành học</label>
        <select id="nganh-hoc" name="major" class="form-control">
            <option value="">-- Chọn ngành học --</option>
            <option value="kinh-te">Kinh tế</option>
            <option value="cntt">Công nghệ thông tin</option>
            <option value="luat">Luật</option>
        </select>
    </div>
    <div class="form-group">
        <label for="mo-ta">Mô tả</label>
        <textarea id="mo-ta" name="description" class="form-control" rows="4"></textarea>
    </div>
    <button type="submit" class="btn-submit">Đăng ký</button>
</form>',
            'footer' => 'Còn 10 chỉ tiêu',
        ];

        // Dữ liệu demo cho Form Tư Vấn Miễn Phí
        $formTuVanMienPhiDemo = [
            'title' => 'Tư vấn miễn phí',
            'description' => 'Mô tả tư vấn miễn phí',
            'script' => '<form id="form-tu-van" class="consultation-form">
    <div class="form-group">
        <label for="ho-ten-tv">Họ và Tên <span class="required">*</span></label>
        <input type="text" id="ho-ten-tv" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="so-dien-thoai-tv">Số điện thoại <span class="required">*</span></label>
        <input type="tel" id="so-dien-thoai-tv" name="phone" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="nganh-hoc-tv">Ngành học</label>
        <select id="nganh-hoc-tv" name="major" class="form-control">
            <option value="">-- Chọn ngành học --</option>
            <option value="kinh-te">Kinh tế</option>
            <option value="cntt">Công nghệ thông tin</option>
            <option value="luat">Luật</option>
        </select>
    </div>
    <div class="form-group">
        <label for="mo-ta-tv">Mô tả</label>
        <textarea id="mo-ta-tv" name="description" class="form-control" rows="4"></textarea>
    </div>
    <button type="submit" class="btn-submit">Đăng ký</button>
</form>',
            'footer' => 'Còn 20 chỉ tiêu',
        ];

        // Dữ liệu demo cho Form Học Thử Miễn Phí
        $formHocThuDemo = [
            'title' => 'Học thử miễn phí',
            'description' => 'Mô tả học thử miễn phí',
            'script' => '<form id="form-hoc-thu" class="consultation-form">
    <div class="form-group">
        <label for="ho-ten-ht">Họ và Tên <span class="required">*</span></label>
        <input type="text" id="ho-ten-ht" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="so-dien-thoai-ht">Số điện thoại <span class="required">*</span></label>
        <input type="tel" id="so-dien-thoai-ht" name="phone" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="nganh-hoc-ht">Ngành học</label>
        <select id="nganh-hoc-ht" name="major" class="form-control">
            <option value="">-- Chọn ngành học --</option>
            <option value="kinh-te">Kinh tế</option>
            <option value="cntt">Công nghệ thông tin</option>
            <option value="luat">Luật</option>
        </select>
    </div>
    <div class="form-group">
        <label for="mo-ta-ht">Mô tả</label>
        <textarea id="mo-ta-ht" name="description" class="form-control" rows="4"></textarea>
    </div>
    <button type="submit" class="btn-submit">Đăng ký</button>
</form>',
            'footer' => 'Còn 15 suất học thử miễn phí',
        ];

        // Lấy tất cả các trường
        $schools = School::all();
        
        echo "Updating form data for {$schools->count()} schools...\n";
        
        $updated = 0;
        foreach ($schools as $school) {
            $updateData = [];
            
            // Cập nhật form_tai_lo_trinh_hoc
            $updateData['form_tai_lo_trinh_hoc'] = $formTaiLoTrinhHocDemo;
            
            // Cập nhật form_tu_van_mien_phi
            $updateData['form_tu_van_mien_phi'] = $formTuVanMienPhiDemo;
            
            // Cập nhật form_hoc_thu
            $updateData['form_hoc_thu'] = $formHocThuDemo;
            
            $school->update($updateData);
            $updated++;
            echo "Updated school ID: {$school->id}\n";
        }
        
        echo "Completed! Updated {$updated} schools with full form data (title, description, script, footer).\n";
    }
}
