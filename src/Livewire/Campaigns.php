<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Laravel\Pulse\Livewire\Concerns\HasPeriod;
use Laravel\Pulse\Livewire\Concerns\RemembersQueries;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;

/**
 * @internal
 */
#[Lazy]
class Campaigns extends Card
{
    use HasPeriod, RemembersQueries;

    /**
     * Ordering.
     *
     * @var 'asc'|'desc'
     */
    #[Url(as: 'campaigns')]
    public string $orderBy = 'asc';

    /**
     * Render the component.
     */
    public function render(): Renderable
    {
        $type = $this->orderBy;

        [$campaignsQuery, $time, $runAt] = $this->remember(
            function () {
                $counts = $this->aggregate(
                    'ctm_campaign',
                    'count',
                    limit: 10,
                );

                return $counts->map(function ($row) {
                    [$country, $campaign] = json_decode($row->key, flags: JSON_THROW_ON_ERROR);

                    return (object) [
                        'key' => $row->key,
                        'country' => $country,
                        'campaign' => $campaign,
                        'count' => (int) $row->count,
                    ];
                });
        });

        return View::make('analytics::livewire.campaigns', [
            'time' => $time,
            'runAt' => $runAt,
            'campaignsQuery' => $campaignsQuery,
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
