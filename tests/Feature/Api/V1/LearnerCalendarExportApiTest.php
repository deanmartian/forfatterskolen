<?php

namespace Tests\Feature\Api\V1;

use App\Services\LearnerCalendarService;
use App\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class LearnerCalendarExportApiTest extends TestCase
{
    use WithFaker;

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
            $table->integer('role')->default(2);
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

        config()->set('services.jwt.secret', 'test-secret');
        config()->set('api.jwt.access_ttl_minutes', 60);
    }

    public function test_export_requires_token(): void
    {
        $response = $this->get('/api/v1/learner/calendar/export');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'auth_required');
    }

    public function test_export_returns_calendar_file(): void
    {
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'calendar-api@example.com',
            'password' => Hash::make('password'),
            'role' => User::LearnerRole,
            'is_active' => 1,
        ]);

        $service = Mockery::mock(LearnerCalendarService::class);
        $service->shouldReceive('eventsForUser')
            ->once()
            ->with($user)
            ->andReturn(new Collection([
                [
                    'title' => 'Calendar Export Event',
                    'start' => Carbon::parse('2025-01-20 10:00:00'),
                    'end' => Carbon::parse('2025-01-20 11:00:00'),
                    'all_day' => false,
                ],
            ]));
        $this->app->instance(LearnerCalendarService::class, $service);

        $response = $this->get('/api/v1/learner/calendar/export', [
            'Authorization' => 'Bearer '.$this->makeTokenForUser($user),
        ]);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/calendar; charset=utf-8')
            ->assertHeader('Content-Disposition', 'attachment; filename="learner-calendar.ics"');

        $response->assertSee('BEGIN:VCALENDAR', false);
        $response->assertSee('SUMMARY=Calendar Export Event', false);
        $response->assertSee('END:VCALENDAR', false);
    }

    private function makeTokenForUser(User $user): string
    {
        $issuedAt = now()->timestamp;
        $expiresAt = now()->addMinutes(60)->timestamp;

        return JWT::encode([
            'iss' => config('app.url', 'http://localhost'),
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => $issuedAt,
            'exp' => $expiresAt,
            'jti' => $this->faker->uuid,
        ], config('services.jwt.secret'), 'HS256');
    }
}
