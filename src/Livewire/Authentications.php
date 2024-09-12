<?php

namespace ZaimeaLabs\Pulse\Analytics\Livewire;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use ZaimeaLabs\Blow\Facades\Blow;
use ZaimeaLabs\Blow\Models\Blow as BlowModel;

/**
 * @internal
 */
#[Lazy]
class Authentications extends Base
{
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
        $orderBy = $this->orderBy;

        [$authenticationsQuery, $time, $runAt] = $this->remember(
            function () use ($orderBy) {
                $counts = BlowModel::query()
                ->whereIn('type', ['login', 'logout'])
                ->orderBy('timestamp', $orderBy)
                ->get();

                $users = Blow::resolveUsers($counts->pluck('key'));

                return $counts->map(function ($row) use ($users){
                    [$type] = json_decode($row->value, flags: JSON_THROW_ON_ERROR);

                    return (object) [
                        'id' => $row->id,
                        'timestamp' => $row->timestamp,
                        'type' => $type,
                        'user' => $users->find($row->key),
                    ];
                });
            }
        );

        return View::make('blow::livewire.authentications', [
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
        return __DIR__.'/../../dist/validation.css';
    }
}
