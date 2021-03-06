<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetFacebookPosts extends Command
{

    use \App\SocialMedia\Facebook;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getFacebookPosts {--delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Facebook posts';

    function handle() {

        if ($this->option('delete')) {
            $this->deleteExistingPosts();
            return null;
        }

        $this->updatePosts();
    }
    
}
