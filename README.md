# Advanced Task Management API

This project is an advanced API for task management that includes features such as managing different types of tasks, task dependencies, and daily reporting. It also incorporates advanced security measures, including JWT authentication.

## Features

- **Task Management**: Handle various types of tasks: Bug, Feature, Improvement.
- **Task Dependencies**: Manage dependencies between tasks.
- **Real-Time Notifications**: Receive updates on task status.
- **Daily Reporting**: Generate daily reports on tasks.
- **Advanced Security**: JWT authentication, CSRF protection, XSS, and SQL Injection prevention.
- **Role and Permission Management**: Control user roles and permissions.

## Requirements

- PHP 8.0 or higher
- Laravel 9.x
- Composer
- MySQL or any database supported by Laravel

## Installation

1. Clone the repository:
 
   git clone https://github.com/GhofranWarrakia/Api-Task_Mangement
   cd repository-name

2. Install the required packages:
composer install 

3. Set up the environment file:
cp .env.example .env

4. Configure the database in the .env file:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
 
5. Run the migrations:

php artisan migrate

6. Generate the application key:

php artisan key:generate

7. Run the Laravel development server:

php artisan serve

### API Endpoints

Tasks
Create Task: POST /api/tasks
Update Task Status: PUT /api/tasks/{id}/status
Reassign Task: PUT /api/tasks/{id}/reassign
Add Comment to Task: POST /api/tasks/{id}/comments
Add Attachment to Task: POST /api/tasks/{id}/attachments
View Task Details: GET /api/tasks/{id}
List All Tasks with Advanced Filters: GET /api/tasks
Assign Task to User: POST /api/tasks/{id}/assign
Generate Daily Task Report: GET /api/reports/daily-tasks
View Blocked Tasks: GET /api/tasks?status=Blocked


### Security and Protection

JWT Authentication: Authenticate using JSON Web Tokens.
Rate Limiting: Protect the API from DDoS attacks.
CSRF Protection: Safeguard the API against CSRF attacks.
XSS and SQL Injection Protection: Utilize Laravel’s built-in protection.

### Usage
You can use tools like Postman to test the API endpoints. Make sure to include the Authorization header with a Bearer Token to access protected routes.

### Contributing
If you would like to contribute to this project, please open an issue or submit a pull request.

### References
Laravel Documentation
Postman

### License
This project is licensed under the FOCAL X.

### Credits
Ghofran Warrakia

### Contact
For any inquiries or support, please contact:

### GitHub: https://github.com/GhofranWarrakia
### LinkedIn: GhofranWarrakia


