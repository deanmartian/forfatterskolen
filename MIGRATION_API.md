# Lovable API v1 (Laravel)

This document is copy/paste-ready for the `/api/v1` endpoints used by Lovable.

## Base URLs
```
https://<your-domain>/api/v1
```

## Authentication
All protected endpoints require a Bearer token.
```
Authorization: Bearer <access_token>
```

### JWT/Refresh flow
1. `POST /auth/login` with email + password.
2. Use the returned `access_token` for protected routes.
3. When the access token expires, call `POST /auth/refresh` with the refresh token.
4. (Optional) call `POST /auth/logout` to revoke the refresh token.

### Token lifetimes
- **Access token**: 15 minutes.
- **Refresh token**: 14 days.

## Error format
All errors use the same envelope:
```json
{
  "error": {
    "message": "Human readable message",
    "code": "unauthorized | forbidden | not_found | validation_error",
    "details": {
      "field": [
        "Validation error message"
      ]
    }
  }
}
```

`details` is only present for validation errors.

## Status codes
- **200**: Success
- **401**: Missing/invalid credentials or expired tokens
- **403**: Authenticated but not allowed (cross-user access blocked)
- **404**: Resource not found
- **422**: Validation error

## Date/time format
All date/time fields use ISO 8601 (UTC) format, e.g. `2024-01-02T10:45:00Z`.

## CORS
Allowed origins:
- `https://www.forfatterskolen.no/`

The API supports `OPTIONS` preflight requests and allows the `Authorization` header.

---

# Auth

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

**Errors**
- **401** `unauthorized` (invalid credentials)
- **403** `forbidden` (inactive user)
- **422** `validation_error`

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

**Errors**
- **401** `unauthorized` (invalid/expired refresh token)
- **403** `forbidden` (inactive user)
- **422** `validation_error`

**Notes**
- If the refresh token is already revoked, the response is **401** `unauthorized`.

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

**Errors**
- **401** `unauthorized` (invalid/expired refresh token)
- **422** `validation_error`

**Notes**
- Logout is idempotent. If the refresh token is already revoked, the response is still **200** with `"revoked": true`.

---

# Profile

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

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

---

# Dashboard

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
      "started_at": "2024-01-02T10:45:00Z",
      "start_date": "2024-01-02",
      "end_date": "2025-01-02",
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

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

---

# Courses

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
      "started_at": "2024-01-02T10:45:00Z",
      "start_date": "2024-01-02",
      "end_date": "2025-01-02",
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

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

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
      "created_at": "2024-02-01T09:15:00Z",
      "updated_at": "2024-02-01T09:15:00Z"
    }
  ]
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden` (course not owned by user)
- **404** `not_found` (course not found)

---

# Lessons

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
    "created_at": "2024-02-01T09:15:00Z",
    "updated_at": "2024-02-01T09:15:00Z"
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden` (lesson not owned by user)
- **404** `not_found` (lesson not found)

---

# Files (Upload + Download)

File uploads are a two-step flow:
1) Request a signed upload instruction.
2) Upload the file using the provided URL.

Downloads are also signed and time-limited.

The signed upload URL is a time-limited API endpoint (not an S3 presign). Both the `signature` query parameter and the `Authorization` header are required.

### Constraints
- `filename` must be a plain file name (no `/` or `\`) and <= 255 chars.
- `mime_type` is required.
- `size` is required (bytes) and must be <= 25 MB (26214400 bytes).
- Access is restricted to the file owner or admins. Cross-user access returns **403**.

## POST /files/signed-upload

**Request**
```http
POST /api/v1/files/signed-upload
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "filename": "notes.pdf",
  "mime_type": "application/pdf",
  "size": 123456
}
```

**Response (200)**
```json
{
  "file_id": 101,
  "upload": {
    "method": "POST",
    "url": "https://<your-domain>/api/v1/files/101/upload?expires=...&signature=...",
    "headers": {
      "Authorization": "Bearer <access_token>"
    },
    "expires_in": 600
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **422** `validation_error`

## POST /files/{id}/upload

Upload the file to the signed URL. This endpoint expects a multipart payload with a `file` field.
Both the `signature` query parameter and `Authorization` header are required.

**Request**
```bash
curl -X POST "https://<your-domain>/api/v1/files/101/upload?expires=...&signature=..." \
  -H "Authorization: Bearer <access_token>" \
  -F "file=@./notes.pdf"
```

**Response (200)**
```json
{
  "uploaded": true,
  "file_id": 101
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **422** `validation_error`

## GET /files/{id}/signed-download

**Request**
```http
GET /api/v1/files/101/signed-download
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "file_id": 101,
  "download_url": "https://<your-domain>/api/v1/files/101/download?expires=...&signature=...",
  "expires_in": 600
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`

## GET /files/{id}/download

Use the signed URL to retrieve the file:
```bash
curl -L "https://<your-domain>/api/v1/files/101/download?expires=...&signature=..." -o notes.pdf
```

**Errors**
- **404** `not_found`

---

# Quick test steps

## Login + access a protected route
```bash
curl -s -X POST "https://<your-domain>/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"secret"}'
```

Copy the `access_token` and call:
```bash
curl -s "https://<your-domain>/api/v1/me" \
  -H "Authorization: Bearer <access_token>"
```

## Refresh access token
```bash
curl -s -X POST "https://<your-domain>/api/v1/auth/refresh" \
  -H "Content-Type: application/json" \
  -d '{"refresh_token":"<refresh_token>"}'
```

## Signed upload flow
```bash
curl -s -X POST "https://<your-domain>/api/v1/files/signed-upload" \
  -H "Authorization: Bearer <access_token>" \
  -H "Content-Type: application/json" \
  -d '{"filename":"notes.pdf","mime_type":"application/pdf","size":123456}'
```

Then upload using the returned `upload.url`:
```bash
curl -X POST "<upload.url>" \
  -H "Authorization: Bearer <access_token>" \
  -F "file=@./notes.pdf"
```

## Signed download flow
```bash
curl -s "https://<your-domain>/api/v1/files/101/signed-download" \
  -H "Authorization: Bearer <access_token>"
```

Then download using the returned `download_url`:
```bash
curl -L "<download_url>" -o notes.pdf
```
