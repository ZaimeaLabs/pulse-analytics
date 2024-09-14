<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Recorders;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Laravel\Pulse\Pulse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
class Authentications
{
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
    public function record(Login|Logout $event, Request $request, Response $response): void
    {
        [$timestamp, $class, $guard] = [
            CarbonImmutable::now()->getTimestamp(),
            $event::class,
            $this->config->get('pulse.recorders.'.self::class.'.guard'),
        ];

        if ($this->config->get('pulse.recorders.'.self::class.'.enabled', false)) {
            $this->pulse->record(
                type: match ($class) {
                    Login::class => 'login',
                    Logout::class => 'logout',
                },
                key: json_encode(
                    [
                        (string) auth($guard)->id() ?? crypt($request->ip(), $this->config->get('app.cipher')),
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
