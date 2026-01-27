# Todo API Documentation

## Overview
A complete RESTful API for managing todos with authentication using Laravel Sanctum.

## Base URL
```
http://localhost:8000/api
```

## Authentication
The API uses token-based authentication via Laravel Sanctum. Include the token in the Authorization header:
```
Authorization: Bearer {your_token}
```

## Endpoints

### Authentication

#### Register
- **POST** `/auth/register`
- **Headers**: `Content-Type: application/json`
- **Body**:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```
- **Response** (201):
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-01-27 10:00:00",
        "updated_at": "2025-01-27 10:00:00"
    },
    "token": "1|abc123..."
}
```

#### Login
- **POST** `/auth/login`
- **Headers**: `Content-Type: application/json`
- **Body**:
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```
- **Response** (200):
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-01-27 10:00:00",
        "updated_at": "2025-01-27 10:00:00"
    },
    "token": "1|abc123..."
}
```

#### Logout
- **POST** `/auth/logout`
- **Headers**: `Authorization: Bearer {token}`, `Content-Type: application/json`
- **Response** (200):
```json
{
    "message": "Logged out successfully"
}
```

#### Get Profile
- **GET** `/auth/profile`
- **Headers**: `Authorization: Bearer {token}`
- **Response** (200):
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-01-27 10:00:00",
        "updated_at": "2025-01-27 10:00:00"
    }
}
```

#### Refresh Token
- **POST** `/auth/refresh`
- **Headers**: `Authorization: Bearer {token}`, `Content-Type: application/json`
- **Response** (200):
```json
{
    "message": "Token refreshed successfully",
    "token": "2|def456..."
}
```

### Todos

#### Get All Todos
- **GET** `/todos`
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `status`: Filter by status (`completed`, `pending`)
  - `priority`: Filter by priority (`low`, `medium`, `high`)
  - `search`: Search in title and description
  - `due_date_from`: Filter todos from this date (YYYY-MM-DD)
  - `due_date_to`: Filter todos until this date (YYYY-MM-DD)
- **Response** (200):
```json
{
    "todos": {
        "data": [
            {
                "id": 1,
                "title": "Complete project",
                "description": "Finish the todo API project",
                "completed": false,
                "priority": "high",
                "due_date": "2025-01-30 23:59:59",
                "created_at": "2025-01-27 10:00:00",
                "updated_at": "2025-01-27 10:00:00",
                "is_overdue": false,
                "is_due_soon": true
            }
        ],
        "current_page": 1,
        "total": 1
    }
}
```

#### Create Todo
- **POST** `/todos`
- **Headers**: `Authorization: Bearer {token}`, `Content-Type: application/json`
- **Body**:
```json
{
    "title": "New task",
    "description": "Task description",
    "priority": "medium",
    "due_date": "2025-01-30 15:00:00"
}
```
- **Response** (201):
```json
{
    "message": "Todo created successfully",
    "todo": {
        "id": 2,
        "title": "New task",
        "description": "Task description",
        "completed": false,
        "priority": "medium",
        "due_date": "2025-01-30 15:00:00",
        "created_at": "2025-01-27 10:30:00",
        "updated_at": "2025-01-27 10:30:00",
        "is_overdue": false,
        "is_due_soon": true
    }
}
```

#### Get Single Todo
- **GET** `/todos/{id}`
- **Headers**: `Authorization: Bearer {token}`
- **Response** (200):
```json
{
    "todo": {
        "id": 1,
        "title": "Complete project",
        "description": "Finish the todo API project",
        "completed": false,
        "priority": "high",
        "due_date": "2025-01-30 23:59:59",
        "created_at": "2025-01-27 10:00:00",
        "updated_at": "2025-01-27 10:00:00",
        "is_overdue": false,
        "is_due_soon": true
    }
}
```

#### Update Todo
- **PUT** `/todos/{id}`
- **Headers**: `Authorization: Bearer {token}`, `Content-Type: application/json`
- **Body**:
```json
{
    "title": "Updated task",
    "description": "Updated description",
    "priority": "low",
    "completed": true
}
```
- **Response** (200):
```json
{
    "message": "Todo updated successfully",
    "todo": {
        "id": 1,
        "title": "Updated task",
        "description": "Updated description",
        "completed": true,
        "priority": "low",
        "due_date": "2025-01-30 23:59:59",
        "created_at": "2025-01-27 10:00:00",
        "updated_at": "2025-01-27 11:00:00",
        "is_overdue": false,
        "is_due_soon": false
    }
}
```

#### Toggle Complete Status
- **PATCH** `/todos/{id}/toggle`
- **Headers**: `Authorization: Bearer {token}`
- **Response** (200):
```json
{
    "message": "Todo status updated successfully",
    "todo": {
        "id": 1,
        "title": "Complete project",
        "description": "Finish the todo API project",
        "completed": true,
        "priority": "high",
        "due_date": "2025-01-30 23:59:59",
        "created_at": "2025-01-27 10:00:00",
        "updated_at": "2025-01-27 11:00:00",
        "is_overdue": false,
        "is_due_soon": false
    }
}
```

#### Delete Todo
- **DELETE** `/todos/{id}`
- **Headers**: `Authorization: Bearer {token}`
- **Response** (200):
```json
{
    "message": "Todo deleted successfully"
}
```

#### Get Statistics
- **GET** `/todos/statistics`
- **Headers**: `Authorization: Bearer {token}`
- **Response** (200):
```json
{
    "statistics": {
        "total": 10,
        "completed": 3,
        "pending": 7,
        "high_priority": 4,
        "overdue": 2,
        "due_soon": 3
    }
}
```

## Error Responses

### Validation Error (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email field is required."
        ]
    }
}
```

### Unauthorized (401)
```json
{
    "message": "Invalid credentials"
}
```

### Forbidden (403)
```json
{
    "message": "Unauthorized"
}
```

### Not Found (404)
```json
{
    "message": "No query results for model [App\\Models\\Todo]"
}
```

## Setup Instructions

1. Install dependencies:
```bash
composer install
```

2. Run migrations:
```bash
php artisan migrate
```

3. Start the development server:
```bash
php artisan serve
```

4. Test the API using Postman or any API client.