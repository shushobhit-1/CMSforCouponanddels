<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MigrateSafe extends Command
{
    protected $signature = 'app:migrate-safe {--seed} {--fresh}';
    protected $description = 'Run migrations safely with proper FK handling across drivers';

    public function handle()
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
        }

        try {
            if ($this->option('fresh')) {
                $this->call('migrate:fresh');
            } else {
                $this->call('migrate');
            }

            if ($this->option('seed')) {
                $this->call('db:seed');
            }
        } finally {
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } elseif ($driver === 'sqlite')) {
                DB::statement('PRAGMA foreign_keys=ON');
            }
        }

        $this->info('Migrations completed safely.');
    }
}

