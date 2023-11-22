<div class="modal__filter" x-data="query">
    @foreach($filters as $filter)
        @include($filter['template'], $filter)
    @endforeach
</div>
