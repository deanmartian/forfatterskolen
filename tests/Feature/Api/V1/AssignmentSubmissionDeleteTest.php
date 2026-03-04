<?php

namespace Tests\Feature\Api\V1;

use App\AssignmentManuscript;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssignmentSubmissionDeleteTest extends TestCase
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

        Schema::create('assignment_manuscripts', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('assignment_id')->nullable();
            $table->integer('user_id')->unsigned();
            $table->string('filename')->nullable();
            $table->integer('words')->default(0);
            $table->timestamps();
        });

        config()->set('services.jwt.secret', 'test-secret');
    }

    public function test_delete_submission_requires_token(): void
    {
        $response = $this->deleteJson('/api/v1/assignments/submissions/1');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'unauthorized');
    }

    public function test_delete_submission_deletes_owned_manuscript_and_file(): void
    {
        $user = $this->createUser('owner@example.com');
        $token = $this->makeTokenForUser($user);

        $relativeFilePath = '/storage/assignment-manuscripts/test-delete.docx';
        $absoluteFilePath = public_path(ltrim($relativeFilePath, '/'));

        File::ensureDirectoryExists(dirname($absoluteFilePath));
        File::put($absoluteFilePath, 'temporary test file');

        $manuscript = AssignmentManuscript::create([
            'assignment_id' => 5,
            'user_id' => $user->id,
            'filename' => $relativeFilePath,
            'words' => 123,
        ]);

        $response = $this->deleteJson('/api/v1/assignments/submissions/'.$manuscript->id, [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $manuscript->id)
            ->assertJsonPath('data.deleted', true);

        $this->assertDatabaseMissing('assignment_manuscripts', ['id' => $manuscript->id]);
        $this->assertFalse(File::exists($absoluteFilePath));
    }

    public function test_delete_submission_rejects_other_user_submission(): void
    {
        $owner = $this->createUser('owner2@example.com');
        $otherUser = $this->createUser('other@example.com');
        $token = $this->makeTokenForUser($otherUser);

        $manuscript = AssignmentManuscript::create([
            'assignment_id' => 7,
            'user_id' => $owner->id,
            'filename' => null,
            'words' => 10,
        ]);

        $response = $this->deleteJson('/api/v1/assignments/submissions/'.$manuscript->id, [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'forbidden');

        $this->assertDatabaseHas('assignment_manuscripts', ['id' => $manuscript->id]);
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
