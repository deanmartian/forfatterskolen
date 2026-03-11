<?php

namespace Tests\Feature\Api;

use App\Course;
use App\CoursesTaken;
use App\Package;
use App\User;
use App\UserPreference;
use Firebase\JWT\JWT;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CommunityUserApiTest extends TestCase
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

        Schema::create('courses', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title')->default('');
            $table->text('description')->nullable();
            $table->string('course_image')->default('');
            $table->string('type')->default('online');
            $table->boolean('is_free')->default(0);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->text('variation')->nullable();
            $table->text('description')->nullable();
            $table->decimal('full_payment_price', 11)->default(0);
            $table->decimal('months_3_price', 11)->default(0);
            $table->decimal('months_6_price', 11)->default(0);
            $table->decimal('months_12_price', 11)->default(0);
            $table->boolean('is_reward')->default(0);
            $table->boolean('is_show')->default(1);
            $table->timestamps();
        });

        Schema::create('courses_taken', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('package_id')->unsigned();
            $table->boolean('is_active')->default(1);
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_preferences', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('role')->nullable();
            $table->boolean('joined_reader_community')->default(0);
            $table->timestamps();
        });

        config()->set('services.jwt.secret', 'test-secret');
    }

    public function test_accepts_request_id_header_without_api_key(): void
    {
        $user = $this->makeUser();

        $response = $this->getJson('/api/community/user', [
            'X-Request-Id' => 'community-sync-req-1',
            'Authorization' => 'Bearer '.$this->makeTokenForUser($user),
        ]);

        $response->assertOk()
            ->assertHeader('X-Request-Id', 'community-sync-req-1')
            ->assertJsonPath('request_id', 'community-sync-req-1');
    }

    public function test_requires_authenticated_user(): void
    {
        $response = $this->getJson('/api/community/user', [
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'unauthorized');
    }

    public function test_returns_community_payload(): void
    {
        $user = $this->makeUser();

        UserPreference::create([
            'user_id' => $user->id,
            'joined_reader_community' => 1,
        ]);

        $activeCourse = Course::create([
            'title' => 'Dramaturgi',
            'description' => 'desc',
            'course_image' => '',
            'type' => 'online',
        ]);

        $expiredCourse = Course::create([
            'title' => 'Old Course',
            'description' => 'desc',
            'course_image' => '',
            'type' => 'online',
        ]);

        $activePackage = Package::create([
            'course_id' => $activeCourse->id,
            'variation' => 'Standard',
            'description' => 'desc',
        ]);

        $expiredPackage = Package::create([
            'course_id' => $expiredCourse->id,
            'variation' => 'Standard',
            'description' => 'desc',
        ]);

        CoursesTaken::create([
            'user_id' => $user->id,
            'package_id' => $activePackage->id,
            'is_active' => 1,
            'end_date' => now()->addDay()->toDateString(),
        ]);

        CoursesTaken::create([
            'user_id' => $user->id,
            'package_id' => $expiredPackage->id,
            'is_active' => 1,
            'end_date' => now()->subDay()->toDateString(),
        ]);

        $response = $this->getJson('/api/community/user', [
            'Authorization' => 'Bearer '.$this->makeTokenForUser($user),
        ]);

        $response->assertOk()
            ->assertJson([
                'external_user_id' => $user->id,
                'name' => 'Helge Skurtveit',
                'first_name' => 'Helge',
                'last_name' => 'Skurtveit',
                'email' => 'helge@example.com',
                'community_access' => true,
                'roles' => ['member'],
            ])
            ->assertJsonPath('course_access.0', 'dramaturgi')
            ->assertJsonCount(1, 'course_access');
    }

    private function makeUser(): User
    {
        return User::create([
            'first_name' => 'Helge',
            'last_name' => 'Skurtveit',
            'email' => 'helge@example.com',
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
            'jti' => 'community-test-token',
        ], config('services.jwt.secret'), 'HS256');
    }
}
