<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'role' => 'user',
        ]);

        $user = User::where('email', 'testuser@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_admin_can_update_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'role' => 'user',
            'password' => 'oldpassword',
        ]);

        $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'password' => '',
                'password_confirmation' => '',
                'role' => 'admin',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_update_user_password(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'email' => 'password@example.com',
            'password' => 'oldpassword',
            'role' => 'user',
        ]);

        $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'role' => 'user',
            ])
            ->assertRedirect(route('users.index'));

        $user->refresh();

        $this->assertTrue(
            Hash::check('newpassword123', $user->password)
        );
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_user_requires_name(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_requires_email(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Test User',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_requires_password(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_password_must_be_confirmed(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'differentpassword',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_password_must_be_at_least_8_characters(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => '1234567',
                'password_confirmation' => '1234567',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_email_must_be_valid(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Test User',
                'email' => 'invalid-email',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_email_must_be_unique(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Another User',
                'email' => 'existing@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_role_is_required(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors('role');
    }

    public function test_user_role_must_be_valid(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'superadmin',
            ]);

        $response->assertSessionHasErrors('role');
    }

    public function test_user_name_cannot_exceed_255_characters(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => str_repeat('A', 256),
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_email_cannot_exceed_255_characters(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $email = str_repeat('a', 250) . '@example.com';

        $response = $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Test User',
                'email' => $email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_keep_same_email_when_updating_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'Original User',
            'email' => 'same@example.com',
            'role' => 'user',
        ]);

        $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => 'Updated User',
                'email' => 'same@example.com',
                'password' => '',
                'password_confirmation' => '',
                'role' => 'user',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
            'email' => 'same@example.com',
        ]);
    }

    public function test_admin_cannot_update_user_with_existing_email(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $user = User::factory()->create([
            'email' => 'user@example.com',
            'role' => 'user',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'email' => 'existing@example.com',
                'password' => '',
                'password_confirmation' => '',
                'role' => 'user',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_create_admin_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'New Admin',
                'email' => 'newadmin@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'admin',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('error', 'You cannot delete your own account.');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_guest_cannot_create_user(): void
    {
        $this->post(route('users.store'), [
            'name' => 'Unauthorized User',
            'email' => 'unauthorized@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('users', [
            'email' => 'unauthorized@example.com',
        ]);
    }
}