<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
            ->assertJsonPath('application_url', 'https://ny.forfatterskolen.no/skrivekurs/'.$courseId.'/application');
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
}
