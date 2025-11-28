@include('backend.dashboard.component.publish', ['model' => ($major) ?? null, 'hideImage' => false])

<div class="ibox w">
    <div class="ibox-title">
        <h5>Danh mục Ngành học</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <select name="major_catalogue_id" class="form-control">
                        <option value="">-- Chọn danh mục --</option>
                        @if(isset($majorCatalogues) && count($majorCatalogues) > 0)
                            @foreach($majorCatalogues as $catalogue)
                                <option 
                                    value="{{ $catalogue->id }}"
                                    {{ old('major_catalogue_id', (isset($major) && isset($major->major_catalogue_id)) ? $major->major_catalogue_id : '') == $catalogue->id ? 'selected' : '' }}
                                >
                                    {{ $catalogue->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
