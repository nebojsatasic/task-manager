# Task Manager

## Project Overview

This is a secure RESTful API built with Laravel for managing projects and tasks. It provides endpoints for creating, assigning, and tracking projects and tasks, with support for user authentication, project membership, and advanced features like sorting and filtering. Designed with flexibility and maintainability in mind, the API can be easily integrated into other systems to support team collaboration and project organization.

## Features

* Users can register and log in using secure authentication.
* Authenticated users can create new projects.
* Registered users can join existing projects as members.
* Only the creator of a project is allowed to attach or detach members.
* For simplicity, the project creator is automatically added as a member upon creation.
* Both project creators and members can view the project.
* Only the project creator can update or delete a project.
* Authenticated users can view all projects they are members of.
* Project creators and members can add tasks to the project.
* Project creators and members can view all tasks within the project.
* The creator of a task is the only one who can update or delete it.
* The creator of a task is the only one who can assign or unassign a project member to/from that task.
* Users can view a list of tasks they have created.
* Each task shows whether it is finished or open.
* Each task shows the user to whom it is assigned.
* Tasks support sorting and filtering via query parameters.
* Project and task listings are paginated for better performance and usability.
* By default, tasks and members are not included in the project view — they can be included via optional query parameters.
* Authenticated users can view tasks assigned to them.

## Technologies Used

* Laravel 11 – Backend framework for API development
* SQLite – Lightweight relational database used for data storage

## Installation

Follow these steps to run the Laravel API locally:

### 1. Clone the repository

```bash
git clone https://github.com/nebojsatasic/task-manager.git
cd task-manager/src
```

### 2. Install PHP dependencies

```bash
composer install
```

If you're having issues running the application (especially due to PHP version differences), you may need to update the dependencies:

> ```bash
> composer update
> ```
> ...to re-resolve dependencies for your environment.

### 3. Set up the environment file

Copy `.env.example` to `.env`

### 4. Generate the application key

```bash
php artisan key:generate
```

### 5. Set up the SQLite database

- Create the SQLite database file (this file is not included in version control):

    ```bash
    touch database/database.sqlite
    ```

- Update your `.env` file with the correct database connection:
    ```env
    DB_CONNECTION=sqlite
    ```

### 6. Run database migrations

```bash
php artisan migrate
```

### 7. Start the local development server

```bash
php artisan serve
```

Access the API at: `http://127.0.0.1:8000`

## PHP Version Compatibility

This project uses **Laravel 11**, which requires **PHP 8.2 or higher**.

- The app was originally developed with **PHP 8.2** and **Laravel 11**.0
- It has been updated to **Laravel 11.45**, tested and deployed on **PHP 8.4.3**
- **Laravel 11.45** remains compatible with **PHP 8.2**, as long as dependencies do

## Endpoints

Base URL: `https://task-mng.nebojsatasic.com/api`

### Public Routes

These routes are accessible without authentication. No token or authorization header is required.

#### `POST /register`

Registers a new user and retrieves a token.

**Required Fields:**
- `name` – string, required
- `email` – string (valid email format), required, unique
- `job_title` – string, required
- `password` – string (min 6 characters), required
- `password_confirmation` – string (must match password), required

**Example Request:**  
`POST https://task-mng.nebojsatasic.com/api/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@gmail.com",
  "job_title": "Software Engineer",
  "password": "secret",
  "password_confirmation": "secret"
}
```

**Response:**
```json
{
  "user": {
    "name": "John Doe",
    "email": "john@gmail.com",
    "job_title": "Software Engineer",
    "updated_at": "2025-05-19T13:22:41.000000Z",
    "created_at": "2025-05-19T13:22:41.000000Z",
    "id": 1
  },
  "access-token": "access_token_here",
  "token-type": "bearer token"
}
```

#### `POST /login`

Authenticates a user and returns an access token for future requests.

**Required Fields:**
- `email` – string (valid email format), required
- `password` – string (min 6 characters), required

**Example Request:**  
`POST https://task-mng.nebojsatasic.com/api/login`

**Request Body:**
```json
{
  "email": "john@gmail.com",
  "password": "secret"
}
```

**Response:**
```json
{
  "access-token": "access_token_here",
  "token-type": "bearer token"
}
```

