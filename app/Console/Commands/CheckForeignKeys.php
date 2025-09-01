<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckForeignKeys extends Command
{
    protected $signature = 'app:check-fk';
    protected $description = 'Check for orphaned foreign keys in key tables';

    public function handle()
    {
        $issues = [];

        // Define checks: [table, fk_column, parent_table, parent_pk]
        $checks = [
            ['coupons', 'store_id', 'stores', 'id'],
            ['coupons', 'category_id', 'categories', 'id'],
            ['deals', 'store_id', 'stores', 'id'],
            ['deals', 'category_id', 'categories', 'id'],
            ['products', 'store_id', 'stores', 'id'],
            ['products', 'category_id', 'categories', 'id'],
            ['favorites', 'user_id', 'users', 'id'],
        ];

        foreach ($checks as [$table, $fk, $parent, $pk]) {
            if (!$this->tableExists($table) || !$this->tableExists($parent)) {
                continue;
            }

            $orphans = DB::table($table . ' as t')
                ->leftJoin($parent . ' as p', 't.' . $fk, '=', 'p.' . $pk)
                ->whereNotNull('t.' . $fk)
                ->whereNull('p.' . $pk)
                ->count();

            if ($orphans > 0) {
                $issues[] = "$table.$fk has {$orphans} orphan rows (no parent in {$parent}.{$pk})";
            }
        }

        if (empty($issues)) {
            $this->info('No foreign key issues detected.');
            return Command::SUCCESS;
        }

        foreach ($issues as $issue) {
            $this->error($issue);
        }

        return Command::FAILURE;
    }

    private function tableExists(string $table): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

