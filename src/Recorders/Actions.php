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
     * Create a new recorder instance.
     */
    public function __construct(
        protected Pulse $pulse,
        protected Repository $config,
        protected string $action = '', // The action processing
        protected string $model = '',  // Where was executed the action.
    ) {
        //
    }

    /**
     * Record the request.
     */
    public function record(Carbon $startedAt, Request $request, Response $response): void
    {
        [$timestamp, $action] = [
            $startedAt->getTimestamp(),
            $this->action,
        ];

        $this->pulse->lazy(function () use ($timestamp, $action, $request) {
            if ($this->shouldIgnore($action) || $this->shouldIgnore($this->resolveRoutePath($request)[0])) {
                return;
            }

            if (($userId = $this->pulse->resolveAuthenticatedUserId()) === null) {
                return;
            }

            $this->pulse->record(
                type: 'user_action',
                key: json_encode(
                    [
                        (string) $userId,
                        $request->path(),
                        $this->action,
                        $this->model
                    ], flags: JSON_THROW_ON_ERROR),
                timestamp: $timestamp
            )->count();
        });
    }

    /**
    * Handle the Model "retrieved" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function retrieved(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_retrieved', false)) {
            $this->action = Constants::ACTION_RETRIEVED;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "creating" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function creating(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_creating', false)) {
            $this->action = Constants::ACTION_CREATING;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "created" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function created(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_created', false)) {
            $this->action = Constants::ACTION_CREATED;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "updating" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function updating(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_updating', false)) {
            $this->action = Constants::ACTION_UPDATING;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "updated" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function updated(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_update', false)) {
            $this->action = Constants::ACTION_UPDATE;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "saving" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function saving(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_saving', false)) {
            $this->action = Constants::ACTION_SAVING;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "saved" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function saved(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_saved', false)) {
            $this->action = Constants::ACTION_SAVED;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "restoring" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function restoring(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_restoring', false)) {
            $this->action = Constants::ACTION_RESTORING;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "restored" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function restored(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_restored', false)) {
            $this->action = Constants::ACTION_RESTORED;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "replicating" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function replicating(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_replicating', false)) {
            $this->action = Constants::ACTION_REPLICATING;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "deleting" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function deleting(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_deleting', false)) {
            $this->action = Constants::ACTION_DELETING;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "deleted" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function deleted(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_deleted', false)) {
            $this->action = Constants::ACTION_DELETED;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "forceDeleting" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function forceDeleting(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_forceDeleting', false)) {
            $this->action = Constants::ACTION_FORCEDELETING;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }

    /**
    * Handle the Model "forceDeleted" event.
    *
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function forceDeleted(Model $model): void
    {
        if ($this->config->get('pulse.recorders.'.self::class.'.on_forceDeleted', false)) {
            $this->action = Constants::ACTION_FORCEDELETED;

            $this->model = get_class(object: $model);

            app()->call(self::record(...));
        }
    }
}
