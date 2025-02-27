<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class InsertTokoCSVCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:insert-toko';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $handle = fopen(storage_path('app/private/toko.csv'), 'r');
        fgetcsv($handle);

        while (($row = fgets($handle)) !== false) {
            Customer::query()->create([
                'id_customer' => $row[0],
                'name' => $row[1],
            ]);
        }
    }
}
