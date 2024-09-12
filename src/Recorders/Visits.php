<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Recorders;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Laravel\Pulse\Concerns\ConfiguresAfterResolving;
use Laravel\Pulse\Facades\Pulse;
use Symfony\Component\HttpFoundation\Response;
use ZaimeaLabs\Pulse\Analytics\Recorders\Concerns\Agent;

/**
 * @internal
 */
class Visits
{
    use ConfiguresAfterResolving;
    /**
     * Create a new recorder instance.
     */
    public function __construct(protected Pulse $pulse) {
        //
    }

    /**
     * Register the recorder.
     */
    public function register(callable $record, Application $app, Request $request): void
    {
        if (config('pulse.recorders.'.static::class.'.enabled', false) === false) {
            return;
        }
        if (config('pulse.recorders.'.static::class.'.ajax_requests', false) === false && $request->ajax()) {
            return;
        }

        $this->afterResolving($app, Kernel::class, fn (Kernel $kernel) => $kernel->whenRequestLifecycleIsLongerThan(-1, $record));
    }

    /**
     * Record the request.
     */
    public function record(Carbon $startedAt, Request $request, Response $response): void
    {
        $exceptPages = config('pulse.recorders.'.static::class.'.except_pages', []);

        if (empty($exceptPages) || !$this->checkIsExceptPages($request->path(), $exceptPages)) {

            $agent = new Agent();

            $visitorId = $this->pulse->resolveAuthenticatedUserId() ?? null;

            if ($visitorId === null) {
                $visitorId = crypt($request->ip(), config('app.cipher'));
            }

            $this->pulse->record(
                type: 'page_view',
                key: json_encode(
                    [
                        (string) $visitorId,
                        $request->url(),
                        $agent->getBrowser(),
                        $agent->getDevice(),
                        $agent->getCountryByIp($request->ip()),
                    ], flags: JSON_THROW_ON_ERROR),
                timestamp: $startedAt->getTimestamp()
            );
        }
    }

    /**
     * Check request page are exists in expect pages.
     */
    private function checkIsExceptPages(string $page, array $exceptPages): bool
    {
        return collect($exceptPages)->contains($page);
    }
}
