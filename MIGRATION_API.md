# Lovable API v1 JWT Auth

This document describes the additive `/api/v1` auth endpoints and expected payloads for Lovable.

## Token lifetimes
- **Access token**: 15 minutes.
- **Refresh token**: 14 days.

## Base URL
```
https://<your-domain>/api/v1
```

## Auth flow
1. `POST /auth/login` with email + password.
2. Use `access_token` in `Authorization: Bearer <token>` for protected routes.
3. When access token expires, call `POST /auth/refresh` with the refresh token.
4. (Optional) call `POST /auth/logout` to revoke the refresh token.

## Error format
All 401/403/422 errors use the same envelope:
```json
{
  "error": {
    "message": "Human readable message",
    "code": "unauthorized | forbidden | validation_error",
    "details": {
      "field": [
        "Validation error message"
      ]
    }
  }
}
```

## POST /auth/login

**Request**
```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "secret"
}
```

**Response (200)**
```json
{
  "access_token": "jwt-access-token",
  "refresh_token": "refresh-token",
  "expires_in": 900
}
```

## POST /auth/refresh

**Request**
```http
POST /api/v1/auth/refresh
Content-Type: application/json

{
  "refresh_token": "refresh-token"
}
```

**Response (200)**
```json
{
  "access_token": "new-jwt-access-token",
  "expires_in": 900
}
```

## POST /auth/logout

**Request**
```http
POST /api/v1/auth/logout
Content-Type: application/json

{
  "refresh_token": "refresh-token"
}
```

**Response (200)**
```json
{
  "revoked": true
}
```

## GET /me

**Request**
```http
GET /api/v1/me
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "id": 123,
  "name": "Ada Lovelace",
  "email": "user@example.com",
  "roles": [
    "learner"
  ]
}
```

## GET /dashboard

Returns a lightweight dashboard summary for the authenticated learner.

**Request**
```http
GET /api/v1/dashboard
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "courses_taken_total": 2,
  "courses_taken": [
    {
      "id": 551,
      "course_id": 12,
      "package_id": 88,
      "is_active": true,
      "started_at": "Jan 02, 2024 10:45 am",
      "start_date": "Jan 02, 2024",
      "end_date": "Jan 02, 2025",
      "access_lessons": [
        1,
        2,
        3
      ],
      "years": 1,
      "is_free": false,
      "course": {
        "id": 12,
        "title": "Creative Writing",
        "description": "Build your writing practice.",
        "description_simplemde": null,
        "course_image": "/images/courses/creative-writing.jpg",
        "type": "Single",
        "instructor": "Ada Lovelace",
        "start_date": "2024-01-02",
        "end_date": "2024-12-31",
        "is_free": false
      }
    }
  ]
}
```

## GET /courses/taken

Returns all courses taken for the authenticated user.

**Request**
```http
GET /api/v1/courses/taken
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": [
    {
      "id": 551,
      "course_id": 12,
      "package_id": 88,
      "is_active": true,
      "started_at": "Jan 02, 2024 10:45 am",
      "start_date": "Jan 02, 2024",
      "end_date": "Jan 02, 2025",
      "access_lessons": [
        1,
        2,
        3
      ],
      "years": 1,
      "is_free": false,
      "course": {
        "id": 12,
        "title": "Creative Writing",
        "description": "Build your writing practice.",
        "description_simplemde": null,
        "course_image": "/images/courses/creative-writing.jpg",
        "type": "Single",
        "instructor": "Ada Lovelace",
        "start_date": "2024-01-02",
        "end_date": "2024-12-31",
        "is_free": false
      }
    }
  ]
}
```

## GET /courses/{id}/lessons

Returns lessons for a course owned by the authenticated user.

**Request**
```http
GET /api/v1/courses/12/lessons
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": [
    {
      "id": 1,
      "course_id": 12,
      "title": "Lesson 1: Start Here",
      "content": "<p>Welcome to the course.</p>",
      "description": null,
      "description_simplemde": null,
      "whole_lesson_file": null,
      "delay": "0",
      "period": "days",
      "order": 1,
      "allow_lesson_download": false,
      "created_at": "Feb 01, 2024 09:15 am",
      "updated_at": "Feb 01, 2024 09:15 am"
    }
  ]
}
```

## GET /lessons/{id}

Returns a single lesson for a course owned by the authenticated user.

**Request**
```http
GET /api/v1/lessons/1
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": {
    "id": 1,
    "course_id": 12,
    "title": "Lesson 1: Start Here",
    "content": "<p>Welcome to the course.</p>",
    "description": null,
    "description_simplemde": null,
    "whole_lesson_file": null,
    "delay": "0",
    "period": "days",
    "order": 1,
    "allow_lesson_download": false,
    "created_at": "Feb 01, 2024 09:15 am",
    "updated_at": "Feb 01, 2024 09:15 am"
  }
}
```
