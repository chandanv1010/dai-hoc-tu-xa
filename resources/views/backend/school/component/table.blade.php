<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th style="width:50px;">
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên trường</th>
        @include('backend.dashboard.component.languageTh')
        <th style="width:120px;">Hệ Tốt Nghiệp</th>
        <th style="width:120px;">Địa Điểm Thi</th>
        <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }} </th>
        <th class="text-center" style="width:180px;">{{ __('messages.tableAction') }} </th>
    </tr>
    </thead>
    <tbody>
        @if(isset($schools) && is_object($schools))
            @foreach($schools as $school)
            <tr>
                <td>
                    <input type="checkbox" value="{{ $school->id }}" class="input-checkbox checkBoxItem">
                </td>
               
                <td>
                    @php
                        $schoolName = $school->name ?? '';
                        $schoolCanonical = $school->canonical ?? '';
                        $schoolUrl = $schoolCanonical ? write_url($schoolCanonical, true, true) : '#';
                    @endphp
                    @if($schoolCanonical)
                        <a href="{{ $schoolUrl }}" target="_blank" title="Xem trang trường: {{ $schoolName }}">
                            {{ $schoolName }}
                        </a>
                    @else
                        {{ $schoolName }}
                    @endif
                </td>
                @include('backend.dashboard.component.languageTd', ['model' => $school, 'modeling' => 'School'])
                <td>
                    {{ $school->graduation_system ?? '-' }}
                </td>
                <td>
                    {{ $school->exam_location ?? '-' }}
                </td>
                <td class="text-center js-switch-{{ $school->id }}"> 
                    <input type="checkbox" value="{{ $school->publish }}" class="js-switch status " data-field="publish" data-model="School" {{ ($school->publish == 2) ? 'checked' : '' }} data-modelId="{{ $school->id }}" />
                </td>
                <td class="text-center" style="white-space: nowrap;"> 
                    <a href="{{ route('school.edit', $school->id) }}" class="btn btn-success btn-sm" style="margin: 0 2px;"><i class="fa fa-edit"></i></a>
                    <a href="{{ route('school.duplicate', $school->id) }}" class="btn btn-info btn-sm" title="Nhân bản" style="margin: 0 2px;"><i class="fa fa-copy"></i></a>
                    <a href="{{ route('school.delete', $school->id) }}" class="btn btn-danger btn-sm" style="margin: 0 2px;"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{  $schools->links('pagination::bootstrap-4') }}

