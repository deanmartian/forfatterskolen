<?php

namespace Tests\Feature\Api\V1;

use App\Course;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CourseForSaleTest extends TestCase
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
            $table->text('description')->nullable();
            $table->text('description_raw')->nullable();
            $table->text('short_description')->nullable();
            $table->string('slug')->nullable();
            $table->string('course_image')->default('');
            $table->string('thumbnail_url')->nullable();
            $table->boolean('for_sale')->default(1);
            $table->integer('status')->default(1);
            $table->tinyInteger('pay_later_with_application')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Cache::flush();
    }

    public function test_it_returns_pay_later_with_application_in_for_sale_response(): void
    {
        Course::create([
            'title' => 'Course 1',
            'description' => 'desc',
            'course_image' => '/img/course-1.png',
            'for_sale' => 1,
            'status' => 1,
            'pay_later_with_application' => 1,
        ]);

        Course::create([
            'title' => 'Course 2',
            'description' => 'desc',
            'course_image' => '/img/course-2.png',
            'for_sale' => 1,
            'status' => 1,
            'pay_later_with_application' => 0,
        ]);

        $response = $this->getJson('/api/v1/courses/for-sale');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.pay_later_with_application', false)
            ->assertJsonPath('data.1.pay_later_with_application', true);
    }
}
