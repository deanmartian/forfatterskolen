<?php

namespace Tests\Feature\Api\V1;

use App\Course;
use App\PaymentPlan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CoursePaymentPlansTest extends TestCase
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

        Schema::create('courses', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title')->default('');
            $table->text('description');
            $table->string('course_image')->default('');
            $table->string('type')->default('');
            $table->string('instructor')->nullable();
            $table->boolean('for_sale')->default(1);
            $table->integer('status')->default(1);
            $table->text('payment_plan_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_plans', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('plan');
            $table->integer('division');
            $table->timestamps();
        });
    }

    public function test_it_returns_payment_plans_for_course_in_course_order(): void
    {
        $course = Course::create([
            'title' => 'Course',
            'description' => 'desc',
            'course_image' => '',
            'type' => 'online',
            'instructor' => 'Instructor',
            'for_sale' => 1,
            'payment_plan_ids' => [3, 1, 99],
        ]);

        PaymentPlan::insert([
            ['id' => 1, 'plan' => '3 måneder', 'division' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'plan' => '12 måneder', 'division' => 12, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->getJson("/api/v1/course/{$course->id}/payment-plans");

        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', 3)
            ->assertJsonPath('data.0.plan', '12 måneder')
            ->assertJsonPath('data.0.division', 12)
            ->assertJsonPath('data.1.id', 1)
            ->assertJsonPath('data.1.plan', '3 måneder')
            ->assertJsonPath('data.1.division', 3)
            ->assertJsonCount(2, 'data');
    }

    public function test_it_returns_empty_array_when_payment_plan_ids_is_null(): void
    {
        $course = Course::create([
            'title' => 'Course',
            'description' => 'desc',
            'course_image' => '',
            'type' => 'online',
            'instructor' => 'Instructor',
            'for_sale' => 1,
            'payment_plan_ids' => null,
        ]);

        $response = $this->getJson("/api/v1/course/{$course->id}/payment-plans");

        $response->assertStatus(200)
            ->assertJsonPath('data', []);
    }

    public function test_it_returns_not_found_for_missing_or_not_for_sale_course(): void
    {
        $course = Course::create([
            'title' => 'Hidden Course',
            'description' => 'desc',
            'course_image' => '',
            'type' => 'online',
            'instructor' => 'Instructor',
            'for_sale' => 0,
            'payment_plan_ids' => [1],
        ]);

        $response = $this->getJson("/api/v1/course/{$course->id}/payment-plans");

        $response->assertStatus(404)
            ->assertJsonPath('error.code', 'not_found');
    }
}
