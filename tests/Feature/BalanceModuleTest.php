<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Balance\Models\Balance;
use App\Modules\Balance\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $user1;
    protected $user2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
    }

    /** @test */
    public function it_can_deposit_funds_to_user_account()
    {
        $response = $this->postJson('/api/deposit', [
            'user_id' => $this->user1->id,
            'amount' => 500.00,
            'comment' => 'Test deposit'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Deposit successful'
            ]);

        $this->assertDatabaseHas('balances', [
            'user_id' => $this->user1->id,
            'balance' => 500.00
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user1->id,
            'type' => 'deposit',
            'amount' => 500.00,
            'comment' => 'Test deposit'
        ]);
    }

    /** @test */
    public function it_can_withdraw_funds_from_user_account()
    {
        // First deposit
        $this->postJson('/api/deposit', [
            'user_id' => $this->user1->id,
            'amount' => 500.00
        ]);

        // Then withdraw
        $response = $this->postJson('/api/withdraw', [
            'user_id' => $this->user1->id,
            'amount' => 200.00,
            'comment' => 'Test withdrawal'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Withdrawal successful'
            ]);

        $this->assertDatabaseHas('balances', [
            'user_id' => $this->user1->id,
            'balance' => 300.00
        ]);
    }

    /** @test */
    public function it_prevents_withdrawal_with_insufficient_balance()
    {
        // Deposit only 100
        $this->postJson('/api/deposit', [
            'user_id' => $this->user1->id,
            'amount' => 100.00
        ]);

        // Try to withdraw 200
        $response = $this->postJson('/api/withdraw', [
            'user_id' => $this->user1->id,
            'amount' => 200.00
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'Insufficient balance'
            ]);

        // Balance should remain unchanged
        $this->assertDatabaseHas('balances', [
            'user_id' => $this->user1->id,
            'balance' => 100.00
        ]);
    }

    /** @test */
    public function it_can_transfer_funds_between_users()
    {
        // Give user1 some balance
        $this->postJson('/api/deposit', [
            'user_id' => $this->user1->id,
            'amount' => 500.00
        ]);

        // Transfer to user2
        $response = $this->postJson('/api/transfer', [
            'from_user_id' => $this->user1->id,
            'to_user_id' => $this->user2->id,
            'amount' => 150.00,
            'comment' => 'Test transfer'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Transfer successful'
            ]);

        // Check sender balance
        $this->assertDatabaseHas('balances', [
            'user_id' => $this->user1->id,
            'balance' => 350.00
        ]);

        // Check receiver balance
        $this->assertDatabaseHas('balances', [
            'user_id' => $this->user2->id,
            'balance' => 150.00
        ]);

        // Check transaction records
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user1->id,
            'type' => 'transfer_out',
            'amount' => 150.00
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user2->id,
            'type' => 'transfer_in',
            'amount' => 150.00
        ]);
    }

    /** @test */
    public function it_prevents_transfer_with_insufficient_balance()
    {
        // Give user1 only 100
        $this->postJson('/api/deposit', [
            'user_id' => $this->user1->id,
            'amount' => 100.00
        ]);

        // Try to transfer 200
        $response = $this->postJson('/api/transfer', [
            'from_user_id' => $this->user1->id,
            'to_user_id' => $this->user2->id,
            'amount' => 200.00
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'Insufficient balance'
            ]);
    }

    /** @test */
    public function it_can_get_user_balance()
    {
        // Create balance
        $this->postJson('/api/deposit', [
            'user_id' => $this->user1->id,
            'amount' => 350.00
        ]);

        $response = $this->getJson('/api/balance/' . $this->user1->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user_id' => $this->user1->id,
                    'balance' => 350.00
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_balance()
    {
        $response = $this->getJson('/api/balance/' . $this->user1->id);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'User has no balance record'
            ]);
    }

    /** @test */
    public function it_validates_deposit_request()
    {
        $response = $this->postJson('/api/deposit', [
            'user_id' => 999999, // Non-existent user
            'amount' => -100 // Invalid amount
        ]);

        $response->assertStatus(422); // Validation error
    }

    /** @test */
    public function it_prevents_transfer_to_same_user()
    {
        $response = $this->postJson('/api/transfer', [
            'from_user_id' => $this->user1->id,
            'to_user_id' => $this->user1->id, // Same user
            'amount' => 100.00
        ]);

        $response->assertStatus(422); // Validation error
    }

    /** @test */
    public function transfer_creates_linked_transactions()
    {
        // Give user1 balance
        $this->postJson('/api/deposit', [
            'user_id' => $this->user1->id,
            'amount' => 500.00
        ]);

        // Transfer
        $this->postJson('/api/transfer', [
            'from_user_id' => $this->user1->id,
            'to_user_id' => $this->user2->id,
            'amount' => 100.00
        ]);

        // Get the transactions
        $transferOut = Transaction::where('user_id', $this->user1->id)
            ->where('type', 'transfer_out')
            ->first();

        $transferIn = Transaction::where('user_id', $this->user2->id)
            ->where('type', 'transfer_in')
            ->first();

        // Assert they are linked
        $this->assertNotNull($transferOut);
        $this->assertNotNull($transferIn);
        $this->assertEquals($transferIn->id, $transferOut->related_transaction_id);
        $this->assertEquals($transferOut->id, $transferIn->related_transaction_id);
    }
}
