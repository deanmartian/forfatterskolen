<?php

namespace Tests\Feature\Api\V1;

use App\Services\FileIntegrityService;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssignmentGroupSubmitFeedbackApiTest extends TestCase
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
            $table->integer('assignment_id')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_group_learners', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('assignment_group_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('assignment_feedbacks', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('assignment_group_learner_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('filename')->nullable();
            $table->timestamps();
        });

        config()->set('services.jwt.secret', 'test-secret');

        $this->app->bind(FileIntegrityService::class, fn () => new class extends FileIntegrityService
        {
            public function __construct() {}

            public function passes(string $path, ?string $extension = null): bool
            {
                return true;
            }
        });
    }

    public function test_submit_feedback_requires_token(): void
    {
        $response = $this->postJson('/api/v1/assignment/group/1/learner/2/submit_feedback');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'unauthorized');
    }

    public function test_submit_feedback_creates_group_feedback_for_target_learner(): void
    {
        $author = $this->createUser('author@example.com');
        $target = $this->createUser('target@example.com');
        $token = $this->makeTokenForUser($author);

        $groupId = \DB::table('assignment_groups')->insertGetId([
            'assignment_id' => 9,
            'title' => 'Group A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('assignment_group_learners')->insert([
            [
                'assignment_group_id' => $groupId,
                'user_id' => $author->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'assignment_group_id' => $groupId,
                'user_id' => $target->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $targetLearnerId = (int) \DB::table('assignment_group_learners')
            ->where('assignment_group_id', $groupId)
            ->where('user_id', $target->id)
            ->value('id');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/v1/assignment/group/'.$groupId.'/learner/'.$targetLearnerId.'/submit_feedback', [
            'filename' => [
                UploadedFile::fake()->create('feedback-one.docx', 10),
                UploadedFile::fake()->create('feedback-two.odt', 10),
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.assignment_group_learner_id', $targetLearnerId)
            ->assertJsonPath('data.user_id', $author->id);

        $this->assertDatabaseHas('assignment_feedbacks', [
            'assignment_group_learner_id' => $targetLearnerId,
            'user_id' => $author->id,
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
