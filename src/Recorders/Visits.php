<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Recorders;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Pulse\Concerns\ConfiguresAfterResolving;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns;
use Symfony\Component\HttpFoundation\Response;
use ZaimeaLabs\Pulse\Analytics\Recorders\Concerns\Agent;

/**
 * @internal
 */
class Visits
{
    use Concerns\Ignores,
        Concerns\LivewireRoutes,
        ConfiguresAfterResolving;

    /**
     * Create a new recorder instance.
     */
    public function __construct(
        protected Pulse $pulse,
        protected Repository $config,
    ) {
        //
    }

    /**
     * Register the recorder.
     */
    public function register(callable $record, Application $app): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.enabled', false) === false) {
            return;
        }

        if ($this->config->get('pulse.recorders.'.self::class.'.ajax_requests', false) === false && request()->ajax()) {
            return;
        }

        $this->afterResolving(
            $app,
            Kernel::class,
            fn (Kernel $kernel) => $kernel->whenRequestLifecycleIsLongerThan(-1, $record) // @phpstan-ignore method.notFound
        );
    }

    /**
     * Record the request.
     */
    public function record(Carbon $startedAt, Request $request, Response $response): void
    {
        if ($this->shouldIgnore($this->resolveRoutePath($request)[0])) {
            return;
        }

        $agent = new Agent();
        $visitorId = $this->pulse->resolveAuthenticatedUserId() ?? crypt($request->ip(), $this->config->get('app.cipher'));

        $this->pulse->record(
            type: 'page_view',
            key: json_encode(
                [
                    (string) $visitorId,
                    $request->path(),
                    $agent->getBrowser(),
                    $agent->getDevice(),
                    $agent->getCountryByIp($request->ip()),
                ], flags: JSON_THROW_ON_ERROR),
            timestamp: $startedAt->getTimestamp()
        )->count();
    }
}
