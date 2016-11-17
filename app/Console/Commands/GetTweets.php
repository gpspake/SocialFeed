<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetTweets extends Command
{

    use \App\SocialMedia\Twitter;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getTweets {--delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get tweets';

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
