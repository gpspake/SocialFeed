<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(FeedTableSeeder::class);
    }
}

class FeedTableSeeder extends Seeder
{
    public function run()
    {
        App\Feed::truncate();

        factory(App\Feed::class, 20)->create();
    }
}