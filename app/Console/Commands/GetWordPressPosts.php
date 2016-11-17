<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetWordPressPosts extends Command
{

    use SocialMedia\WordPress;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getWordPressPosts {--delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get WordPress Posts';

    /**
     * The service name
     *
     * @var string
     */
    protected $service_name = 'wordpress';

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
