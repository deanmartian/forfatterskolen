<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class CourseApplicationApiTest extends TestCase
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
        config()->set('api.lovable_url', 'https://ny.forfatterskolen.no');

        Schema::create('courses', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title');
            $table->boolean('status')->default(1);
            $table->boolean('is_free')->default(0);
            $table->boolean('pay_later_with_application')->default(1);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('course_id');
            $table->boolean('is_reward')->default(0);
            $table->boolean('is_show')->default(1);
            $table->string('variation')->default('Standard');
            $table->decimal('full_payment_price', 10, 2)->default(1000);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->unsignedTinyInteger('role')->default(2);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('course_applications', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('package_id');
            $table->unsignedInteger('user_id');
            $table->text('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_returns_show_application_when_course_supports_application_flow(): void
    {
        $courseId = \DB::table('courses')->insertGetId([
            'title' => 'Application Course',
            'status' => 1,
            'is_free' => 0,
            'pay_later_with_application' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('packages')->insert([
            'course_id' => $courseId,
            'is_reward' => 0,
            'is_show' => 1,
            'variation' => 'Standard',
            'full_payment_price' => 12000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/courses/'.$courseId.'/application');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('action', 'show_application')
            ->assertJsonPath('redirect_url', 'https://ny.forfatterskolen.no/skrivekurs/'.$courseId.'/application');
    }

    public function test_it_returns_checkout_redirect_when_course_is_not_application_based(): void
    {
        $courseId = \DB::table('courses')->insertGetId([
            'title' => 'Checkout Course',
            'status' => 1,
            'is_free' => 0,
            'pay_later_with_application' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('packages')->insert([
            'course_id' => $courseId,
            'is_reward' => 0,
            'is_show' => 1,
            'variation' => 'Standard',
            'full_payment_price' => 12000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/courses/'.$courseId.'/application');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('action', 'redirect_checkout')
            ->assertJsonPath('redirect_url', 'https://ny.forfatterskolen.no/skrivekurs/'.$courseId.'/checkout');
    }

    public function test_it_processes_application_and_returns_thank_you_redirect(): void
    {
        $courseId = \DB::table('courses')->insertGetId([
            'title' => 'Application Course',
            'status' => 1,
            'is_free' => 0,
            'pay_later_with_application' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $packageId = \DB::table('packages')->insertGetId([
            'course_id' => $courseId,
            'is_reward' => 0,
            'is_show' => 1,
            'variation' => 'Standard',
            'full_payment_price' => 12000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/courses/'.$courseId.'/application/process', [
            'email' => 'applicant@example.com',
            'first_name' => 'Applicant',
            'last_name' => 'Writer',
            'phone' => '12345678',
            'manuscript' => UploadedFile::fake()->create('draft.docx', 32),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('action', 'application_submitted')
            ->assertJsonPath('redirect_url', 'https://ny.forfatterskolen.no/skrivekurs/'.$courseId.'/application/thank-you');

        $this->assertDatabaseHas('users', [
            'email' => 'applicant@example.com',
            'first_name' => 'Applicant',
            'last_name' => 'Writer',
        ]);

        $user = \DB::table('users')->where('email', 'applicant@example.com')->first();
        $this->assertAuthenticatedAs(\App\User::find($user->id));

        $this->assertDatabaseHas('course_applications', [
            'package_id' => $packageId,
            'user_id' => $user->id,
        ]);
    }

    public function test_it_rejects_duplicate_course_application_for_same_user_and_package(): void
    {
        $courseId = \DB::table('courses')->insertGetId([
            'title' => 'Application Course',
            'status' => 1,
            'is_free' => 0,
            'pay_later_with_application' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $packageId = \DB::table('packages')->insertGetId([
            'course_id' => $courseId,
            'is_reward' => 0,
            'is_show' => 1,
            'variation' => 'Standard',
            'full_payment_price' => 12000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = \DB::table('users')->insertGetId([
            'email' => 'duplicate@example.com',
            'first_name' => 'Duplicate',
            'last_name' => 'Writer',
            'password' => bcrypt(Str::random(12)),
            'role' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('course_applications')->insert([
            'package_id' => $packageId,
            'user_id' => $userId,
            'file_path' => '/existing.docx',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/courses/'.$courseId.'/application/process', [
            'email' => 'duplicate@example.com',
            'first_name' => 'Duplicate',
            'last_name' => 'Writer',
            'phone' => '12345678',
            'manuscript' => UploadedFile::fake()->create('new-draft.docx', 32),
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'duplicate_application');

        $this->assertAuthenticatedAs(\App\User::find($userId));
    }
}
