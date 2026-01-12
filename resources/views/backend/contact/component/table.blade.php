<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th>Họ Tên</th>
            <th>Ngày tạo</th>
            <th>Số điện thoại</th>
            <th>Email</th>
            <th>Địa chỉ</th>
            <th>Lời nhắn</th>
            <th>Nguồn UTM</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        {{-- @dd($contacts) --}}
        @if(isset($contacts) && is_object($contacts))
            @foreach($contacts as $contact)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $contact->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $contact->name }}
                    </td>
                    <td>
                        {{ convertDateTime($contact->created_at,'d/m/Y') }}
                    </td>
                    <td>
                        {{ $contact->phone }}
                    </td>
                    <td>
                        {{ $contact->email ?? '-' }}
                    </td>
                    <td>
                        {{ $contact->address }}
                    </td>
                    <td>
                        @if(!empty($contact->message))
                            {!! $contact->message !!}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $utmParts = [];
                            if (!empty($contact->utm_source)) {
                                $utmParts[] = 'Source: ' . $contact->utm_source;
                            }
                            if (!empty($contact->utm_medium)) {
                                $utmParts[] = 'Medium: ' . $contact->utm_medium;
                            }
                            if (!empty($contact->utm_campaign)) {
                                $utmParts[] = 'Campaign: ' . $contact->utm_campaign;
                            }
                            $utmDisplay = !empty($utmParts) ? implode('<br>', $utmParts) : '-';
                        @endphp
                        @if($utmDisplay !== '-')
                            <small>{!! $utmDisplay !!}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    
                    <td class="text-center"> 
                        <a href="{{ route('contact.delete', $contact->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{  $contacts->links('pagination::bootstrap-4') }}
