<?php

namespace ZaimeaLabs\Pulse\Analytics\Livewire;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use ZaimeaLabs\Blow\Facades\Blow;
use ZaimeaLabs\Blow\Models\Blow as BlowModel;
/**
 * @internal
 */
#[Lazy]
class Visits extends Card
{
    /**
     * Ordering.
     *
     * @var 'asc'|'desc'
     */
    #[Url(as: 'visits')]
    public string $orderBy = 'asc';

    /**
     * Render the component.
     */
    public function render(): Renderable
    {
        $orderBy = $this->orderBy;
        //$test = $this->aggregate('page_view', ['sum', 'count']);
        [$visits, $time, $runAt] = $this->remember(
            function () use ($orderBy) {
                $counts = BlowModel::query()
                ->where('type', 'page_view')
                ->orderBy('timestamp', $orderBy)
                ->get();

                $users = Blow::resolveUsers($counts->pluck('key'));

                return $counts->map(function ($row) use ($users){
                    [$url, $browser, $platform, $visitorid, $country] = json_decode($row->value, flags: JSON_THROW_ON_ERROR);

                    return (object) [
                        'id' => $row->id,
                        'timestamp' => $row->timestamp,
                        'url' => $url,
                        'browser' => $browser,
                        'platform' => $platform,
                        'visitorid' => $visitorid,
                        'country' => $country,
                        'user' => $users->find($row->key),
                    ];
                });
            }
        );

        return View::make('blow::livewire.visits', [
            'time' => $time,
            'runAt' => $runAt,
            'visitsQuery' => $visits,
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
