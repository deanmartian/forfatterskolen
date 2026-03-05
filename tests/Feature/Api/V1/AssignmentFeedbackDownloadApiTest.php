<?php

namespace Tests\Feature\Api\V1;

use App\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssignmentFeedbackDownloadApiTest extends TestCase
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

        Schema::create('assignment_groups', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('assignment_id')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_group_learners', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('assignment_group_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });

        Schema::create('assignment_feedbacks', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('assignment_group_learner_id');
            $table->unsignedInteger('user_id');
            $table->string('filename')->nullable();
            $table->boolean('is_active')->default(1);
            $table->dateTime('availability')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_manuscripts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('assignment_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_feedbacks_no_group', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('assignment_manuscript_id')->nullable();
            $table->unsignedInteger('learner_id');
            $table->unsignedInteger('feedback_user_id')->nullable();
            $table->string('filename')->nullable();
            $table->boolean('is_active')->default(1);
            $table->dateTime('availability')->nullable();
            $table->timestamps();
        });

        config()->set('services.jwt.secret', 'test-secret');
        config()->set('api.jwt.access_ttl_minutes', 60);
    }

    public function test_learner_assignment_feedback_download_route_requires_token(): void
    {
        $response = $this->getJson('/api/v1/learner/assignment/feedback/1/download');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'error' => ['message', 'code'],
                'request_id',
            ]);
    }

    public function test_learner_assignment_feedback_download_named_route_matches_expected_path(): void
    {
        $url = route('api.v1.learner.assignment.feedback.download', [
            'id' => 99,
            'v' => 123,
            'type' => 'group',
        ], false);

        $this->assertSame('/api/v1/learner/assignment/feedback/99/download?v=123&type=group', $url);
    }

    public function test_download_feedback_returns_conflict_for_ambiguous_id_without_type(): void
    {
        $user = User::create([
            'first_name' => 'Main',
            'last_name' => 'Learner',
            'email' => 'main@example.com',
            'password' => Hash::make('password'),
            'role' => User::LearnerRole,
            'is_active' => 1,
        ]);

        $groupId = \DB::table('assignment_groups')->insertGetId([
            'assignment_id' => 1,
            'title' => 'Group 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupLearnerId = \DB::table('assignment_group_learners')->insertGetId([
            'assignment_group_id' => $groupId,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('assignment_manuscripts')->insert([
            'id' => 7,
            'assignment_id' => 1,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('assignment_feedbacks')->insert([
            'id' => 55,
            'assignment_group_learner_id' => $groupLearnerId,
            'user_id' => $user->id,
            'filename' => '/storage/assignment-feedback/group.docx',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('assignment_feedbacks_no_group')->insert([
            'id' => 55,
            'assignment_manuscript_id' => 7,
            'learner_id' => $user->id,
            'feedback_user_id' => $user->id,
            'filename' => '/storage/assignment-feedback/no-group.docx',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $token = $this->makeTokenForUser($user);

        $response = $this->getJson('/api/v1/learner/assignment/feedback/55/download', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('error.code', 'ambiguous_feedback_id');
    }

    protected function makeTokenForUser(User $user): string
    {
        $secret = config('services.jwt.secret');
        $now = time();
        $ttl = (int) config('api.jwt.access_ttl_minutes', 60) * 60;

        return JWT::encode([
            'sub' => $user->id,
            'iat' => $now,
            'exp' => $now + $ttl,
        ], $secret);
    }
}