### Authenticated Routes

All routes below require a valid Bearer token in the `Authorization` header.

**Example Header:**  
`Authorization: Bearer your_access_token_here`

#### `GET /user`

Retrieves the authenticated user's data.

**Example Request:**  
`GET https://task-mng.nebojsatasic.com/api/user`

**Response:**
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@gmail.com",
    "job_title": "Software Engineer"
  }
}
```

#### `POST /logout`

Logs out the authenticated user by revoking the current access token.

**Example Request:**  
`POST https://task-mng.nebojsatasic.com/api/logout`

**Response:**
```json
{
  "message": "Logged out"
}
```

#### `GET /projects`

Retrieves a list of all projects the currently authenticated user is a member of.

Optionally includes related `tasks`, `members`, or both using the `include` query parameter.

**Query Parameters:**
- `include=tasks` – include related tasks
- `include=members` – include project members
- `include=tasks,members` – include both tasks and members

**Example Request:**  
`GET https://task-mng.nebojsatasic.com/api/projects?include=tasks,members`

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Company Website Redesign",
      "description": "Redesign the company website to improve UX, update branding, and optimize for performance and SEO.",
      "creator": {
        "id": 1,
        "name": "John Doe",
        "email": "john@gmail.com",
        "job_title": "Software Engineer"
      },
      "creation-date": "2025-05-19",
      "tasks": [
        {
          "id": 1,
          "title": "Plan New Layout",
          "description": "Sketch new homepage and contact page.",
          "project_title": "Company Website Redesign",
          "assigned_to": {
            "id": 2,
            "name": "Alice Johnson",
            "email": "alice@gmail.com",
            "job_title": "Web Designer"
          },
          "status": "finished",
          "creation-date": "2025-05-19"
        },
        // More tasks
      ],
      "members": [
        {
          "id": 1,
          "name": "John Doe",
          "email": "john@gmail.com",
          "job_title": "Software Engineer"
        },
        // more members
      ]
    },
    // more projects
  ],
  "links": {
    "first": "http://localhost:8000/api/projects?page=1",
    // more links
  },
  "meta": {
    "current_page": 1,
    // more meta data
  }
}
```

#### `GET /projects/{project}`

Retrieves a specific project by its ID.

Access is restricted to users who are members of the project.

The response always includes the project's tasks and members.

**Path Parameters:**
- `{project}` – required, numeric ID of the project

**Example Request:**  
`GET https://task-mng.nebojsatasic.com/api/projects/1`

**Response:**
```json
{
  "data": {
    "id": 1,
    "title": "Company Website Redesign",
    "description": "Redesign the company website to improve UX, update branding, and optimize for performance and SEO.",
    "creator": {
      "id": 1,
      "name": "John Doe",
      "email": "john@gmail.com",
      "job_title": "Software Engineer"
    },
    "creation-date": "2025-05-19",
    "tasks": [
      {
        "id": 1,
        "title": "Plan New Layout",
        "description": "Sketch new homepage and contact page.",
        "project_title": "Company Website Redesign",
        "assigned_to": {
          "id": 2,
          "name": "Alice Johnson",
          "email": "alice@gmail.com",
          "job_title": "Web Designer"
        },
        "status": "finished",
        "creation-date": "2025-05-19"
      },
      {
        "id": 2,
        "title": "Update Text Content",
        "description": "Write new About and Services page content.",
        "project_title": "Company Website Redesign",
        "assigned_to": {
          "id": 3,
          "name": "Bob Smith",
          "email": "bob@gmail.com",
          "job_title": "Content Writer"
        },
        "status": "open",
        "creation-date": "2025-05-20"
      },
      {
        "id": 3,
        "title": "Build New Pages",
        "description": "Code homepage and contact page with new layout.",
        "project_title": "Company Website Redesign",
        "assigned_to": {
          "id": 4,
          "name": "Carla Reyes",
          "email": "carla@gmail.com",
          "job_title": "Frontend Developer"
        },
        "status": "open",
        "creation-date": "2025-05-20"
      }
    ],
    "members": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@gmail.com",
        "job_title": "Software Engineer"
      },
      {
        "id": 2,
        "name": "Alice Johnson",
        "email": "alice@gmail.com",
        "job_title": "Web Designer"
      },
      {
        "id": 3,
        "name": "Bob Smith",
        "email": "bob@gmail.com",
        "job_title": "Content Writer"
      },
      {
        "id": 4,
        "name": "Carla Reyes",
        "email": "carla@gmail.com",
        "job_title": "Frontend Developer"
      }
    ]
  }
}
```

