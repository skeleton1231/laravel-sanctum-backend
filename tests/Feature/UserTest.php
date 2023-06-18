<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// php artisan test --filter UserTest
class UserTest extends TestCase
{
    use RefreshDatabase;  // This trait is used to rollback database changes after each test

    /**
     * Test register a new user
     *
     * @return void
     */
    public function test_user_can_register()
    {
        $userData = [
            "name" => "John Doe",
            "email" => "johndoe@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ];

        $response = $this->postJson('/api/v1/user/register', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
            'access_token',
            'token_type',
        ]);
    }


    /**
     * Test user can login
     *
     * @return void
     */
    public function test_user_can_login()
    {
        // Use the User factory to create a new user
        User::factory()->create([
            "name" => "John Doe",
            "email" => "johndoe@example.com",
            "password" => bcrypt("password"),
        ]);

        $response = $this->postJson('/api/v1/user/login', [
            "email" => "johndoe@example.com",
            "password" => "password",
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
            'access_token',
            'token_type',
        ]);
    }

    /**
     * Test get user details
     *
     * @return void
     */
    public function test_user_can_get_details()
    {
        $userData = [
            "name" => "John Doe",
            "email" => "johndoe@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ];

        // Register the user first
        $this->postJson('/api/v1/user/register', $userData);

        // Login the user
        $response = $this->postJson('/api/v1/user/login', [
            "email" => "johndoe@example.com",
            "password" => "password",
        ]);

        $token = $response->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/user/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logout successfully']);
    }

}

/*
    这是你需要的 cURL 测试代码：

注册新用户：
```bash
curl -X POST -H "Content-Type: application/json" \
-d '{
    "name": "John Doe",
    "email": "johndoe@example.com",
    "password": "password",
    "password_confirmation": "password"
}' \
http://localhost:8000/api/v1/user/register
```

用户登录：
```bash
curl -X POST -H "Content-Type: application/json" \
-d '{
    "email": "johndoe@example.com",
    "password": "password"
}' \
http://localhost/api/v1/user/login
```
这将返回一个访问令牌，我们将在后续请求中使用它。

获取用户详细信息（替换 `<your_token>` 为你在上一个请求中得到的令牌）：
```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer <your_token>" \
http://localhost/api/v1/user
```

用户注销（替换 `<your_token>` 为你在上一个请求中得到的令牌）：
```bash
curl -X POST -H "Content-Type: application/json" -H "Authorization: Bearer <your_token>" \
http://localhost/api/v1/user/logout
```

请注意，这些 cURL 请求假设你的 API 端点正在本地运行并监听 localhost。如果你的 API 是在其它地方运行，你需要更改 `http://localhost` 到你的 API 实际的 URL。
*/
