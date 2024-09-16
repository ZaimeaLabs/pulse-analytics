<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Recorders\Concerns;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Laravel\Pulse\Concerns\ConfiguresAfterResolving;
use Laravel\Pulse\Facades\Pulse;

/**
 * @internal
 */
trait BootAfterResolving
{
    use ConfiguresAfterResolving;

    /**
     * Boot the recorder.
     */
    public function boot(callable $record, Application $app, Request $request): void
    {
        $this->afterResolving(
            $app,
            Kernel::class,
            fn (Kernel $kernel) => $kernel->whenRequestLifecycleIsLongerThan(-1, $record) // @phpstan-ignore method.notFound
        );
    }

    public function call(): void
    {
        app()->call($this->boot(...), [
            'record' => fn (...$args) => Pulse::rescue(fn () => $this->record(...$args)),
        ]);
    }

}
