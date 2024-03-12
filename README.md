# Laravel/PHP back end project

**[Link to live production build](https://laravel-php-api.vercel.app/public/api)**

Welcome to another one of my API projects. For this project, I have written, tested and deployed a Laravel application as part of a full-stack project, taking advantage of many of the framework's powerful features including:
- facades and helper functions;
- user authentication and authorisation;
- database seeding;
- Eloquent ORM query builder;
- advanced API testing tools.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## What is it for?
This API processes data related to users/candidates and their exams. For example, it allows a user to view information about exam venues/locations, dates, candidate names, and so on.
<br>

## An important note on access
- Ensure that you include an **Accept: application/json** HTTP header with all requests to the API.
- Most of the API's features are only available to admin-level users. To experiment with this app, you can sign up for a new account using an email that ends with **@v3.admin** which will give you full admin privileges.

```json
// To sign up as an admin, make a POST request to https://laravel-php-api.vercel.app/public/api/signup and include a request body in the following format:

        {
		    "name": "Anna Torpid",
		    "email": "annt@v3.admin",
		    "password": "dfbdf9suhfd9shf",
		    "password_confirmation": "dfbdf9suhfd9shf"
        }

// To login, make a POST request to https://laravel-php-api.vercel.app/public/api/login and include a request body in the following format:

        {
		    "email": "annt@v3.admin",
		    "password": "dfbdf9suhfd9shf"
        }

// The above POST request returns this response. Make sure to include the returned token in the authorisation header of all future requests.

        {
	        "user": {
		        "id": 11,
		        "name": "Anna Torpid",
		        "email": "annt@v3.admin"
	        },
	        "token": "5|tAujbY9luWTKquNEruGHU7soCXp7MuzVb8WR0VO9"
        }
```

Once you have signed up or logged in, you will be issued with an API token which will need to be attached to your request headers in order to ensure full CRUD access.
<br>


## Available endpoints
To access each endpint, append the URI fragment to the root endoint.
You can view further details by visiting the [root endpoint](https://laravel-php-api.vercel.app/public/api).

Resource | Description | Authentication/authorisation
---|---|---
POST /signup | Create new account. | Public
POST /login | Log in to existing account. | Public
GET /logout/{id} | Log out (revokes tokens). | Logged-in users only
GET /exams | Shows list of all exams. **Includes 5 optional query parameters**. | Admin-only
GET /exams/{id} | Get specific exam. | Can only see own exams
PUT /exams/{id} | Modify a specific exam. | Can only edit own exams
GET /exams/search/{name} | Substring search for specific candidate. | Admin-only
DELETE /exams/{id} | Delete exam. | Can only delete own exam
GET /users | Get list of all users. | Admin-only
GET /users/{id}/exams | Get list of all exams for a user | Can only see own exams
<br>



## Key product features
- Deployed on Vercel as a 'Serverless Function', utilising a PHP runtime to handle the applications's configuration.
- Production deployment is linked to a live PSQL database hosted remotely with a different provider.
- Expressive Laravel-specific syntax with extensive method-chaining, allowing for more concise code structure.
- Developed in a test-driven manner using PHPUnit and Laravel's fluent JSON syntax (based on closures and the AssertableJson class).
- Automated testing with a CI/CD workflow configured using Github Actions.
- Architected using an object-oriented MVC pattern
- Authentication services implemented using Sanctum
    - Users are able to sign up and log in.
    - Protected routes are only accessible to users with valid API tokens.
- Authorization services implemented using Laravel's gates, a closure-based approach to authorization.
    - Certain actions are only permitted to users with specific roles, such as admin status.
- Relationships and schema constraints defined using Eloquent ORM's relationships tools
- SQLite database used for development and testing.
<br>


## Main files and folders in the project directory
- ./app contains the core logic of the application. 
    - /app/Http/Controllers contains the request handling logic.
    - app/Models contains all the model classes and is where we specify all fillable fields.
    - app/Providers contains classes which bootstrap the entire application.
- ./routes/api.php is where we concisely define all of our routes (endpoints).
- ./tests/Feature contains all the integration tests for the app.
- ./config/database.php allows you to configure your database connection settings. 
- ./database is another very important folder.
    - ./database/factories contains the factory classes which are used to seed the databases.
    - ./database/migrations allows us to modify our data model
    - ./databse/seeders contains classes that seed the database via factories.
<br>



## Running the project in your local environment
First, ensure you have PHP and Composer installed on your machine.
>Minimum version requiremenets: *PHP ^8.1*; *Composer 2.5.4*.

1) Fork and clone the repository.
2) cd into the repository and run these CLI commands:

        composer update
        composer install

3) Rename your ```.env.example``` file to ```.env```, remove the variables for the default mysql connection, and ensure you add the following 3 variables:

        DB_CONNECTION=sqlite
        DB_FOREIGN_KEYS=true
        USE_SQLITE_SYNTAX=like
        DB_DATABASE= this needs to be the absolute path to the sqlite database located in ./database/database.sqlite, e.g. /home/username/mydocuments/laravel-api/database/database.sqlite*


4) To spin up the local development server, run the Artisan CLI command:
        
        php artisan serve

5) To run the test suit, run the Artisan CLI command:

        php artisan test

<br>
