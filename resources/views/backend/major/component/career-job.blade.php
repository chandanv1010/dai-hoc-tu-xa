<div class="career-job-item mb15" data-job-index="{{ $jobIndex }}">
    <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
        <div class="row mb15">
            <div class="col-lg-3">
                <div class="form-row">
                    <label class="control-label text-left">Icon</label>
                    <input 
                        type="text"
                        name="chance[job][{{ $jobIndex }}][image]"
                        value="{{ old("chance.job.{$jobIndex}.image", (is_array($job) ? ($job['image'] ?? '') : ($job->image ?? ($job->icon ?? '')))) }}"
                        class="form-control upload-image"
                        placeholder="Chọn icon/ảnh"
                        readonly
                    >
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-row">
                    <label class="control-label text-left">Tên nghề</label>
                    <input 
                        type="text"
                        name="chance[job][{{ $jobIndex }}][name]"
                        value="{{ old("chance.job.{$jobIndex}.name", (is_array($job) ? ($job['name'] ?? '') : ($job->name ?? ($job->title ?? '')))) }}"
                        class="form-control"
                        placeholder="Nhập tên nghề"
                    >
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-row">
                    <label class="control-label text-left">Mô tả</label>
                    <input 
                        type="text"
                        name="chance[job][{{ $jobIndex }}][description]"
                        value="{{ old("chance.job.{$jobIndex}.description", (is_array($job) ? ($job['description'] ?? '') : ($job->description ?? ''))) }}"
                        class="form-control"
                        placeholder="Nhập mô tả"
                    >
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-row">
                    <label class="control-label text-left">Lương</label>
                    <input 
                        type="text"
                        name="chance[job][{{ $jobIndex }}][salary]"
                        value="{{ old("chance.job.{$jobIndex}.salary", (is_array($job) ? ($job['salary'] ?? '') : ($job->salary ?? ''))) }}"
                        class="form-control"
                        placeholder="Nhập lương"
                    >
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-row">
                    <label class="control-label text-left">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm removeCareerJobBtn" style="width: 100%;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

