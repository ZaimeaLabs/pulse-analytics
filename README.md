Add the recorder to your `config/pulse.php` file
```php
    'recorders' => [
        ZaimeaLabs\Pulse\Analytics\Recorders\Actions::class => [
            'enabled'        => env('PULSE_ACTION_ENABLED', true), // Store when user do action.
            'user_model' => \App\Models\User::class, // User model.
            'on_user_delete' => true, // Authentications rows when the user is deleted.
            'on_store'       => true,
            'on_update'      => true,
            'on_destroy'     => true,
            'on_read'        => true,
            'on_replicate'   => false,
            'obervers' => [
                \App\Models\Article::class,
                \App\Models\Client::class,
                \App\Models\ClientLog::class,
                \App\Models\Like::class,
                \App\Models\Tag::class,
                \App\Models\User::class,
            ],
        ],

        ZaimeaLabs\Pulse\Analytics\Recorders\Visits::class => [
            'enabled' => env('PULSE_VISIT_ENABLED', true),
            'ajax_requests' => true, // Disable visit in Ajax mode, set it to false.
            'except_pages' => [
                'pulse',
                'login',
                'logout',
                'livewire/update',
                '_debugbar',
            ],

            /*
            * If you want to delete visit rows after some days, you can change this to 360 for example,
            * but you don't like to delete rows you can change it to 0.
            *
            * For this feature you need Task-Scheduling => https://laravel.com/docs/11.x/scheduling
            */
            'delete_days' => 0,
        ],

        ZaimeaLabs\Pulse\Analytics\Recorders\Authentications::class => [
            'enabled' => env('PULSE_AUTHENTICATION_ENABLED', true), // Store when user login or logout.
            'on_user_delete' => true, // Authentications rows when the user is deleted.
            'user_model' => \App\Models\User::class, // User model.
            'guard' => 'web', // The correct guard.
        ],
    ]
```
