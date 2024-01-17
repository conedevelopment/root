@if(! empty($values))
    <table style="width: 100%;">
        <thead>
            <tr>
                @foreach($values[0] as $field)
                    <th>{!! $field['label'] !!}</th>
                @endforeach
            </tr>
        </thead>
        <thead>
            @foreach($values as $value)
                <tr>
                    @foreach($value as $field)
                        <td>{!! $field['formattedValue'] !!}</td>
                    @endforeach
                </tr>
            @endforeach
        </thead>
    </table>
@endif
