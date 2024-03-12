<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\Exam;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $newUser = [
            "name" => "Alex Pitfall",
		    "email" => "alex_pp7@v3.admin",
		    "password" => "uttdis8766ss",
		    "password_confirmation" => "uttdis8766ss"
        ];

        $response = $this->postJson('/api/signup', $newUser);
        Sanctum::actingAs(User::find($response['user']['id']));
    }


    public function test_get_request_returns_all_exams(): void
    {
        $response = $this->get('/api/exams');

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('exams', 30)
                     ->hasAll('links', 'meta')
            );
    }


    public function test_get_returns_exams_sorted_in_desc_date_order_by_default(): void
    {
        // Get an array of Exams directly from the database, ordered by date DESC
        $examsFromDb = Exam::orderBy('date', 'desc')->get()->toArray();
        $datesFromDb = array_slice(
            array_map(fn ($m) => $m['date'], $examsFromDb), 0, 50
        );
        
        // Get an array of Exams via the API, of the same length as the above array.
        $response = $this->get('/api/exams?limit=50');
        $datesFromApi = array_map(fn ($m) => $m['date'], $response['exams']);

        // Assert that the response objects must be sorted in the correct order.
        $response->assertStatus(200);
        $this->assertEquals($datesFromDb, $datesFromApi);
    }


    public function test_get_returns_single_exam()
    {
        $response = $this->get('/api/exams/3');
        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) => 
                        $json->has('exam', fn (AssertableJson $json) =>
                            $json->hasAll(['id', 'title', 'description', 'candidateId', 'candidateName', 'date', 'locationName', 'longitude', 'latitude'])
                        )
                  );
    }


    public function test_get_returns_404_for_nonexistent_exam()
    {
        $response = $this->get('/api/exams/3333333');
        $response->assertStatus(404)
                 ->assertExactJson(['msg' => 'Not found.']);
    }


    public function test_get_allows_exams_to_be_sorted_in_asc_date_order(): void
    {
         $examsFromDb = Exam::orderBy('date', 'asc')->get()->toArray();
         $datesFromDb = array_slice(
             array_map(fn ($m) => $m['date'], $examsFromDb), 0, 50
         );
         
         $response = $this->get('/api/exams?limit=50&order=asc');
         $datesFromApi = array_map(fn ($m) => $m['date'], $response['exams']);
 
         $response->assertStatus(200);
         $this->assertEquals($datesFromDb, $datesFromApi);
    }


    public function test_get_allows_exams_to_be_filtered_by_location(): void
    {
        Exam::factory()->count(7)->create(['location_name' => 'Montut']);
        $response = $this->get('/api/exams?location=montut');
        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) =>
                        $json->has('exams', 7, fn (AssertableJson $json) =>
                                $json->where('locationName', 'Montut')
                                     ->etc()
                        )->etc()
                );
    }


    public function test_get_allows_exams_to_be_filtered_by_candidate_name()
    {
        Exam::factory()->count(12)->create(['candidate_name' => 'Ziupard Charles']);
        $response = $this->get('/api/exams/search/ziupard');
        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) =>
                        $json->has('exams', 12, fn (AssertableJson $json) =>
                                $json->where('candidateName', 'Ziupard Charles')
                                     ->etc()
                        )->etc()
                );
    }

    public function test_get_allows_exams_to_be_filtered_by_date()
    {
        Exam::factory()->count(4)->create(['date' => '2021-12-14']);
        $response = $this->get('/api/exams?date=2021-12-14');
        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) =>
                        $json->has('exams', 4, fn (AssertableJson $json) =>
                                $json->where('date', '2021-12-14')
                                     ->etc()
                        )->etc()
                );
    }

    public function test_get_allows_exams_to_be_filtered_by_month()
    {
        Exam::factory()->count(4)->create(['date' => '2023-10-19']);
        $response = $this->get('/api/exams?month=10');
        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) =>
                        $json->has('exams.0', fn (AssertableJson $json) =>
                                $json->where('date', fn (string $date) => Str::of($date)->is('202*-10-*'))
                                     ->etc()
                        )->etc()
                );
    }

    public function test_get_allows_exams_to_be_filtered_by_month_and_year()
    {
        Exam::factory()->count(4)->create(['date' => '2029-08-04']);
        $response = $this->get('/api/exams?month=8&year=2029');
        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) =>
                        $json->has('exams.0', fn (AssertableJson $json) =>
                                $json->where('date', fn (string $date) => Str::of($date)->is('2029-08-*'))
                                     ->etc()
                        )->etc()
                );
    }

    public function test_get_allows_exams_to_be_filtered_to_dates_occurring_after_specified_query()
    {
        Exam::factory()->count(10)->create();
        $response = $this->get('/api/exams?after=' . Carbon::now());
        $response->assertStatus(200);
        foreach($response['exams'] as $exam)
        {
            $this->assertTrue(Carbon::parse($exam['date']) > Carbon::now());
        }
    }

    public function test_get_allows_exams_to_be_filtered_by_name()
    {
        Exam::factory()->count(14)->create(['candidate_name' => 'John Abruzzi']);
        $response = $this->get('/api/exams?name=abruzz');
        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) =>
                        $json->has('exams', 14, fn (AssertableJson $json) =>
                                $json->where('candidateName', 'John Abruzzi')
                                     ->etc()
                        )->etc()
                );
    }

    public function test_get_allows_exams_to_be_filtered_by_date_and_location()
    {
        Exam::factory()->count(6)->create(['date' => '2021-12-14', 'location_name' => 'glasgow']);
        $response = $this->get('/api/exams?date=2021-12-14&location=glasgow');
        $response->assertStatus(200)
                 ->assertJson(fn (AssertableJson $json) =>
                        $json->has('exams', 6, fn (AssertableJson $json) =>
                                $json->where('date', '2021-12-14')
                                     ->where('locationName', 'glasgow')
                                     ->etc()
                        )->etc()
                );
    }


    public function test_get_returns_404_for_nonexistent_user()
    {
        $request = $this->get('/api/users/44/exams');
        $request->assertStatus(404)
                ->assertExactJson(['msg' => 'Not found.']);
    }


    public function test_get_returns_empty_array_when_searching_for_names_not_in_db()
    {
        $response = $this->get('/api/exams/search/374653743');
        $response->assertStatus(200)
                 ->assertExactJson(['exams' => []]);
    }


    public function test_get_request_returns_all_users(): void
    {
        $response = $this->get('/api/users');
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->has('users', 43)
        );
    }


    public function test_post_request_adds_exam_to_database(): void
    {
        $user = User::factory()->create();
        $newExam =   [
            "title" => "VICTVS15",
            "description" => "VICTVS Exam 15",
            "candidate_id" => $user->id,
            "candidate_name" => $user->name,
            "date" => "05/05/2023 14:30:00",
            "location_name" => "London",
            "latitude" => 51.50374306483545,
            "longitude" => -0.14074641294861687
        ];
        
        $response = $this->postJson('/api/exams', $newExam);

        $response
            ->assertStatus(201)
            ->assertJson($newExam);
   
    }

    public function test_post_does_not_allow_multiple_exams_to_be_created_in_same_time_slot()
    {
        $user = User::factory()->create();
        $newExam =   [
            "title" => "VICTVS15",
            "description" => "VICTVS Exam 15",
            "candidate_id" => $user->id,
            "candidate_name" => $user->name,
            "date" => "05/05/2023 14:30:00",
            "location_name" => "London",
            "latitude" => 51.50374306483545,
            "longitude" => -0.14074641294861687
        ];

        $newExam2 =   [
            "title" => "VICTVS16",
            "description" => "VICTVS Exam 16",
            "candidate_id" => $user->id,
            "candidate_name" => $user->name,
            "date" => "05/05/2023 14:30:00",
            "location_name" => "Sydney",
            "latitude" => 51.50374306483545,
            "longitude" => -0.14074641294861687
        ];
        
        $this->postJson('/api/exams', $newExam);
        $response = $this->postJson('/api/exams', $newExam2);

        $response->assertStatus(400)
                 ->assertExactJson(['msg' => 'Candidate is already booked in for an exam at this time.']);

    }


    public function test_post_returns_400_if_foreign_key_does_not_match_primary_key()
    {
        $user = User::find(2);
        $request = [
            "title" => "VICTVS15",
            "description" => "VICTVS Exam 15",
            "candidate_id" => 2,
            "candidate_name" => $user->name . "let's violate the schema constraint",
            "date" => "05/05/2024 14:30:00",
            "location_name" => "London",
            "latitude" => 51.50374306483545,
            "longitude" => -0.14074641294861687
        ];

        $response = $this->postJson('/api/exams', $request);
        $response->assertStatus(400)
                 ->assertExactJson(['msg' => 'Candidate\'s name does not match his or her existing name on record.']);

    }


    public function test_post_returns_404_if_attempting_to_create_a_new_exam_for_a_nonexistent_user()
    {
        $request = [
            "title" => "VICTVS15",
            "description" => "VICTVS Exam 15",
            "candidate_id" => 47,
            "candidate_name" => "Daniel Polt",
            "date" => "05/05/2024 14:30:00",
            "location_name" => "London",
            "latitude" => 51.50374306483545,
            "longitude" => -0.14074641294861687
        ];

        $response = $this->postJson('/api/exams', $request);
        $response->assertStatus(404)
                 ->assertExactJson(['msg' => 'That user does not exist in the database.']);
    }

    public function test_put_admin_can_update_any_exam(): void
    {
        $response = $this->putJson('/api/exams/12', [
            'title' => 'new title',
            'location_name' => 'Liverpool',
            'description' => 'new description goes here'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('title', 'new title')
                     ->where('location_name', 'Liverpool')
                     ->where('description', 'new description goes here')
                     ->hasAll(['id', 'title', 'description', 'location_name', 'candidate_id', 'candidate_name', 'date', 'longitude', 'latitude'])
                     ->etc()
            );
    }
}
