<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Recorders;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Laravel\Pulse\Facades\Pulse;

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
    public function __construct(protected Pulse $pulse) {
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
            config('pulse.recorders.'.static::class.'.guard'),
        ];

        if (config('pulse.recorders.'.static::class.'.enabled', false)) {
            $this->pulse->record(
                type: match ($class) {
                    Login::class => 'login',
                    Logout::class => 'logout',
                },
                key: json_encode(
                    [
                        (string) auth($guard)->id() ?? crypt(request()->ip(), config('app.cipher')),
                        match ($class) {
                            Login::class => 'login',
                            Logout::class => 'logout',
                        },
                    ], flags: JSON_THROW_ON_ERROR),
                timestamp: $timestamp,
            );
        }
    }
}
