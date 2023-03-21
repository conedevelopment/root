<?php

namespace Cone\Root\Enums;

enum AlertType: string
{
    case Info = 'info';
    case Success = 'success';
    case Error = 'danger';
    case Warning = 'warning';
}
