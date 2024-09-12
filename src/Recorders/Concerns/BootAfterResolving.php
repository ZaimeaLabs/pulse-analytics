<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Concerns;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Laravel\Pulse\Facades\Pulse;

/**
 * @internal
 */
trait BootAfterResolving
{
    /**
     * Configure the class after resolving.
     */
    public function afterResolving(Application $app, string $class, Closure $callback): void
    {
        $app->afterResolving($class, $callback);

        if ($app->resolved($class)) {
            $callback($app->make($class));
        }
    }

    /**
     * Boot the recorder.
     */
    public function boot(callable $record, Application $app, Request $request): void
    {
        $this->afterResolving($app, Kernel::class, fn (Kernel $kernel) => $kernel->whenRequestLifecycleIsLongerThan(-1, $record));
    }

    public function call()
    {
        app()->call($this->boot(...), [
            'record' => fn (...$args) => Pulse::rescue(fn () => $this->record(...$args)),
        ]);
    }
}
