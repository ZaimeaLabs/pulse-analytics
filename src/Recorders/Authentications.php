<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Recorders;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Config\Repository;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Ignores;

/**
 * @internal
 */
class Authentications
{
    use Ignores;

    /**
     * The events to listen for.
     *
     * @var list<class-string>
     */
    public array $listen = [
        Login::class,
        Logout::class,
    ];

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
     * Record the request.
     */
    public function record(Login|Logout $event): void
    {
        [$timestamp, $class, $guard] = [
            CarbonImmutable::now()->getTimestamp(),
            $event::class,
            $this->config->get('pulse.recorders.'.self::class.'.guard'),
        ];

        if ($this->config->get('pulse.recorders.'.self::class.'.enabled', false))
        {
            if ($this->shouldIgnore(match ($class) { Login::class => 'login', Logout::class => 'logout',})){
                return;
            }

            $visitorId = auth($guard)->id() ?? crypt(request()->ip(), $this->config->get('app.cipher'));

            $this->pulse->record(
                type: match ($class) {
                    Login::class => 'login',
                    Logout::class => 'logout',
                },
                key: json_encode(
                    [
                        (string) $visitorId,
                        match ($class) {
                            Login::class => 'login',
                            Logout::class => 'logout',
                        },
                    ], flags: JSON_THROW_ON_ERROR),
                timestamp: $timestamp,
            )->count();
        }
    }
}
