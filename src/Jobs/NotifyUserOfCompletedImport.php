<?php

namespace Uccello\Import\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Uccello\Import\Notifications\ImportIsReadyNotification;

class NotifyUserOfCompletedImport implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $importType;

    public function __construct(User $user, $importType)
    {
        $this->user = $user;
        $this->importType = $importType;
    }

    public function handle()
    {
        $this->user->notify(new ImportIsReadyNotification($this->importType));
    }
}
