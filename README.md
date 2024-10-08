### WORK IN PROGRESS ###

# Analytics cards for Laravel Pulse
A customizable Laravel Pulse card for analytics metrics.

## Features

This card monitors and displays the following:

- **Actions:**
  - **Eloquent Hook:** 
      - Retrieved: after a record has been retrieved.
      - Creating: before a record has been created.
      - Created: after a record has been created.
      - Updating: before a record is updated.
      - Updated: after a record has been updated.
      - Saving: before a record is saved (either created or updated).
      - Saved: after a record has been saved (either created or updated).
      - Restoring: before a soft-deleted record is going to be restored.
      - Restored: after a soft-deleted record has been restored.
      - Replicating: when a record is replicated.
      - Deleting: before a record is deleted or soft-deleted.
      - Deleted: after a record has been deleted or soft-deleted.
      - ForceDeleting: befor a record is soft-deleted.
      - ForceDeleted: after a record has been soft-deleted.

- **Authentications:** Store when a User is logged in or out.

- **Campaign:**
  - **URL:** Use pulse config to set what to catch for campaign.

- **Visits:** Metrics for page view.


## Installation
> **Note**
> You need to have [Laravel Pulse](https://pulse.laravel.com/) installed first.

Install the package via composer by this command:
```sh
composer require zaimealabs/pulse-analytics
```

Add the recorder to your `config/pulse.php` file
```php
    'recorders' => [
        ZaimeaLabs\Pulse\Analytics\Recorders\Actions::class => [
            'enabled'           => env('PULSE_ACTION_ENABLED', true),
            'on_retrieved'      => false,
            'on_creating'       => false,
            'on_created'        => true,
            'on_updating'       => false,
            'on_updated'        => true,
            'on_saving'         => false,
            'on_saved'          => true,
            'on_restoring'      => true,
            'on_restored'       => true,
            'on_replicate'      => true,
            'on_deleting'       => false,
            'on_deleted'        => true,
            'on_forceDeleting'  => false,
            'on_forceDeleted'   => true,
            'obervers' => [
                \App\Models\User::class,
            ],
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'$#', // Pulse dashboard...
                '#^/login#',
                '#^/logout#',
            ],
        ],

        ZaimeaLabs\Pulse\Analytics\Recorders\Visits::class => [
            'enabled' => env('PULSE_VISIT_ENABLED', true),
            'ajax_requests' => true, // Disable visit in Ajax mode, set it to false.
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'$#', // Pulse dashboard...
                '#^/telescope#', // Telescope dashboard...
                '#^/_ignition#',
                '#^/dev#',
                '#^/livewire#',
                '#^/_debugbar#',
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
            'enabled' => env('PULSE_AUTHENTICATION_ENABLED', true),
            'guard' => 'web', // The correct guard.
            'ignore' => [
                //'login',
                //'logout',
            ],
        ],

        ZaimeaLabs\Pulse\Analytics\Recorders\Campaign::class => [
            'enabled' => env('PULSE_CAMPAIGN_ENABLED', true),
            'catch' => [
                '#^ctm_campaign=advertisement&ctm_source=Zaimea.com$#',
                '#^ctm_campaign=advertisement&ctm_source=Custura.de$#',
            ],
        ],
    ]
```

### Add components to the dashboard
> **Note**
> To add the card to the Pulse dashboard, you must first [publish the vendor view](https://laravel.com/docs/10.x/pulse#dashboard-customization).

```bash
php artisan vendor:publish --tag=pulse-dashboard
```

Then, you can modify the `dashboard.blade.php` file:

```diff
<x-pulse>
+   <livewire:actions cols="6" />

+   <livewire:visits cols="6"/>

+   <livewire:authentications cols="6" />

+   <livewire:campaigns cols="6" />

    <livewire:pulse.servers cols="full" />

    <livewire:pulse.usage cols='4' rows='1' />
```