#### `POST /projects`

Creates a new project.

**Fields:**
- `title` – string (max 255 characters), **required**
- `description` – text, optional

**Example Request:**  
`POST https://task-mng.nebojsatasic.com/api/projects`

**Request Body:**
```json
{
  "title": "Website Redesign",
  "description": "Redesign the company website to improve UX, update branding, and optimize for performance and SEO."
}
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "title": "Website Redesign",
    "description": "Redesign the company website to improve UX, update branding, and optimize for performance and SEO.",
    "creator": {
      "id": 1,
      "name": "John Doe",
      "email": "john@gmail.com",
      "job_title": "Software Engineer"
    },
    "creation-date": "2025-05-19"
  }
}
```

#### `PUT /projects/{project}` or `PATCH /projects/{project}`

Updates a specific project by its ID.

Only the creator of a project can update it.

**Path Parameters:**
- `{project}` – required, numeric ID of the project

**Optional Fields:**
- `title` – string (max 255 characters)
- `description` – text

**Example Request:**  
`PATCH https://task-mng.nebojsatasic.com/api/projects/1`

**Request Body:**
```json
{
  "title": "Company Website Redesign"
}
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "title": "Company Website Redesign",
    "description": "Redesign the company website to improve UX, update branding, and optimize for performance and SEO.",
    "creator": {
      "id": 1,
      "name": "John Doe",
      "email": "john@gmail.com",
      "job_title": "Software Engineer"
    },
    "creation-date": "2025-05-19"
  }
}
```

#### `DELETE /projects/{project}`

Deletes a specific project by its ID.

Only the creator of a project can delete it.

**Path Parameters:**
- `{project}` – required, numeric ID of the project

**Example Request:**  
`DELETE https://task-mng.nebojsatasic.com/api/projects/1`

**Response:**
- Status: `204 No Content`
- Time: `984 ms`
- Size: `0 B`

#### `POST /projects/{project}/members/{user}`

Attach a user as a member to the given project.

Only the creator of the project is authorized to perform this action.

If the user is already a member of the project, no duplicate entries will be made in the database.

**Path Parameters:**
- `{project}` – required, numeric ID of the project
- `{user}` – required, numeric ID of the user

**Example Request:**  
`POST https://task-mng.nebojsatasic.com/api/projects/1/members/2`

**Response:**
```json
{
  "message": "Member attached successfully."
}
```

#### `DELETE /projects/{project}/members/{user}`

Detach a user from the given project.

Only the creator of the project is authorized to perform this action.

If the user is not a member of the project, no action will be performed.

**Path Parameters:**
- `{project}` – required, numeric ID of the project
- `{user}` – required, numeric ID of the user

**Example Request:**  
`DELETE https://task-mng.nebojsatasic.com/api/projects/1/members/2`

**Response:**
```json
{
  "message": "Member detached successfully."
}
```

#### `GET /tasks`

Retrieves a list of all tasks of projects that the authenticated user is a member of.

**Example Request:**  
`GET https://task-mng.nebojsatasic.com/api/tasks`

**Response:**
```json
{
  "data": [
    {
      "id": 5,
      "title": "Write Posts for Social Networks",
      "description": "Create short, engaging text content for Facebook, Instagram, and LinkedIn to promote the campaign.",
      "project_title": "Company Marketing Campaign",
      "assigned_to": {
        "id": 3,
        "name": "Bob Smith",
        "email": "bob@gmail.com",
        "job_title": "Content Writer"
      },
      "status": "open",
      "creation-date": "2025-05-21"
    },
    // more tasks
    {
      "id": 1,
      "title": "Plan New Layout",
      "description": "Sketch new homepage and contact page.",
      "project_title": "Company Website Redesign",
      "assigned_to": {
        "id": 2,
        "name": "Alice Johnson",
        "email": "alice@gmail.com",
        "job_title": "Web Designer"
      },
      "status": "finished",
      "creation-date": "2025-05-19"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/tasks?page=1",
    // more links
  },
  "meta": {
    "current_page": 1,
    // more meta data
    "total": 5
  }
}
```

