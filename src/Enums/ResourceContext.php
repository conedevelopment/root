<?php

namespace Cone\Root\Enums;

enum ResourceContext: string
{
    case Create = 'create';
    case Index = 'index';
    case Show = 'show';
    case Update = 'update';
}
