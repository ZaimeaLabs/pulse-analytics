<?php declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Livewire\LivewireManager;
use ZaimeaLabs\Pulse\Analytics\Livewire\Actions;
use ZaimeaLabs\Pulse\Analytics\Livewire\Authentications;
use ZaimeaLabs\Pulse\Analytics\Livewire\Visits;

class AppsLoadServiceProvider extends ServiceProvider
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
            $livewire->component('visits', Visits::class);
        });
    }
}
