<?php

namespace Cone\Root\Relations;

use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesFilters;

abstract class Relation
{
    use ResolvesActions;
    use ResolvesFields;
    use ResolvesFilters;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }
}
