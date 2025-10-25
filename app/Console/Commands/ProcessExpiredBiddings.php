<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BiddingService;


class ProcessExpiredBiddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:process-expired-biddings';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    
      protected $signature = 'bidding:process';
    protected $description = 'Process expired biddings and notify users';

    public function handle(BiddingService $biddingService)
    {
        $biddingService->processExpiredBiddings();
        $this->info('Expired biddings processed successfully.');
    }
    
    
}
