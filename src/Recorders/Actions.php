<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Recorders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Laravel\Pulse\Facades\Pulse;
use Symfony\Component\HttpFoundation\Response;
use ZaimeaLabs\Pulse\Analytics\Constants;
use ZaimeaLabs\Pulse\Analytics\Recorders\Concerns\BootAfterResolving;

/**
 * @internal
 */
class Actions
{
    use BootAfterResolving;

    /**
     * The action processing.
     */
    protected string $action = '';

    /**
     * Where was executed the action.
     */
    protected string $model = '';

    /**
     * Create a new recorder instance.
     */
    public function __construct(protected Pulse $pulse) {
        //
    }

    /**
     * Record the request.
     */
    public function record(Carbon $startedAt, Request $request, Response $response): void
    {
        if (($userId = $this->pulse->resolveAuthenticatedUserId()) === null) {
            return;
        }

        $this->pulse->record(
            type: 'user_action',
            key: json_encode(
                [
                    (string) $userId,
                    $request->url(),
                    $this->action,
                    $this->model,
                    crypt($request->ip(), config('app.cipher')),
                ], flags: JSON_THROW_ON_ERROR),
            timestamp: $startedAt->getTimestamp()
        );
    }

    public function created(Model $model)
    {
        if (config('pulse.recorders.'.static::class.'.on_store', false)) {
            $this->action = Constants::ACTION_STORE;

            $this->model = get_class($model);

            self::call();
        }
    }

    public function updated(Model $model)
    {
        if (config('pulse.recorders.'.static::class.'.on_update', false)) {
            $this->action = Constants::ACTION_UPDATE;

            $this->model = get_class($model);

            self::call();
        }
    }

    public function deleted(Model $model)
    {
        if (config('pulse.recorders.'.static::class.'.on_destroy', false)) {
            $this->action = Constants::ACTION_DELETE;

            $this->model = get_class($model);

            self::call();
        }
    }

    public function retrived(Model $model)
    {
        if (config('pulse.recorders.'.static::class.'.on_red', false)) {
            $this->action = Constants::ACTION_READ;

            $this->model = get_class($model);

            self::call();
        }
    }

    public function replicating(Model $model)
    {
        if (config('pulse.recorders.'.static::class.'.on_replicate', false)) {
            $this->action = Constants::ACTION_REPLICATE;

            $this->model = get_class($model);

            self::call();
        }
    }
}
