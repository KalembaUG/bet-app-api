<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;

class ApiRootController extends Controller
{
    public function endpoints()
    {
        return response()->json(['available endpoints' => [
            'POST /signup' => [
                'auth' => 'public endpoint',
                'description' => 'allows a user to create a new account',
                'example request body' => [
                    'name' => 'Michael Daniels',
                    'email' => 'newemail@email.co.uk',
                    'password' => '738bfeygufdsr',
                    'password_confirmation' => '738bfeygufdsr'
                ],
                'returns' => 'API key which is required to access other services'
            ],
            'POST /login' => [
                'auth' => 'public endpoint',
                'description' => 'allows a user to sign in to an existing account',
                'example request body' => [
                    'email' => 'newemail@email.co.uk',
                    'password' => '738bfeygufdsr',
                ],
                'returns' => 'API key which is required to access other services'
            ],
            'GET /logout/{id}' => [
                'auth' => 'only logged-in users can access this endpoint',
                'description' => 'logs user out and revokes all previous API tokens',
            ],
            'GET /exams' => [
                'auth' => 'requires admin access',
                'description' => 'returns list of all exams',
                'available queries' => [
                    'order' => 'ASC or DESC',
                    'location' => 'Any value accepted',
                    'date' => 'Must be in this format: 2023-12-13',
                    'month' => 'Must be an integer value, e.g. 2, 6, 10, etc'
                ]
            ],
            'GET /exams/{id}' => [
                'auth' => 'user can only access his/her own exams, while an admin can access any single exam.',
                'description' => 'returns single exam'
            ],
            'GET /exams/search/{name}' => [
                'auth' => 'requires admin access',
                'description' => 'returns a list of exams where the candidate name contains the substring specific in the URL'
            ],
            'PUT /exams/{id}' => [
                'auth' => 'user can only update his/her own exams, while an admin can update any exam.',
                'description' => 'allows exam data to be modified',
                'example request body' => [
                    'description' => 'new description',
                    'location_name' => 'New location',
                    'latitude' => 465.1532842,
			        'longitude' => 472.5534568
                    ],
            ],
            'DELETE /exams/{id}' => [
                'auth' => 'user can only delete his/her own exams, while an admin can delete any exam.',
                'description' => 'deletes exam',
            ],
            'POST /exams' => [
                'auth' => 'requires admin accesss',
                'description' => 'adds a new exam to the database',
                'example request body' => [
                    'title' => "VICTVS422",
                    'description' => "VICTVS Exam 8",
                    'candidateId' => 2,
                    'candidateName' => "Prof. Ed Jaskolski",
                    'date' => '2027-03-03 07:46:42',
                    'locationName' => 'Corneliushaven',
                    'latitude' => 160.2525259,
                    'longitude' => 220.7580725
                ]
            ],
            'GET /users' => [
                'auth' => 'requires admin access',
                'description' => 'returns list of all users'
            ],
            'GET /users/{id}/exams' => [
                'auth' => 'a user can only view his/her own exams, while an admin has no restrictions.',
                'description' => 'returns a list of all exams for a specific candidate'
            ]
         ]], 200, [], JSON_UNESCAPED_SLASHES);
    }
}