##### Sorting

By default, tasks are sorted by `created_at` (newest first).

You can override this using the `sort` query parameter. Multiple fields can be combined, separated by commas.

**Allowed sort fields:**
- `title`
- `is_done`
- `created_at`

**Prefix a field with `-` to sort in descending order.**

**Examples:**
- `GET /tasks?sort=title` – Sort by title (ascending)
- `GET /tasks?sort=-created_at` – Sort by creation date (descending)
- `GET /tasks?sort=title,created_at` – Sort by title, then by creation date
- `GET /tasks?sort=-title,-created_at` – Sort by title and creation date (both descending)

##### Filtering

You can filter tasks by their `is_done` status using the `filter[is_done]` query parameter.

**Accepted values:** `true`, `1`, `false`, `0` (as strings)

**Examples:**
- `GET /tasks?filter[is_done]=true` – Returns only **completed** tasks
- `GET /tasks?filter[is_done]=false` – Returns only **incomplete** tasks

**👉 Note:** You can combine filtering and sorting.

Example:  
`GET /tasks?filter[is_done]=true&sort=title`

#### `GET /tasks/{task}`

Retrieves a specific task by its ID.

Access is restricted to users who are members of the project to which the task belongs.

**Path Parameters:**
- `{task}` – required, numeric ID of the task

**Example Request:**  
`GET https://task-mng.nebojsatasic.com/api/tasks/1`

**Response:**
```json
{
  "data": {
    "id": 1,
    "title": "Plan New Layout",
    "description": "Sketch new homepage and contact page.",
    "project_title": "Company Website Redesign",
    "assigned_to": {
      "id": 2,
      "name": "Alice Johnson",
      "email": "alice@gmail.com",
      "job_title": "Web Designer"
    },
    "status": "finished",
    "creation-date": "2025-05-19"
  }
}
```

#### `POST /tasks`

Creates a new task.

Tasks belong to a specific project. Only members of a project are allowed to create tasks within that project.

To create a task, the authenticated user must be a member of the project specified in the request body.

**Fields:**
- `title` – string (max 255 characters), **required**
- `description` – text, optional
- `is_done` – boolean, optional
- `project_id` – integer, optional

**Example Request:**  
`POST https://task-mng.nebojsatasic.com/api/tasks`

**Request Body:**
```json
{
  "title": "Plan New Layout",
  "description": "Sketch new homepage and contact page.",
  "project_id": 1
}
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "title": "Plan New Layout",
    "description": "Sketch new homepage and contact page.",
    "project_title": "Company Website Redesign",
    "assigned_to": null,
    "status": "open",
    "creation-date": "2025-05-19"
  }
}
```

#### `PUT /tasks/{task}` or `PATCH /tasks/{task}`

Updates a specific task by its ID.

Only the creator of a task can update it.

**Path Parameters:**
- `{task}` – required, numeric ID of the task

**Optional Fields:**
- `title` – string (max 255 characters)
- `description` – text
- `is_done` – boolean
- `project_id` – integer

**Example Request:**  
`PATCH https://task-mng.nebojsatasic.com/api/task/1`

**Request Body:**
```json
{
  "is_done": true
}
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "title": "Plan New Layout",
    "description": "Sketch new homepage and contact page.",
    "project_title": "Company Website Redesign",
    "assigned_to": {
      "id": 2,
      "name": "Alice Johnson",
      "email": "alice@gmail.com",
      "job_title": "Web Designer"
    },
    "status": "finished",
    "creation-date": "2025-05-19"
  }
}
```

#### `DELETE /task/{task}`

Deletes a specific task by its ID.

Only the creator of a task can delete it.

**Path Parameters:**
- `{task}` – required, numeric ID of the task

**Example Request:**  
`DELETE https://task-mng.nebojsatasic.com/api/tasks/1`

**Response:**
- Status: `204 No Content`
- Time: `689 ms`
- Size: `0 B`

#### `GET /me/tasks`

Retrieves all tasks created by the authenticated user.

**Example Request:**  
`GET https://task-mng.nebojsatasic.com/api/me/tasks`

