<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UnauthorisedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_unauthorised_user_cannot_view_full_list_of_exams(): void
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get('/api/exams');

        $response->assertStatus(403);
        $response->assertExactJson(['msg' => 'Administrator access is required to perform this action.']);
    }

    public function test_get_unauthorised_user_cannot_view_full_list_of_users()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get('/api/users');

        $response->assertStatus(403);
        $response->assertExactJson(['msg' => 'Administrator access is required to perform this action.']);
    }


    public function test_get_unauthorised_user_cannot_view_individual_exams()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get('/api/exams/12');
        $response->assertStatus(404);
        $response->assertExactJson(['msg' => 'Not found.']);
    }


    public function test_post_forbids_access_to_unauthenticated_user()
    {
        $requestBody =   [
            "title" => "VICTVS15",
            "description" => "VICTVS Exam 15",
            "candidate_id" => 0,
            "candidate_name" => "Wilmers",
            "date" => "05/05/2023 14:30:00",
            "location_name" => "London",
            "latitude" => 51.50374306483545,
            "longitude" => -0.14074641294861687
        ];

        $response = $this->postJson('/api/exams', $requestBody);
        $response
            ->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }


    public function test_put_forbids_access_to_unauthenticated_user()
    {
        $requestBody = [
            'description' => 'new value'
        ];

        $response = $this->putJson('/api/exams/4', $requestBody);
        $response
            ->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function test_delete_forbids_access_to_unauthenticated_users()
    {
        $response = $this->deleteJson('/api/exams/12');
        $response
                ->assertStatus(401)
                ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function test_put_forbids_updates_to_other_users_exams()
    {
        $user1 = User::factory()->create();
        $this->actingAs($user1);
        $response = $this->putJson('/api/exams/2', ['description' => 'new']);
        $response->assertStatus(404)
                 ->assertExactJson(['msg' => 'Not found.']);
    }


}
