@include('backend.dashboard.component.breadcrumb', ['title' => 'Xóa Danh mục Ngành học'])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('major.catalogue.destroy', $majorCatalogue->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => ($majorCatalogue) ?? null])
</form>

