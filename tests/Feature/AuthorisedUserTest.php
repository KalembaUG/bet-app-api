<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;

class AuthorisedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_put_authorised_candidate_can_update_own_exam_details()
    {
        $user = User::factory()->create(['name' => 'Jackson Bubblebeard']);
        $exam = Exam::factory()->create(['candidate_id' => $user->id]);
        $this->actingAs($user);
        
        $request = ['location_name' => 'Mars'];
        $response = $this->putJson('/api/exams/' . $exam->id, $request);

        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) => 
                        $json->where('location_name','Mars')
                             ->etc()
    );
    }

    public function test_put_prevents_updates_to_id_field()
    {
        $user = User::factory()->create(['name' => 'Jackson Bubblebeard']);
        $exam = Exam::factory()->create(['candidate_id' => $user->id]);
        $this->actingAs($user);
        
        $request = ['candidate_id' => 16];
        $response = $this->putJson('/api/exams/' . $exam->id, $request);

        $response->assertStatus(400)
                 ->assertExactJson(['msg' => 'You cannot change a candidate\'s name or ID.']);
    }


    public function test_put_prevents_updates_to_candidate_name_field()
    {
        $user = User::factory()->create(['name' => 'Jackson Bubblebeard']);
        $exam = Exam::factory()->create(['candidate_id' => $user->id]);
        $this->actingAs($user);
        
        $request = ['candidate_name' => 'John Ferman'];
        $response = $this->putJson('/api/exams/' . $exam->id, $request);

        $response->assertStatus(400)
                 ->assertExactJson(['msg' => 'You cannot change a candidate\'s name or ID.']);
    }

    public function test_get_authorised_user_can_view_single_exam()
    {
        $user = User::factory()->has(Exam::factory()->count(4))->create();
        $examId = Exam::where('candidate_id', $user->id)->first()->id;

        $this->actingAs($user);
        $response = $this->get('api/exams/' . $examId);
        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) => 
                    $json->has('exam', fn (AssertableJson $json) => 
                          $json->hasAll('id', 'title', 'description', 'candidateId')
                               ->etc()
                        )
    );
    }


    public function test_get_authorised_candidate_can_view_all_their_exams()
    {
        $user = User::factory()
                      ->has(Exam::factory()->count(8))
                      ->create();
        
        $this->actingAs($user);
        $response = $this->get('api/users/' . $user->id . '/exams');
        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) =>
                        $json->has('exams', 8)
                             ->etc()
                );
    }


    public function test_delete_authorised_candidate_can_delete_own_exams()
    {
        $user = User::factory()->create();
        $exam = Exam::factory()->create(['candidate_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->deleteJson('/api/exams/' . $exam->id);
        $response->assertStatus(200);
    }


    public function test_get_returns_404_for_nonexistent_exam()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get('/api/exams/3333333');
        $response->assertStatus(404)
                 ->assertExactJson(['msg' => 'Not found.']);
    }


    public function test_put_returns_404_for_nonexistent_exam()
    {
        $this->actingAs(User::factory()->create());
        $request = ['candidate_name' => 'Donald Donaldson'];
        $response = $this->putJson('/api/exams/333333343434343', $request);
        $response->assertStatus(404)
                 ->assertExactJson(['msg' => 'Not found.']);
    }
}
