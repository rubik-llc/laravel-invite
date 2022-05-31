<?php

namespace Rubik\LaravelInvite\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredInvitesCommand extends Command
{
    public $signature = 'invite:delete-expired {--all}';

    public $description = 'Deletes all expired invitations that have surpassed the amount of time given in the config file';

    /**
     * @return int
     */
    public function handle(): int
    {
        $this->info('Deleting invitations...');

        $this->option('all')
            ? $this->info('Deleted ' . $this->deleteAllExpired() . ' invitations!')
            : $this->info('Deleted ' . $this->deleteExpiredBasedOnConfig() . ' invitations!');


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
        return config('invite.invitation_model')::where('expires_at', '<=', $this->getExpirationDate())->expired()->delete();
    }

    /**
     * @return int
     */
    public function deleteAllExpired(): int
    {
        return config('invite.invitation_model')::expired()->delete();
    }
}
