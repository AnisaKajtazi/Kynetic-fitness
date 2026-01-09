<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderItem;
use Carbon\Carbon;

class ResetWeeklyOrderItems extends Command
{
    protected $signature = 'orders:reset-weekly';
    protected $description = 'Reset order items every Monday';

    public function handle()
    {
        $startOfLastWeek = Carbon::now()->startOfWeek()->subWeek();
        $endOfLastWeek = Carbon::now()->endOfWeek()->subWeek();

        $deleted = OrderItem::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->delete();

        $this->info("Deleted $deleted order items from last week.");

        return 0;
    }
}
