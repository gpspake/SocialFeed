<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetTweets extends Command
{

    use SocialMedia\Twitter;

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
    protected $description = 'Get my tweets';

    /**
     * The service name
     *
     * @var string
     */
    protected $service_name = 'twitter';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->deleteExistingPosts();

        if ($this->option('delete')) {
            return null;
        }

        $posts = $this->parsePosts( $this->getPosts() );

        $this->savePosts($posts);
    }
}
