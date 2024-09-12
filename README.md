Add the recorder to your `config/pulse.php` file
```php
'recorders' => [
    \ZaimeaLabs\Blow\Recorders\Actions::class => [
        'enabled'        => env('BLOW_ACTION_ENABLED', true), // Store when user do action.
        'on_store'       => true,
        'on_update'      => true,
        'on_destroy'     => true,
        'on_read'        => true,
        'on_replicate'   => false,
        'obervers' => [
            \App\Models\User::class,
        ],
    ],
    \ZaimeaLabs\Blow\Recorders\Visits::class => [
        'enabled' => env('BLOW_VISIT_ENABLED', true),
        'ajax_requests' => true, // Disable visit in Ajax mode, set it to false.
        'except_pages' => [
            'blow',
            'login',
            'logout',
            'livewire/update',
        ],

        /*
        * If you want to delete visit rows after some days, you can change this to 360 for example,
        * but you don't like to delete rows you can change it to 0.
        *
        * For this feature you need Task-Scheduling => https://laravel.com/docs/11.x/scheduling
        */
        'delete_days' => 0,
    ],
    \ZaimeaLabs\Blow\Recorders\Authentications::class => [
        'enabled' => env('BLOW_AUTHENTICATION_ENABLED', true), // Store when user login or logout.
        'guard' => 'web', // The correct guard.
    ],
]
```
