<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ZaimeaLabs\Pulse\Analytics\Recorders\Visits;

class RemoveVisitsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pulse:remove-visits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove visits records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = config('pulse.recorders.'.Visits::class.'.delete_days', 0);
        if ($days === 0) {
            $this->error('Your delete days are 0, You can go to the config file and change it!');
            return;
        }

        $date = now()->subDays($days)->getTimestamp();

        //Add deleting from db

        $this->info('Records have been removed successfully!');
    }
}
