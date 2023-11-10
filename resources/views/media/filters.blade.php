<div class="modal__filter">
    @foreach($filters as $filter)
        @include($filter['template'], $filter)
    @endforeach
</div>
