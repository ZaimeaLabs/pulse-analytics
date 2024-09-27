<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Livewire\LivewireManager;
use ZaimeaLabs\Pulse\Analytics\Livewire\Actions;
use ZaimeaLabs\Pulse\Analytics\Livewire\Authentications;
use ZaimeaLabs\Pulse\Analytics\Livewire\Campaigns;
use ZaimeaLabs\Pulse\Analytics\Livewire\Visits;
use ZaimeaLabs\Pulse\Analytics\Recorders\Actions as RecordersActions;

class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'analytics');

        $this->callAfterResolving('livewire', function (LivewireManager $livewire, Application $app) {
            $livewire->component('actions', Actions::class);
            $livewire->component('authentications', Authentications::class);
            $livewire->component('campaigns', Campaigns::class);
            $livewire->component('visits', Visits::class);
        });

        if (config('pulse.recorders.'.RecordersActions::class.'.enabled'))
        {
            $this->bootActionsObservers();
        }
    }

    /**
     * Register the obervers for Actions.
     */
    public function bootActionsObservers()
    {
        $obervers = config('pulse.recorders.'.RecordersActions::class.'.obervers');

        foreach ($obervers as $observer) {
            $observer::observe(RecordersActions::class);
        }
    }
}
