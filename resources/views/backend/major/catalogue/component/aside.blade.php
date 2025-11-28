@include('backend.dashboard.component.publish', ['model' => ($majorCatalogue) ?? null, 'hideImage' => false])

<div class="ibox w">
    <div class="ibox-title">
        <h5>Sắp xếp</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <input type="number" name="order" class="form-control" value="{{ old('order', (isset($majorCatalogue->order)) ? $majorCatalogue->order : 0) }}" placeholder="0">
                </div>
            </div>
        </div>
    </div>
</div>

