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
use Symfony\Component\HttpFoundation\Response;
use ZaimeaLabs\Pulse\Analytics\Recorders\Concerns\Agent;

/**
 * @internal
 */
class Campaign
{
    use Concerns\Catches,
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
        $this->pulse->lazy(function () use ($startedAt, $request) {
            if ($this->shouldCatch($request->getQueryString())) {
                return;
            }

            $agent = new Agent();

            $this->pulse->record(
                type: 'ctm_campaign',
                key: json_encode(
                    [
                        $agent->getCountryByIp($request->ip()),
                        $request->getQueryString(),
                    ], flags: JSON_THROW_ON_ERROR),
                timestamp: $startedAt->getTimestamp()
            )->count();
        });
    }
}
