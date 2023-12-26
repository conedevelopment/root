<turbo-frame id="widget-{{ $key }}" @if(! $isTurbo) src="{{ $url }}" @endif>
    <div {{ $attrs }}></div>
</turbo-frame>
