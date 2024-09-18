<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Recorders;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns;
use Symfony\Component\HttpFoundation\Response;
use ZaimeaLabs\Pulse\Analytics\Constants;

/**
 * @internal
 */
class Actions
{
    use Concerns\Ignores,
        Concerns\LivewireRoutes;

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
    public function __construct(
        protected Pulse $pulse,
        protected Repository $config,
    ) {
        //
    }

    /**
     * Record the request.
     */
    public function record(Carbon $startedAt, Request $request, Response $response): void
    {
        if ($this->shouldIgnore($this->action) || $this->shouldIgnore($this->resolveRoutePath($request)[0])) {
            return;
        }

        if (($userId = $this->pulse->resolveAuthenticatedUserId()) === null) {
            return;
        }

        $this->pulse->record(
            type: 'user_action',
            key: json_encode(
                [(string) $userId, $request->path(), $this->action, $this->model], flags: JSON_THROW_ON_ERROR),
            timestamp: $startedAt->getTimestamp()
        )->count();
    }

    public function created(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_store', false)) {
            $this->action = Constants::ACTION_STORE;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    public function updated(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_update', false)) {
            $this->action = Constants::ACTION_UPDATE;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    public function deleted(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_destroy', false)) {
            $this->action = Constants::ACTION_DELETE;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    public function retrived(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_red', false)) {
            $this->action = Constants::ACTION_READ;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    public function replicating(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_replicate', false)) {
            $this->action = Constants::ACTION_REPLICATE;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }
}
