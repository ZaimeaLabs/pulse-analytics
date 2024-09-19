<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Laravel\Pulse\Livewire\Concerns\HasPeriod;
use Laravel\Pulse\Livewire\Concerns\RemembersQueries;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;

/**
 * @internal
 */
#[Lazy]
class Authentications extends Card
{
    use HasPeriod, RemembersQueries;

    /**
     * Ordering.
     *
     * @var 'asc'|'desc'
     */
    #[Url(as: 'authentications')]
    public string $orderBy = 'asc';

    /**
     * Render the component.
     */
    public function render(): Renderable
    {
        $type = $this->orderBy;

        [$authenticationsQuery, $time, $runAt] = $this->remember(
            function () {
                $counts = $this->aggregateTypes(
                    ['login', 'logout'],
                    'count',
                );

                $users = Pulse::resolveUsers($counts->pluck('key'));

                return $counts->map(function ($row) use ($users) {
                    return (object) [
                        'type' => $row->login ? 'login' : 'logout',
                        'key' => $row->key,
                        'user' => $users->find($row->key),
                    ];
                });
        });

        return View::make('analytics::livewire.authentications', [
            'time' => $time,
            'runAt' => $runAt,
            'authenticationsQuery' => $authenticationsQuery,
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
