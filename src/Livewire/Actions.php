<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Laravel\Pulse\Livewire\Concerns\HasPeriod;
use Laravel\Pulse\Livewire\Concerns\RemembersQueries;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use ZaimeaLabs\Pulse\Analytics\Recorders\Actions as ActionsRecorder;

/**
 * @internal
 */
#[Lazy]
class Actions extends Card
{
    use HasPeriod, RemembersQueries;

    /**
     * Ordering.
     *
     * @var 'asc'|'desc'
     */
    #[Url(as: 'actions')]
    public string $orderBy = 'asc';

    /**
     * Render the component.
     */
    public function render(): Renderable
    {
        $type = $this->orderBy;

        [$actionsQuery, $time, $runAt] = $this->remember(
            function () {
                $counts = $this->aggregate(
                    'user_action',
                    'count',
                    limit: 10,
                );
                $keys = collect($counts->pluck('key'))->map(function ($userId) {
                    return Arr::only(json_decode($userId), '0');
                });

                $users = Pulse::resolveUsers(collect($keys->flatten()));

                return $counts->map(function ($row) use ($users) {
                    [$id, $url, $action, $model] = json_decode($row->key, flags: JSON_THROW_ON_ERROR);

                    return (object) [
                        'key' => $row->key,
                        'url' => $url,
                        'action' => $action,
                        'model' => $model,
                        'user' => $users->find($id),
                        'count' => (int) $row->count,
                    ];
                });
            },
            'keys'
        );

        return View::make('analytics::livewire.actions', [
            'time' => $time,
            'runAt' => $runAt,
            'config' => Config::get('pulse.recorders.'.ActionsRecorder::class),
            'actionsQuery' => $actionsQuery,
        ]);
    }

    /**
     * Define any CSS that should be loaded for the component.
     *
     * @return string|\Illuminate\Contracts\Support\Htmlable|array<int, string|\Illuminate\Contracts\Support\Htmlable>|null
     */
    protected function css()
    {
        return __DIR__.'/../../dist/analytics.css';
    }
}
