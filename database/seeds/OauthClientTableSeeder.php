<?php

use Illuminate\Database\Seeder;

class OauthClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->delete();
        DB::insert('insert into oauth_clients (id, secret, name) values (?, ?, ?)', ['EsXOQOgVS', 'ZwUCAxgbUjWAJgJyW5MEOIKp', 'Mobile Api']);
    }
}
