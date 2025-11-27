<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th style="width:50px;">
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên chuyên ngành</th>
        @include('backend.dashboard.component.languageTh')
        <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }} </th>
        <th class="text-center" style="width:100px;">Trang chủ</th>
        <th class="text-center" style="width:100px;">{{ __('messages.tableAction') }} </th>
    </tr>
    </thead>
    <tbody>
        @if(isset($majors) && is_object($majors))
            @foreach($majors as $major)
            <tr>
                <td>
                    <input type="checkbox" value="{{ $major->id }}" class="input-checkbox checkBoxItem">
                </td>
               
                <td>
                    {{ $major->name }}
                </td>
                @include('backend.dashboard.component.languageTd', ['model' => $major, 'modeling' => 'Major'])
                <td class="text-center js-switch-{{ $major->id }}"> 
                    <input type="checkbox" value="{{ $major->publish }}" class="js-switch status " data-field="publish" data-model="Major" {{ ($major->publish == 2) ? 'checked' : '' }} data-modelId="{{ $major->id }}" />
                </td>
                <td class="text-center js-switch-home-{{ $major->id }}"> 
                    <input type="checkbox" value="{{ $major->is_home ?? 0 }}" class="js-switch status " data-field="is_home" data-model="Major" {{ (($major->is_home ?? 0) == 2) ? 'checked' : '' }} data-modelId="{{ $major->id }}" />
                </td>
                <td class="text-center"> 
                    <a href="{{ route('major.edit', $major->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                    <a href="{{ route('major.delete', $major->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{  $majors->links('pagination::bootstrap-4') }}

