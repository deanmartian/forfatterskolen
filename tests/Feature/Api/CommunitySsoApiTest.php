<?php

namespace Tests\Feature\Api;

use App\ApiCommunitySsoCode;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CommunitySsoApiTest extends TestCase
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

        Schema::create('users', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('role')->default(User::LearnerRole);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_preferred_editor', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('editor_id')->unsigned()->index();
            $table->timestamps();
        });

        Schema::create('api_community_sso_codes', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('code_hash', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        config()->set('services.jwt.secret', 'test-secret');
        config()->set('api.jwt.access_ttl_minutes', 15);
        config()->set('api.community_sso.code_ttl_seconds', 120);
    }

    public function test_issue_code_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/community/issue-code');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'unauthorized');
    }

    public function test_issue_and_exchange_code_returns_access_token(): void
    {
        $user = $this->makeUser();

        $issueResponse = $this->postJson('/api/v1/community/issue-code', [], [
            'Authorization' => 'Bearer '.$this->makeTokenForUser($user),
        ]);

        $issueResponse->assertOk()
            ->assertJsonStructure(['code', 'expires_at']);

        $code = $issueResponse->json('code');

        $exchangeResponse = $this->postJson('/api/community/exchange-code', [
            'code' => $code,
        ]);

        $exchangeResponse->assertOk()
            ->assertJsonStructure(['access_token', 'expires_in', 'token_type'])
            ->assertJsonPath('token_type', 'Bearer');

        $payload = JWT::decode(
            $exchangeResponse->json('access_token'),
            new Key(config('services.jwt.secret'), 'HS256')
        );

        $this->assertSame($user->id, $payload->sub);
        $this->assertSame($user->email, $payload->email);
    }

    public function test_exchange_code_is_one_time_use(): void
    {
        $user = $this->makeUser();

        $code = 'one-time-community-code';

        ApiCommunitySsoCode::create([
            'user_id' => $user->id,
            'code_hash' => hash('sha256', $code),
            'expires_at' => now()->addMinute(),
        ]);

        $this->postJson('/api/community/exchange-code', ['code' => $code])
            ->assertOk();

        $this->postJson('/api/community/exchange-code', ['code' => $code])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'unauthorized');
    }

    private function makeUser(): User
    {
        return User::create([
            'first_name' => 'Forum',
            'last_name' => 'User',
            'email' => 'forum@example.com',
            'password' => Hash::make('password'),
            'role' => User::LearnerRole,
            'is_active' => 1,
        ]);
    }

    private function makeTokenForUser(User $user): string
    {
        return JWT::encode([
            'iss' => config('app.url'),
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => now()->timestamp,
            'exp' => now()->addMinutes(30)->timestamp,
            'jti' => 'community-sso-test-token',
        ], config('services.jwt.secret'), 'HS256');
    }
}