**Response:**
```json
{
  "data": [
    {
      "id": 4,
      "title": "Create Campaign Strategy",
      "description": "Plan goals, target audience, and platforms.",
      "project_title": "Company Marketing Campaign",
      "assigned_to": {
        "id": 5,
        "name": "Emma Clark",
        "email": "emma@gmail.com",
        "job_title": "Marketing Manager"
      },
      "status": "finished",
      "creation-date": "2025-05-21"
    },
    {
      "id": 5,
      "title": "Write Posts for Social Networks",
      "description": "Create short, engaging text content for Facebook, Instagram, and LinkedIn to promote the campaign.",
      "project_title": "Company Marketing Campaign",
      "assigned_to": {
        "id": 3,
        "name": "Bob Smith",
        "email": "bob@gmail.com",
        "job_title": "Content Writer"
      },
      "status": "open",
      "creation-date": "2025-05-21"
    }
  ]
}
```

#### `PATCH /tasks/{task}/assign/{user}`

Assigns the project member to the task.

Each task can be assigned to only one member; assigning multiple users is not supported.

Only the creator of the task is authorized to perform this action.

**Path Parameters:**
- `{task}` – required, numeric ID of the task
- `{user}` – required, numeric ID of the user

**Example Request:**  
`PATCH https://task-mng.nebojsatasic.com/api/tasks/1/assign/2`

**Response:**
```json
{
  "data": {
    "id": 1,
    "title": "Plan New Layout",
    "description": "Sketch new homepage and contact page.",
    "project_title": "Company Website Redesign",
    "assigned_to": {
      "id": 2,
      "name": "Alice Johnson",
      "email": "alice@gmail.com",
      "job_title": "Web Designer"
    },
    "status": "open",
    "creation-date": "2025-05-19"
  }
}
```

#### `PATCH /tasks/{task}/unassign`

Unassign the project member from the task.

Only the creator of the project is authorized to perform this action.

**Path Parameters:**
- `{task}` – required, numeric ID of the task

**Example Request:**  
`PATCH https://task-mng.nebojsatasic.com/api/tasks/1/unassign`

**Response:**
```json
{
  "data": {
    "id": 1,
    "title": "Plan New Layout",
    "description": "Sketch new homepage and contact page.",
    "project_title": "Company Website Redesign",
    "assigned_to": null,
    "status": "open",
    "creation-date": "2025-05-19"
  }
}
```

#### `GET /me/tasks/assigned`

Retrieves all tasks assigned to the authenticated user.

**Example Request:**  
`GET https://task-mng.nebojsatasic.com/api/me/tasks/assigned`

**Response:**
```json
{
  "data": [
    {
      "id": 2,
      "title": "Update Text Content",
      "description": "Write new About and Services page content.",
      "project_title": "Company Website Redesign",
      "assigned_to": {
        "id": 3,
        "name": "Bob Smith",
        "email": "bob@gmail.com",
        "job_title": "Content Writer"
      },
      "status": "open",
      "creation-date": "2025-05-20"
    },
    {
      "id": 5,
      "title": "Write Posts for Social Networks",
      "description": "Create short, engaging text content for Facebook, Instagram, and LinkedIn to promote the campaign.",
      "project_title": "Company Marketing Campaign",
      "assigned_to": {
        "id": 3,
        "name": "Bob Smith",
        "email": "bob@gmail.com",
        "job_title": "Content Writer"
      },
      "status": "open",
      "creation-date": "2025-05-21"
    }
  ]
}
```

## Deployment & CI/CD

This project includes a Jenkins pipeline for automated deployment.

### Highlights:

- Pulls from GitHub `main` branch
- Copies .env and SQLite DB from a secure location on the server
- Installs Laravel with `--no-dev` and optimized autoloader
- Sets permissions and ownership for Laravel's writable directories
- Runs `php artisan config:cache` and `route:cache`
- `php artisan view:cache` is commented out — not needed in API-only apps

> See [`Jenkinsfile`](./Jenkinsfile) for details.

## License

No license

## Author

Name: Nebojsa Tasic

Email: [nele.tasic@gmail.com](mailto:nele.tasic@gmail.com)

Website: [https://nebojsatasic.com](https://nebojsatasic.com)

Feel free to reach out if you need login details or any other information.
