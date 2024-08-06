<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminChart extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->description('all the users')
                ->color('primary')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Employees', Employee::count())
                ->description('all the employees')
                ->color('secondary')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
  
            Stat::make('Unique views', '192.1k')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
