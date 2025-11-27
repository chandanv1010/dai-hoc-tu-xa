<div class="student-feedback-item mb15" data-feedback-index="{{ $feedbackIndex }}">
    <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
        <div class="row mb15">
            <div class="col-lg-3">
                <div class="form-row">
                    <label class="control-label text-left">Ảnh đại diện</label>
                    <input 
                        type="text"
                        name="feedback[items][{{ $feedbackIndex }}][image]"
                        value="{{ old("feedback.items.{$feedbackIndex}.image", (is_array($feedback) ? ($feedback['image'] ?? '') : ($feedback->image ?? ($feedback->avatar ?? '')))) }}"
                        class="form-control upload-image"
                        placeholder="Chọn ảnh"
                        readonly
                    >
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-row">
                    <label class="control-label text-left">Tên</label>
                    <input 
                        type="text"
                        name="feedback[items][{{ $feedbackIndex }}][name]"
                        value="{{ old("feedback.items.{$feedbackIndex}.name", (is_array($feedback) ? ($feedback['name'] ?? '') : ($feedback->name ?? ''))) }}"
                        class="form-control"
                        placeholder="Nhập tên"
                    >
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-row">
                    <label class="control-label text-left">Chức vụ</label>
                    <input 
                        type="text"
                        name="feedback[items][{{ $feedbackIndex }}][position]"
                        value="{{ old("feedback.items.{$feedbackIndex}.position", (is_array($feedback) ? ($feedback['position'] ?? '') : ($feedback->position ?? ''))) }}"
                        class="form-control"
                        placeholder="Nhập chức vụ"
                    >
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-row">
                    <label class="control-label text-left">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm removeStudentFeedbackBtn" style="width: 100%;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Mô tả</label>
                    <textarea 
                        name="feedback[items][{{ $feedbackIndex }}][description]"
                        class="form-control"
                        rows="3"
                        placeholder="Nhập mô tả"
                    >{{ old("feedback.items.{$feedbackIndex}.description", (is_array($feedback) ? ($feedback['description'] ?? '') : ($feedback->description ?? ''))) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

