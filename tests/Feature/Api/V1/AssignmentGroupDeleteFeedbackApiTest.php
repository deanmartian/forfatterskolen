<?php

namespace Tests\Feature\Api\V1;

use App\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssignmentGroupDeleteFeedbackApiTest extends TestCase
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
            $table->integer('role')->default(2);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assignment_feedbacks', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('assignment_group_learner_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('filename')->nullable();
            $table->timestamps();
        });

        config()->set('services.jwt.secret', 'test-secret');
    }

    public function test_delete_feedback_requires_token(): void
    {
        $response = $this->postJson('/api/v1/feedback/1/delete_feedback');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'unauthorized');
    }

    public function test_delete_feedback_removes_feedback_owned_by_learner(): void
    {
        $author = $this->createUser('author@example.com');
        $token = $this->makeTokenForUser($author);

        $feedbackId = \DB::table('assignment_feedbacks')->insertGetId([
            'assignment_group_learner_id' => 2,
            'user_id' => $author->id,
            'filename' => '/storage/assignment-feedbacks/test.docx',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/v1/feedback/'.$feedbackId.'/delete_feedback');

        $response->assertOk()
            ->assertJsonPath('data.id', $feedbackId)
            ->assertJsonPath('data.deleted', true);

        $this->assertDatabaseMissing('assignment_feedbacks', [
            'id' => $feedbackId,
        ]);
    }

    private function createUser(string $email): User
    {
        return User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => User::LearnerRole,
            'is_active' => 1,
        ]);
    }

    private function makeTokenForUser(User $user): string
    {
        $now = time();

        return JWT::encode([
            'iss' => config('app.url', 'http://localhost'),
            'sub' => $user->id,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + 3600,
        ], config('services.jwt.secret'), 'HS256');
    }
}
