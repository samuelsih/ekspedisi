<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\User;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportCustomerJob implements ShouldQueue
{
    use Queueable;

    private readonly string $s3Url;

    private readonly User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $filename)
    {
        $this->s3Url = Storage::disk('s3')->temporaryUrl($filename, now()->addHour());
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [];

        $file = fopen($this->s3Url, 'r');
        fgetcsv($file);

        $totalRows = 0;
        $batchSize = 100;
        $now = now();

        while (($row = fgetcsv($file)) !== false) {
            $data[] = [
                'id' => str()->ulid(),
                'id_customer' => $row[0],
                'name' => $row[1],
                'created_at' => $now,
            ];

            if (count($data) >= $batchSize) {
                $success = $this->insertCustomer($data, $totalRows);
                if(! $success) {
                    return;
                }

                $data = [];
            }

            $totalRows++;
        }

        if (! empty($data)) {
            $success = $this->insertCustomer($data, $totalRows);
            if(! $success) {
                return;
            }

            $totalRows += count($data);
        }

        Notification::make()
            ->title('Import Customer Success')
            ->success()
            ->body("{$totalRows} rows already been imported")
            ->send()
            ->sendToDatabase($this->user);

        event(new DatabaseNotificationsSent($this->user));
    }

    private function insertCustomer(array $data, int $totalRows): bool
    {
        try {
            DB::transaction(fn () => Customer::insert($data));
        } catch (\Throwable $th) {
            $totalRows++;

            Log::error($th->getMessage());

            Notification::make()
                ->title('Import Customer Failed')
                ->danger()
                ->body("Row {$totalRows} has been failed to create. {$th->getMessage()}")
                ->send()
                ->sendToDatabase($this->user);

            event(new DatabaseNotificationsSent($this->user));

            return false;
        }

        return true;
    }
}
