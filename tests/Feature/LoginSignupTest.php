<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\User;

class LoginSignupTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $newUser = [
            "name" => "Alex Pitfall",
		    "email" => "alex_pp7@randomemail.com",
		    "password" => "uttdis8766",
		    "password_confirmation" => "uttdis8766"
        ];
        $this->postJson('/api/signup', $newUser);
    }
    
    public function test_post_signup_new_user_and_returns_api_token(): void
    {
        $newUser = [
            "name" => "Jake Arthur",
            "email" => "gf6yh@email.com",
            "password" => "hexagon98_sd",
            "password_confirmation" => "hexagon98_sd"
        ];

        $response = $this->postJson('/api/signup', $newUser);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('token')
                     ->whereType('token', 'string')
                     ->has('user', fn(AssertableJson $json) => 
                        $json->where('name', 'Jake Arthur')
                             ->where('email', fn (string $email) => str($email)->is('gf6yh@email.com'))
                             ->missing('password')
                             ->etc()
                     )
            );
    }

    public function test_post_registered_user_can_login_and_receives_new_token()
    {
        $loginDetails = [
		    "email" => "alex_pp7@randomemail.com",
		    "password" => "uttdis8766"
        ];

        $this->postJson('/api/signup', $loginDetails);

        $response = $this->postJson('/api/login', [
            'email' => $loginDetails['email'], 
            'password' => $loginDetails['password']
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('token')
                     ->whereType('token', 'string')
                     ->has('user', fn(AssertableJson $json) => 
                        $json->where('name', 'Alex Pitfall')
                             ->where('email', fn (string $email) => str($email)->is('alex_pp7@randomemail.com'))
                             ->etc()
                     )
            );
    }

    public function test_get_logout_destroys_api_token_and_returns_confirmation_message()
    {
        $loginConf = $this->postJson('/api/login/', [
            "email" => "alex_pp7@randomemail.com",
            "password" => "uttdis8766",
        ]);
        $id = $loginConf['user']['id'];

        $user = User::find($id);
        $this->actingAs($user);

        $response = $this->get('/api/logout/' . $id);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'token' => $loginConf['token'],
        ]);
        $response
            ->assertStatus(200)
            ->assertExactJson(['msg' => 'You are now logged out.']);
    }

    public function test_post_rejects_signup_if_email_is_already_taken()
    {
        $newUser = [
            "name" => "Alex Pitfall",
		    "email" => "alex_pp7@randomemail.com",
		    "password" => "uttdis8766",
		    "password_confirmation" => "uttdis8766"
        ];

        $response = $this->postJson('/api/signup', $newUser);

        $response
            ->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('message', 'The email has already been taken.')
                     ->etc()
            );
    }

    public function test_post_rejects_signup_if_password_is_too_short()
    {
        $newUser = [
            "name" => "Rebecca Biers",
		    "email" => "bec2005@kmail.co.uk",
		    "password" => "234",
		    "password_confirmation" => "234"
        ];

        $response = $this->postJson('/api/signup', $newUser);

        $response
            ->assertStatus(422)
            ->assertInvalid(['password' => 'The password field must be at least 10 characters']);
    }

    public function test_post_rejects_signup_if_passwords_do_not_match()
    {
        $newUser = [
            "name" => "Rebecca Biers",
		    "email" => "bec2005@kmail.co.uk",
		    "password" => "2343425dfgdfgdfg",
		    "password_confirmation" => "234serxdfsdfds"
        ];

        $response = $this->postJson('/api/signup', $newUser);

        $response
            ->assertStatus(422)
            ->assertInvalid(['password' => 'The password field confirmation does not match']);
    }

    public function test_post_rejects_login_if_email_does_not_exist_in_database()
    {
        $existingUser = [
		    "email" => "bec2005@kmail.co.uk",
		    "password" => "2343425dfgdfgdfg"
        ];

        $response = $this->postJson('/api/login', $existingUser);

        $response
            ->assertStatus(404)
            ->assertExactJson(['msg' => 'Invalid email']);
    }

    public function test_post_rejects_login_if_password_is_incorrect()
    {
        $existingUser = [
		    "email" => "alex_pp7@randomemail.com",
		    "password" => "uttdis8766d",
        ];

        $response = $this->postJson('/api/login', $existingUser);

        $response
            ->assertStatus(400)
            ->assertExactJson(['msg' => 'Incorrect password']);
    }
}
