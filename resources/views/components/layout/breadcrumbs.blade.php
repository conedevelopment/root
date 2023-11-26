<ol class="breadcrumb-list app-header__breadcrumb">
    @foreach($breadcrumbs as $item)
        <li>
            @if($loop->last)
                <span aria-current="page">{{ $item['label'] }}</span>
            @else
                <a href="{{ $item['uri'] }}">{{ $item['label'] }}</a>
            @endif
        </li>
    @endforeach
</ol>
