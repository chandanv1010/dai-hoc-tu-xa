<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th style="width:50px;">
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên trường</th>
        @include('backend.dashboard.component.languageTh')
        <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }} </th>
        <th class="text-center" style="width:100px;">{{ __('messages.tableAction') }} </th>
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
                    {{ $school->name }}
                </td>
                @include('backend.dashboard.component.languageTd', ['model' => $school, 'modeling' => 'School'])
                <td class="text-center js-switch-{{ $school->id }}"> 
                    <input type="checkbox" value="{{ $school->publish }}" class="js-switch status " data-field="publish" data-model="School" {{ ($school->publish == 2) ? 'checked' : '' }} data-modelId="{{ $school->id }}" />
                </td>
                <td class="text-center"> 
                    <a href="{{ route('school.edit', $school->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                    <a href="{{ route('school.delete', $school->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{  $schools->links('pagination::bootstrap-4') }}

