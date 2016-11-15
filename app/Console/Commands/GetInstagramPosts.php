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
     * The service name
     *
     * @var string
     */
    protected $service_name = 'instagram';

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
