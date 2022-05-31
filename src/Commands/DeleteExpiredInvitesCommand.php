<?php

namespace Rubik\LaravelInvite\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredInvitesCommand extends Command
{
    public $signature = 'laravel-invite:delete-expired {--all}';

    public $description = 'Deletes all expired invitations based on the given values in the config file';

    /**
     * @return int
     */
    public function handle(): int
    {
        $this->info('Deleting invites...');

        $this->option('all')
            ? $this->info('Deleted ' . $this->deleteAllExpired() . ' invites!')
            : $this->info('Deleted ' . $this->deleteExpiredBasedOnConfig() . ' invites!');


        return self::SUCCESS;
    }

    /**
     * @return Carbon
     */
    public function getExpirationDate(): Carbon
    {
        return Carbon::now()->sub(config('invite.expire.after'), config('invite.unit'));
    }

    /**
     * @return int
     */
    public function deleteExpiredBasedOnConfig(): int
    {
        return config('invite.invite_model')::where('expires_at', '<=', $this->getExpirationDate())->expired()->delete();
    }

    /**
     * @return int
     */
    public function deleteAllExpired(): int
    {
        return config('invite.invite_model')::expired()->delete();
    }
}
