<?php

namespace Tests\Feature\Api\V1;

use App\Course;
use App\CoursesTaken;
use App\Package;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CourseTakenTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

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

        Schema::create('courses', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title')->default('');
            $table->text('description');
            $table->string('course_image')->default('');
            $table->string('type')->default('');
            $table->string('instructor')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('for_sale')->default(1);
            $table->integer('status')->default(1);
            $table->boolean('is_free')->default(0);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->text('variation');
            $table->text('description');
            $table->decimal('full_payment_price', 11);
            $table->decimal('months_3_price', 11);
            $table->decimal('months_6_price', 11);
            $table->decimal('months_12_price', 11);
            $table->string('full_price_product')->default('');
            $table->string('months_3_product')->default('');
            $table->string('months_6_product')->default('');
            $table->string('months_12_product')->default('');
            $table->integer('full_price_due_date')->default(0);
            $table->integer('months_3_due_date')->default(0);
            $table->integer('months_6_due_date')->default(0);
            $table->integer('months_12_due_date')->default(0);
            $table->boolean('course_type')->default(0);
            $table->boolean('is_reward')->default(0);
            $table->boolean('is_show')->default(1);
            $table->boolean('is_upgradeable')->default(1);
            $table->timestamps();
        });

        Schema::create('package_courses', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->integer('included_package_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('courses_taken', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('package_id')->unsigned();
            $table->boolean('is_active')->default(1);
            $table->dateTime('started_at')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('access_lessons')->default('[]');
            $table->integer('years')->default(1);
            $table->boolean('is_free')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        config()->set('services.jwt.secret', 'test-secret');
        config()->set('api.jwt.access_ttl_minutes', 60);
    }

    public function test_taken_requires_token(): void
    {
        $response = $this->getJson('/api/v1/courses/taken');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'error' => ['message', 'code'],
                'request_id',
            ]);
    }

    public function test_taken_returns_empty_collection_for_valid_user(): void
    {
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => User::LearnerRole,
            'is_active' => 1,
        ]);

        $token = $this->makeTokenForUser($user);

        $response = $this->getJson('/api/v1/courses/taken', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'request_id'])
            ->assertJsonCount(0, 'data');
    }

    public function test_taken_handles_null_dates(): void
    {
        $user = User::create([
            'first_name' => 'Legacy',
            'last_name' => 'User',
            'email' => 'legacy@example.com',
            'password' => Hash::make('password'),
            'role' => User::LearnerRole,
            'is_active' => 1,
        ]);

        $course = Course::create([
            'title' => 'Legacy Course',
            'description' => 'Course description',
            'course_image' => '',
            'type' => 'online',
            'instructor' => 'Instructor',
        ]);

        $package = Package::unguarded(function () use ($course) {
            return Package::create([
                'course_id' => $course->id,
                'variation' => 'Standard',
                'description' => 'Package description',
                'full_payment_price' => 1000,
                'months_3_price' => 400,
                'months_6_price' => 200,
                'months_12_price' => 100,
                'full_price_product' => 'full',
                'months_3_product' => 'm3',
                'months_6_product' => 'm6',
                'months_12_product' => 'm12',
                'full_price_due_date' => 0,
                'months_3_due_date' => 0,
                'months_6_due_date' => 0,
                'months_12_due_date' => 0,
                'course_type' => 0,
                'is_reward' => 0,
                'is_show' => 1,
                'is_upgradeable' => 1,
            ]);
        });

        CoursesTaken::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'is_active' => 1,
            'started_at' => null,
            'start_date' => null,
            'end_date' => null,
            'access_lessons' => '[]',
            'years' => 1,
            'is_free' => 0,
        ]);

        $token = $this->makeTokenForUser($user);

        $response = $this->getJson('/api/v1/courses/taken', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.0.start_date', null)
            ->assertJsonPath('data.0.end_date', null)
            ->assertJsonPath('data.0.started_at', null);
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
