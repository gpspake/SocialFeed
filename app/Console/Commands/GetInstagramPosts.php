<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetInstagramPosts extends Command
{

    use SocialMedia\Instagram;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getInstagramPosts {--delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Instagram Posts';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('delete')) {
            $this->deleteExistingPosts();
            return null;
        }

        $this->updatePosts();
    }
}
