<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FrontendHelpersApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('sqlite3')) {
            $this->markTestSkipped('SQLite3 extension is not available.');
        }

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        $this->app['db']->purge('sqlite');
        $this->app['db']->reconnect('sqlite');

        Schema::create('genre', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
        });
    }

    public function test_it_returns_genres_from_frontend_helpers(): void
    {
        $this->app['db']->table('genre')->insert([
            ['name' => 'Fantasy'],
            ['name' => 'Krim'],
        ]);

        $response = $this->getJson('/api/v1/genres');

        $response->assertStatus(200)
            ->assertJsonPath('0.name', 'Fantasy')
            ->assertJsonPath('1.name', 'Krim');
    }

    public function test_it_returns_manuscript_types_from_frontend_helpers(): void
    {
        $response = $this->getJson('/api/v1/manuscript-types');

        $response->assertStatus(200)
            ->assertJsonPath('0.id', 1)
            ->assertJsonPath('0.option', 'Hele manuset')
            ->assertJsonPath('3.id', 4)
            ->assertJsonPath('3.option', 'Slutten av manuset');
    }
}
