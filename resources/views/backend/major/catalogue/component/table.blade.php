<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th style="width:50px;">
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>{{ __('messages.tableName') }}</th>
        @include('backend.dashboard.component.languageTh')
        <th class="text-right">Sắp xếp</th>
        <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }} </th>
        <th class="text-center" style="width:100px;">{{ __('messages.tableAction') }} </th>
    </tr>
    </thead>
    <tbody>
        @if(isset($majorCatalogues) && is_object($majorCatalogues))
            @foreach($majorCatalogues as $majorCatalogue)
            <tr >
                <td>
                    <input type="checkbox" value="{{ $majorCatalogue->id }}" class="input-checkbox checkBoxItem">
                </td>
               
                <td>
                    {{ $majorCatalogue->name }}
                </td>
                @include('backend.dashboard.component.languageTd', ['model' => $majorCatalogue, 'modeling' => 'MajorCatalogue'])
                <td class="sort">
                    <input type="text" name="order" value="{{ $majorCatalogue->order }}" class="form-control sort-order text-right" data-id="{{ $majorCatalogue->id }}" data-model="{{ $config['model'] }}">
                </td>
                <td class="text-center js-switch-{{ $majorCatalogue->id }}"> 
                    <input type="checkbox" value="{{ $majorCatalogue->publish }}" class="js-switch status " data-field="publish" data-model="{{ $config['model'] }}" {{ ($majorCatalogue->publish == 2) ? 'checked' : '' }} data-modelId="{{ $majorCatalogue->id }}" />
                </td>
                <td class="text-center"> 
                    <a href="{{ route('major.catalogue.edit', $majorCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                    <a href="{{ route('major.catalogue.delete', $majorCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{  $majorCatalogues->links('pagination::bootstrap-4') }}

