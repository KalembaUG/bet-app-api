# User Stories


## Core requirements
1) "As a user, I want to be able to view a list of all exams".
2) "As a user, I want the list of exams to be sorted in date order by default."
3) "As a user, I want the list of exams to be sortable by date, candidate or location."

These are fufilled by the following endpoints:
- GET /exams - returns a list of all exams sorted by date.
- GET /exams?date={date} - allows list to be filtered by date
- GET /exams?name={name} - allows list to be filtered by candidate.
- GET /exams?location={location} - allows list to be filtered by location

## Extended requirements
User:
1) "As a user, I want to be able to sign up for a new account."
2) "As a user, I want to be able to sign in to my account."

Admin: 
1) "As an admin, I want to be able to create and add new exam sessions."
2) "As an admin, I want to be able to edit the details of any exam session."
3) "As an admin, I want to be able to delete/cancel any exam session."
4) "As an admin, I want to be able to view a list of all users."
5) "As an admin, I want to be able to set a page limit on the list of exams being returned."
6) "As an admin, I want to be able to freely select different pages on the returned list of exams."
7) "As an admin, I want to be able to view a list of upcoming exams."
8) "As an admin, I want to be able to sort the list of exams in ascending as well as descending date order."

Candidate:
1) "As a candidate, I want to be able to view a list of all my own exams."
2) "As a candidate, I want to be able to view individual exams that are under my name."
3) "As a candidate, I want to be able to modify the details of my own exam session."
4) "As a candidate, I want to be able to cancel my own exam sessions."


These are fulfilled by the following endpoints:
- POST /signup - allows user to create a new account.
- POST /login - allows user to login to existing account.
- PUT /exams/{id} - allows user to 
- POST /exams - allows a new exam to be added to the database.
- DELETE /exams/{id} - allows a single exam to be deleted
- GET /exams?after={date} - filters list of exams to exclude any past dates.
- GET /exams?limit={limit}&page={page} - allows for pagination.
- GET /exams?order={order} - allows list of exams to be sorted in asc/desc date order.
