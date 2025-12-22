<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th style="width:50px;">
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên chuyên ngành</th>
        @include('backend.dashboard.component.languageTh')
        <th style="width:150px;">Đối Tượng Tuyển Sinh</th>
        <th style="width:150px;">Địa Điểm Thi</th>
        <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }} </th>
        <th class="text-center" style="width:150px;">{{ __('messages.tableAction') }} </th>
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
                    <a href="{{ route('router.index', ['canonical' => $major->canonical]) }}" target="_blank">
                        {{ $major->name }}
                    </a>
                </td>
                @include('backend.dashboard.component.languageTd', ['model' => $major, 'modeling' => 'Major'])
                <td>
                    {{ $major->admission_subject ?? '-' }}
                </td>
                <td>
                    {{ $major->exam_location ?? '-' }}
                </td>
                <td class="text-center js-switch-{{ $major->id }}"> 
                    <input type="checkbox" value="{{ $major->publish }}" class="js-switch status " data-field="publish" data-model="Major" {{ ($major->publish == 2) ? 'checked' : '' }} data-modelId="{{ $major->id }}" />
                </td>
                <td class="text-center"> 
                    <a href="{{ route('major.edit', $major->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                    <a href="{{ route('major.duplicate', $major->id) }}" class="btn btn-info" title="Nhân bản"><i class="fa fa-copy"></i></a>
                    <a href="{{ route('major.delete', $major->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{  $majors->links('pagination::bootstrap-4') }}

