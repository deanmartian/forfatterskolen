<?php

namespace Tests\Feature\Api\V1;

use App\Assignment;
use App\AssignmentGroup;
use App\AssignmentGroupLearner;
use App\AssignmentManuscript;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssignmentGroupShowDetailsApiTest extends TestCase
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

        Schema::create('assignments', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('course_id')->nullable();
            $table->string('title')->default('');
            $table->text('description')->nullable();
            $table->string('submission_date')->nullable();
            $table->string('available_date')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_groups', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('assignment_id');
            $table->string('title')->nullable();
            $table->dateTime('submission_date')->nullable();
            $table->boolean('allow_feedback_download')->default(0);
            $table->timestamps();
        });

        Schema::create('assignment_group_learners', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('assignment_group_id');
            $table->unsignedInteger('user_id');
            $table->string('could_send_feedback_to')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_manuscripts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('assignment_id');
            $table->unsignedInteger('user_id');
            $table->string('filename')->nullable();
            $table->integer('status')->default(0);
            $table->boolean('locked')->default(0);
            $table->boolean('has_feedback')->default(0);
            $table->integer('words')->default(0);
            $table->dateTime('uploaded_at')->nullable();
            $table->timestamps();
        });

        config()->set('services.jwt.secret', 'test-secret');
        config()->set('api.jwt.access_ttl_minutes', 60);
    }

    public function test_group_show_details_requires_token(): void
    {
        $response = $this->getJson('/api/v1/assignment/group/1/show-details');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'error' => ['message', 'code'],
                'request_id',
            ]);
    }

    public function test_group_show_details_returns_expected_payload_for_member(): void
    {
        $user = User::create([
            'first_name' => 'Main',
            'last_name' => 'Learner',
            'email' => 'main@example.com',
            'password' => Hash::make('password'),
            'role' => User::LearnerRole,
            'is_active' => 1,
        ]);

        $otherUser = User::create([
            'first_name' => 'Other',
            'last_name' => 'Learner',
            'email' => 'other@example.com',
            'password' => Hash::make('password'),
            'role' => User::LearnerRole,
            'is_active' => 1,
        ]);

        $assignment = Assignment::create([
            'title' => 'Group Assignment',
            'description' => 'Test assignment',
            'submission_date' => now()->toDateTimeString(),
            'available_date' => now()->toDateTimeString(),
        ]);

        $group = AssignmentGroup::create([
            'assignment_id' => $assignment->id,
            'title' => 'Test Group',
            'submission_date' => now()->toDateTimeString(),
            'allow_feedback_download' => 1,
        ]);

        $otherLearner = AssignmentGroupLearner::create([
            'assignment_group_id' => $group->id,
            'user_id' => $otherUser->id,
        ]);

        $memberLearner = AssignmentGroupLearner::create([
            'assignment_group_id' => $group->id,
            'user_id' => $user->id,
            'could_send_feedback_to' => (string) $otherLearner->id,
        ]);

        AssignmentManuscript::create([
            'assignment_id' => $assignment->id,
            'user_id' => $user->id,
            'filename' => '/storage/assignment-manuscripts/sample.docx',
            'status' => 1,
            'locked' => 0,
            'has_feedback' => 1,
            'words' => 1234,
            'uploaded_at' => now(),
        ]);

        $token = $this->makeTokenForUser($user);

        $response = $this->getJson('/api/v1/assignment/group/'.$group->id.'/show-details', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.group.id', $group->id)
            ->assertJsonPath('data.group.assignment_id', $assignment->id)
            ->assertJsonPath('data.otherLearnersIdList.0', $otherLearner->id)
            ->assertJsonPath('data.groupLearnerList.0.id', $memberLearner->id)
            ->assertJsonPath('data.assignmentManuscript.assignment_id', $assignment->id)
            ->assertJsonPath('data.assignmentManuscript.user_id', $user->id)
            ->assertJsonStructure([
                'data' => [
                    'group',
                    'otherLearnersIdList',
                    'couldSendFeedbackTo',
                    'groupLearnerList',
                    'assignmentManuscript',
                ],
                'request_id',
            ]);
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
