<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class TimeDateWidget extends Widget
{
    protected static ?int $sort = -20;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.time-date-widget';
}
