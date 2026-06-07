<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present_on_api_responses(): void
    {
        $response = $this->getJson('/api/user/products');

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->assertHeader('Content-Security-Policy');
    }

    public function test_user_can_register_and_login(): void
    {
        // 1. Register User
        $registerResponse = $this->postJson('/api/user/register', [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'skin_type' => 'Oily',
            'skin_concern' => 'Acne',
        ]);

        $registerResponse->assertStatus(201);
        $registerResponse->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => ['user_id', 'full_name', 'email', 'skin_type', 'skin_concern'],
                'token'
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'full_name' => 'John Doe'
        ]);

        // 2. Login User
        $loginResponse = $this->postJson('/api/user/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200);
        $loginResponse->assertJsonStructure([
            'success',
            'message',
            'data' => ['user', 'token']
        ]);
    }

    public function test_admin_can_register_and_login(): void
    {
        // 1. Register Admin
        $registerResponse = $this->postJson('/api/admin/register', [
            'admin_name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => 'adminpassword',
        ]);

        $registerResponse->assertStatus(201);
        $this->assertDatabaseHas('admins', [
            'email' => 'admin@example.com',
            'admin_name' => 'Super Admin'
        ]);

        // 2. Login Admin
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'adminpassword',
        ]);

        $loginResponse->assertStatus(200);
        $loginResponse->assertJsonStructure([
            'success',
            'data' => ['admin', 'token']
        ]);
    }

    public function test_user_cannot_access_admin_endpoints(): void
    {
        // Create user & authenticate
        $user = User::create([
            'full_name' => 'John User',
            'email' => 'user@example.com',
            'password_hash' => bcrypt('password'),
        ]);

        $token = $user->createToken('token')->plainTextToken;

        // Try to create category as user (Admin-only)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/admin/categories', [
            'category_name' => 'Cleansers',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Access denied. Administrator privileges required.'
        ]);
    }

    public function test_admin_cannot_access_user_protected_endpoints(): void
    {
        // Create admin & authenticate
        $admin = Admin::create([
            'admin_name' => 'Admin User',
            'email' => 'admin@example.com',
            'password_hash' => bcrypt('password'),
        ]);

        $token = $admin->createToken('token')->plainTextToken;

        // Try to access user routines (requires User guard)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user/routines');

        // Since it checks $request->user() which resolves to Admin and the route has 'auth:sanctum' which authorizes the admin token,
        // but user-specific endpoints expect User model, or AdminAuthenticate blocks, wait, the routines route doesn't have admin.auth middleware.
        // Wait! How is `RoutineController` protected from Admin accessing it?
        // Let's check `RoutineController::index()`:
        // `$userId = auth()->user()->user_id;`
        // If an admin requests it, does Admin model have `user_id` attribute? No, it has `admin_id`. So `$userId` would be null or throw.
        // Let's verify what happens. Ideally, user-only routes should block admin. Let's make sure.
        // We can add a middleware or check instance in the controller.
        // Wait, let's write a simple User Authenticate middleware or checking logic, or handle it in the Controller / Policies.
        // In our policies (e.g. `RoutinePolicy`):
        // `return $user instanceof User && ...`
        // So for routes like `RoutineController::show`, it uses the Policy and blocks admins!
        // But for `index()`, let's see. In `RoutineController::index()`, it does:
        // `$userId = auth()->user()->user_id;` which is null for admin since Admin has `admin_id`.
        // To be completely secure and prevent type mismatch or unauthorized operations, we should enforce that the authenticated user on user-only routes is an instance of `User`.
        // Let's check if we can create a `UserAuthenticate` middleware or check user instance in `index()`, or check if we should do that.
        // Let's create a custom middleware `UserAuthenticate` and apply it to user-only routes!
        // That is an excellent security measure! It guarantees guard isolation: only `User` models can access user endpoints, and only `Admin` models can access admin endpoints.
        // Let's write `UserAuthenticate` middleware and add it to `bootstrap/app.php` and then apply it to `routes/api.php`!
        // Let's assert 403 for admin accessing user routes.
        
        $response->assertStatus(403);
    }
}
