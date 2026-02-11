<?php

namespace Tests\Feature\Api\V1;

use App\Order;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ShopManuscriptCheckoutTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('sqlite3')) {
            $this->markTestSkipped('SQLite3 extension is not available.');
        }

        config()->set('cache.default', 'array');
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
            $table->boolean('could_buy_course')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_preferred_editor', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('editor_id');
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('street')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('vipps_phone_number')->nullable();
            $table->timestamps();
        });

        Schema::create('shop_manuscripts', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('max_words')->default(0);
            $table->decimal('full_payment_price', 11, 2);
            $table->timestamps();
        });

        Schema::create('genre', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('payment_plans', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('division')->default(1);
            $table->timestamps();
        });

        Schema::create('payment_modes', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('mode');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->integer('item_id');
            $table->integer('type');
            $table->integer('package_id')->default(0);
            $table->integer('plan_id')->nullable();
            $table->integer('payment_mode_id')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('additional', 10, 2)->default(0);
            $table->string('svea_order_id')->nullable();
            $table->string('svea_invoice_id')->nullable();
            $table->tinyInteger('is_processed')->default(0);
            $table->tinyInteger('is_order_withdrawn')->default(0);
            $table->timestamps();
        });

        Schema::create('order_shop_manuscripts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->string('genre')->nullable();
            $table->string('file')->nullable();
            $table->integer('words')->nullable();
            $table->text('description')->nullable();
            $table->string('synopsis')->nullable();
            $table->tinyInteger('coaching_time_later')->default(0);
            $table->tinyInteger('send_to_email')->default(0);
            $table->timestamps();
        });

        Schema::create('shop_manuscripts_taken', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('shop_manuscript_id');
            $table->string('genre')->nullable();
            $table->text('description')->nullable();
            $table->string('file')->nullable();
            $table->integer('words')->nullable();
            $table->string('synopsis')->nullable();
            $table->tinyInteger('coaching_time_later')->default(0);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_welcome_email_sent')->default(false);
            $table->timestamps();
        });

        \App\PaymentPlan::create(['id' => 1, 'division' => 1]);
        \App\PaymentPlan::create(['id' => 2, 'division' => 3]);
        \App\PaymentMode::create(['id' => 1, 'mode' => 'Vipps']);
        \App\PaymentMode::create(['id' => 2, 'mode' => 'Svea']);
        \App\PaymentMode::create(['id' => 3, 'mode' => 'Faktura']);

        config()->set('services.jwt.secret', 'test-secret');
        config()->set('api.jwt.access_ttl_minutes', 60);
        config()->set('services.vipps.client_id', null);
        config()->set('services.vipps.client_secret', null);
        config()->set('services.vipps.msn', null);
    }

    public function test_creates_checkout_happy_path(): void
    {
        [$user, $token, $manuscript] = $this->seedCheckoutContext();

        $response = $this->checkout($token, $manuscript->id, 'idem-key-1001', 1, 1);

        $response->assertStatus(201)
            ->assertJsonStructure(['redirect_url', 'reference']);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $manuscript->id,
            'type' => Order::MANUSCRIPT_TYPE,
        ]);
    }

    public function test_idempotency_returns_same_order(): void
    {
        [, $token, $manuscript] = $this->seedCheckoutContext();

        $first = $this->checkout($token, $manuscript->id, 'idem-key-constant', 1, 1);
        $second = $this->checkout($token, $manuscript->id, 'idem-key-constant', 1, 1);

        $first->assertStatus(201);
        $second->assertStatus(201);
        $this->assertSame($first->json('order_id'), $second->json('order_id'));
        $this->assertEquals(1, Order::query()->count());
    }

    public function test_faktura_mode_id_three_is_treated_as_svea_provider(): void
    {
        [, $token, $manuscript] = $this->seedCheckoutContext();

        $response = $this->checkout($token, $manuscript->id, 'idem-key-faktura', 3, 1);

        $response->assertStatus(201)
            ->assertJsonStructure(['redirect_url', 'reference']);
    }

    public function test_unauthorized_user_cannot_read_or_cancel_others_order(): void
    {
        [$owner, $ownerToken, $manuscript] = $this->seedCheckoutContext('owner@example.com');
        [, $otherToken] = $this->seedCheckoutContext('other@example.com');

        $create = $this->checkout($ownerToken, $manuscript->id, 'idem-owner-key', 1, 1);
        $orderId = (int) $create->json('order_id');

        $show = $this->getJson('/api/v1/learner/shop-manuscripts/checkout/'.$orderId, [
            'Authorization' => 'Bearer '.$otherToken,
        ]);

        $cancel = $this->postJson('/api/v1/learner/shop-manuscripts/checkout/'.$orderId.'/cancel', [], [
            'Authorization' => 'Bearer '.$otherToken,
        ]);

        $show->assertStatus(404);
        $cancel->assertStatus(404);

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'user_id' => $owner->id,
            'is_processed' => 0,
        ]);
    }

    public function test_cancels_pending_order(): void
    {
        [, $token, $manuscript] = $this->seedCheckoutContext();

        $create = $this->checkout($token, $manuscript->id, 'idem-cancel-key', 1, 1);
        $orderId = (int) $create->json('order_id');

        $cancel = $this->postJson('/api/v1/learner/shop-manuscripts/checkout/'.$orderId.'/cancel', [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $cancel->assertStatus(200)
            ->assertJsonPath('status', 'cancelled');

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'is_order_withdrawn' => 1,
        ]);
    }

    public function test_webhook_updates_order_to_paid_and_creates_purchase(): void
    {
        [$user, $token, $manuscript] = $this->seedCheckoutContext();

        $create = $this->checkout($token, $manuscript->id, 'idem-webhook-key', 1, 1);
        $orderId = (int) $create->json('order_id');

        $order = Order::find($orderId);
        $order->svea_order_id = 'sm-'.$orderId.'-'.$user->id;
        $order->save();

        $webhook = $this->postJson('/api/v1/payments/vipps/shop-manuscripts/webhook', [
            'transactionInfo' => [
                'orderId' => 'sm-'.$orderId.'-'.$user->id,
                'status' => 'CAPTURED',
            ],
        ]);

        $webhook->assertStatus(200)->assertJson(['ok' => true]);

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'is_processed' => 1,
        ]);

        $this->assertDatabaseHas('shop_manuscripts_taken', [
            'user_id' => $user->id,
            'shop_manuscript_id' => $manuscript->id,
        ]);
    }

    public function test_by_word_count_returns_matching_shop_manuscript_plan(): void
    {
        [$user, $token] = $this->seedCheckoutContext();

        \App\ShopManuscript::create([
            'title' => 'Big Plan',
            'description' => 'Large manuscript',
            'max_words' => 60000,
            'full_payment_price' => 2990,
        ]);

        $response = $this->getJson('/api/v1/learner/shop-manuscripts/by-word-count?word_count=18000', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.max_words', 20000);
    }

    private function checkout(string $token, int $manuscriptId, string $idempotencyKey, int $paymentModeId, int $paymentPlanId)
    {
        $genreId = \App\Genre::query()->firstOrCreate(['name' => 'Fiction'])->id;

        return $this->post('/api/v1/learner/shop-manuscripts/'.$manuscriptId.'/checkout', [
            'payment_mode_id' => $paymentModeId,
            'payment_plan_id' => $paymentPlanId,
            'genre' => $genreId,
            'description' => 'API checkout',
            'manuscript' => UploadedFile::fake()->create('draft.docx', 10, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            'word_count' => 6000,
        ], [
            'Authorization' => 'Bearer '.$token,
            'Idempotency-Key' => $idempotencyKey,
            'Accept' => 'application/json',
        ]);
    }

    private function seedCheckoutContext(string $email = 'learner@example.com'): array
    {
        $user = User::create([
            'first_name' => 'Learner',
            'last_name' => 'User',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => User::LearnerRole,
            'is_active' => 1,
            'could_buy_course' => 1,
        ]);

        $manuscript = \App\ShopManuscript::create([
            'title' => 'Manuskript',
            'description' => 'Description',
            'max_words' => 20000,
            'full_payment_price' => 1490,
        ]);

        return [$user, $this->makeTokenForUser($user), $manuscript];
    }

    private function makeTokenForUser(User $user): string
    {
        return JWT::encode([
            'iss' => config('app.url', 'http://localhost'),
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => now()->timestamp,
            'exp' => now()->addMinutes(60)->timestamp,
            'jti' => (string) $user->id.'-checkout-test',
        ], config('services.jwt.secret'), 'HS256');
    }
}
