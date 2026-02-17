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
    "code": "unauthorized | forbidden | not_found | validation_error | invalid_request",
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

# Health

## GET /health

Returns API health info.

**Request**
```http
GET /api/v1/health
```

**Response (200)**
```json
{
  "ok": true,
  "env": "production",
  "time": "2024-01-02T10:45:00Z"
}
```

---

# Profile

## GET /profile

**Request**
```http
GET /api/v1/profile
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "first_name": "Ada",
  "last_name": "Lovelace",
  "email": "user@example.com",
  "phone": "+47 999 99 999",
  "address": {
    "street": "Examplegata 1",
    "postal_code": "0123",
    "city": "Oslo"
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

## PUT /profile

Updates contact fields for the authenticated user. Only `phone` and the `address` subfields are writable. `email` is read-only.

**Request (JSON)**
```http
PUT /api/v1/profile
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "phone": "+47 999 99 999",
  "address": {
    "street": "Examplegata 1",
    "postal_code": "0123",
    "city": "Oslo"
  }
}
```

**Request (multipart/form-data)**
```http
PUT /api/v1/profile
Authorization: Bearer <access_token>
Content-Type: multipart/form-data

phone=+47 999 99 999
address[street]=Examplegata 1
address[postal_code]=0123
address[city]=Oslo
```

**Response (200)**
```json
{
  "first_name": "Ada",
  "last_name": "Lovelace",
  "email": "user@example.com",
  "phone": "+47 999 99 999",
  "address": {
    "street": "Examplegata 1",
    "postal_code": "0123",
    "city": "Oslo"
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **422** `validation_error`

---

# Dashboard

## GET /dashboard

Returns a lightweight dashboard summary for the authenticated learner.

**Request**
```http
GET /api/v1/dashboard
Authorization: Bearer <access_token>
```

---

# Coaching time

## GET /learner/coaching-time

Returns coaching-time summary data for the authenticated learner.

**Request**
```http
GET /api/v1/learner/coaching-time
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "stats": {
    "booked_editors_count": 1,
    "booked_sessions_this_month": 2,
    "available_slots": 5
  },
  "next_session": {
    "id": 10,
    "editor": "Editor Name",
    "scheduled_at": "2024-06-01T09:00:00Z",
    "scheduled_at_local": "2024-06-01T11:00:00+02:00",
    "duration_minutes": 60,
    "plan_type": 1
  },
  "coaching_timers": [
    {
      "id": 1,
      "plan_type": 1,
      "plan_label": "Coaching time (1 time)",
      "help_with": "Plot structure",
      "status": 0,
      "approved_date": null,
      "suggested_date": null,
      "call_type": "video"
    }
  ],
  "booked_sessions": [
    {
      "id": 10,
      "editor": "Editor Name",
      "scheduled_at": "2024-06-01T09:00:00Z",
      "scheduled_at_local": "2024-06-01T11:00:00+02:00",
      "duration_minutes": 60,
      "plan_type": 1
    }
  ],
  "editors": [
    {
      "id": 99,
      "name": "Editor Name",
      "available_slots": 3
    }
  ],
  "links": {
    "self": "https://<your-domain>/api/v1/learner/coaching-time",
    "available": "https://<your-domain>/api/v1/learner/coaching-time/available",
    "request": "https://<your-domain>/api/v1/learner/coaching-time/request",
    "add_session": "https://<your-domain>/api/v1/learner/coaching-time/add-session"
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

---

# Calendar

## GET /calendar/events

Returns calendar events for the authenticated learner.

**Request**
```http
GET /api/v1/calendar/events
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "events": [
    {
      "id": 12,
      "title": "Lesson: Lesson 1 fra Course Title",
      "className": "event-important",
      "start": "2024-01-02",
      "end": "2024-01-03",
      "all_day": true,
      "allDay": true,
      "color": "#d95e66"
    }
  ]
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

---

# Private messages

## GET /learner/private-messages

Returns private messages for the authenticated learner.

**Request**
```http
GET /api/v1/learner/private-messages?per_page=10
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": [
    {
      "id": 12,
      "message": "<p>Remember to submit your manuscript.</p>",
      "from_user": {
        "id": 4,
        "name": "Admin User"
      },
      "created_at": "2024-01-02T10:45:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

---

# Email history

## GET /learner/email-history

Returns email history entries for the authenticated learner.

**Request**
```http
GET /api/v1/learner/email-history?per_page=10
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": [
    {
      "id": 123,
      "subject": "Welcome to Forfatterskolen",
      "from_email": "postmail@forfatterskolen.no",
      "message": "<p>Welcome!</p>",
      "parent": "courses-taken",
      "parent_id": 88,
      "recipient": {
        "name": "Ada Lovelace",
        "id": 42,
        "email": "ada@example.com"
      },
      "track_code": "abc123",
      "date_open": "2024-01-05T12:30:00Z",
      "created_at": "2024-01-05T12:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 10,
    "total": 25
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

## GET /learner/email-history/search

Searches email history entries for the authenticated learner by subject.

**Request**
```http
GET /api/v1/learner/email-history/search?subject=welcome&per_page=10
Authorization: Bearer <access_token>
```

**Required query params**
- `subject` (string): Subject keyword to search for (case-insensitive).

**Optional query params**
- `per_page` (int): Page size (max 50, default 10).

**Response (200)**
```json
{
  "data": [
    {
      "id": 123,
      "subject": "Welcome to Forfatterskolen",
      "from_email": "postmail@forfatterskolen.no",
      "message": "<p>Welcome!</p>",
      "parent": "courses-taken",
      "parent_id": 88,
      "recipient": {
        "name": "Ada Lovelace",
        "id": 42,
        "email": "ada@example.com"
      },
      "track_code": "abc123",
      "date_open": "2024-01-05T12:30:00Z",
      "created_at": "2024-01-05T12:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **422** `invalid_request` (subject query is required)

## GET /learner/coaching-time/available

Returns available editor time slots for the authenticated learner.

**Request**
```http
GET /api/v1/learner/coaching-time/available
Authorization: Bearer <access_token>
```

**Optional query params**
- `coaching_timer_id` (int): Select a specific coaching timer for availability checks.

**Response (200)**
```json
{
  "coaching_timers": [
    {
      "id": 1,
      "plan_type": 1,
      "plan_label": "Coaching time (1 time)",
      "help_with": null,
      "status": 0,
      "approved_date": null,
      "suggested_date": null,
      "call_type": null
    }
  ],
  "selected_coaching_timer": {
    "id": 1,
    "plan_type": 1,
    "plan_label": "Coaching time (1 time)",
    "help_with": null,
    "status": 0,
    "approved_date": null,
    "suggested_date": null,
    "call_type": null
  },
  "has_pending_request": false,
  "editors": [
    {
      "editor": {
        "id": 99,
        "name": "Editor Name"
      },
      "slots": [
        {
          "id": 55,
          "date": "2024-06-01",
          "start_time": "09:00:00",
          "duration": 60,
          "scheduled_at": "2024-06-01T09:00:00Z",
          "requested": false,
          "declined": false,
          "can_book": true
        }
      ]
    }
  ],
  "links": {
    "self": "https://<your-domain>/api/v1/learner/coaching-time/available",
    "back": "https://<your-domain>/api/v1/learner/coaching-time",
    "request": "https://<your-domain>/api/v1/learner/coaching-time/request"
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found` (invalid `coaching_timer_id`)

## POST /learner/coaching-time/request

Books an available editor time slot for the authenticated learner.

**Request**
```http
POST /api/v1/learner/coaching-time/request
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "coaching_timer_id": 1,
  "editor_time_slot_id": 55,
  "help_with": "Plot structure",
  "call_type": "video"
}
```

**Response (200)**
```json
{
  "message": "Time slot booked.",
  "coaching_timer": {
    "id": 1,
    "plan_type": 1,
    "plan_label": "Coaching time (1 time)",
    "help_with": "Plot structure",
    "status": 0,
    "approved_date": null,
    "suggested_date": null,
    "call_type": "video"
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **409** `slot_booked`
- **422** `invalid_slot_duration`
- **422** `slot_in_past`
- **422** `validation_error`

## POST /learner/coaching-time/add-session

Adds a coaching-time session from a course package for the authenticated learner.

**Request**
```http
POST /api/v1/learner/coaching-time/add-session
Authorization: Bearer <access_token>
Content-Type: multipart/form-data

course_taken_id=123
plan_type=1
manuscript=<optional .docx file>
```

**Response (201)**
```json
{
  "message": "Coaching Time added.",
  "coaching_timer": {
    "id": 1,
    "plan_type": 1,
    "plan_label": "Coaching time (1 time)",
    "help_with": null,
    "status": 0,
    "approved_date": null,
    "suggested_date": null,
    "call_type": null
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found` (invalid `course_taken_id`)
- **422** `validation_error`

---

# Courses (Public)

## GET /courses/for-sale

Returns active courses available for purchase.

**Request**
```http
GET /api/v1/courses/for-sale
```

**Response (200)**
```json
{
  "data": [
    {
      "id": 12,
      "title": "Creative Writing",
      "slug": "creative-writing",
      "short_description": "Build your writing practice.",
      "is_active": true,
      "start_date": "2024-01-02",
      "end_date": "2024-12-31",
      "thumbnail_url": "https://www.forfatterskolen.no/images/course.png",
      "checkout_url": "https://www.forfatterskolen.no/checkout/12"
    }
  ]
}
```

## GET /courses/{id}

Returns a public course detail payload for purchasable courses.

**Request**
```http
GET /api/v1/courses/12
```

**Response (200)**
```json
{
  "data": {
    "id": 12,
    "title": "Creative Writing",
    "slug": "creative-writing",
    "short_description": "Build your writing practice.",
    "description": "<p>Full description.</p>",
    "description_simplemde": null,
    "type": "Single",
    "instructor": "Ada Lovelace",
    "start_date": "2024-01-02",
    "end_date": "2024-12-31",
    "thumbnail_url": "https://www.forfatterskolen.no/images/course.png",
    "course_image": "/images/course.png",
    "is_active": true,
    "is_free": false,
    "checkout_url": "https://www.forfatterskolen.no/checkout/12"
  }
}
```

**Errors**
- **404** `not_found` (course not found)

# Webinars (Public)

## GET /free-webinars

Returns free/public webinar listings.

**Request**
```http
GET /api/v1/free-webinars
```

**Response (200)**
```json
{
  "data": [
    {
      "id": 44,
      "title": "Free webinar",
      "description": "Details",
      "start_date": "2024-05-10 18:00:00",
      "image_url": "https://www.forfatterskolen.no/uploads/free-webinar.png",
      "webinar_url": "https://www.forfatterskolen.no/free-webinar/44"
    }
  ]
}
```

## GET /free-webinars/{id}

Returns a free/public webinar detail.

**Request**
```http
GET /api/v1/free-webinars/44
```

**Response (200)**
```json
{
  "data": {
    "id": 44,
    "title": "Free webinar",
    "description": "Details",
    "start_date": "2024-05-10 18:00:00",
    "image_url": "https://www.forfatterskolen.no/uploads/free-webinar.png",
    "webinar_url": "https://www.forfatterskolen.no/free-webinar/44",
    "gtwebinar_id": "ABC123"
  }
}
```

**Errors**
- **404** `not_found`

# Free manuscript

## POST /free-manuscripts

Submits a free manuscript for review. Rate-limited to 5 requests/hour per IP.

**Request**
```http
POST /api/v1/free-manuscripts
Content-Type: application/json

{
  "email": "user@example.com",
  "first_name": "Ada",
  "last_name": "Lovelace",
  "genre": 1,
  "text": "Manuscript text..."
}
```

**Response (201)**
```json
{
  "id": 123
}
```

**Errors**
- **422** `validation_error` (invalid payload or duplicate email)
- **429** `too_many_requests` (rate limit exceeded)

# Publisher books

## GET /publisher-books

Returns published book listings.

**Request**
```http
GET /api/v1/publisher-books
```

**Response (200)**
```json
{
  "data": [
    {
      "id": 88,
      "title": "Book Title",
      "description": "Book description",
      "quote_description": "Quote",
      "author_image": "/images/author.png",
      "book_image": "https://www.forfatterskolen.no/images/book.png",
      "book_image_link": "https://bookstore.example/book",
      "order": 1
    }
  ]
}
```

# Courses (Learner)

## GET /courses/certificates/{id}/download

Downloads a course certificate PDF for the authenticated learner.

**Request**
```http
GET /api/v1/courses/certificates/{id}/download
Authorization: Bearer <access_token>
```

**Response (200)**

Binary PDF download with `Content-Disposition: attachment`.

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`

# Webinars

## GET /webinars

Returns the portal webinar lists for the authenticated learner.

**Request**
```http
GET /api/v1/webinars
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": {
    "upcoming": [
      {
        "id": 123,
        "course_id": 17,
        "course_title": "Webinarpakke",
        "title": "Upcoming webinar",
        "description": "Details",
        "host": "Host name",
        "start_date": "2024-05-10 18:00:00",
        "image_url": "https://www.forfatterskolen.no/uploads/webinar.png",
        "is_replay": false
      }
    ],
    "replays": [
      {
        "id": 456,
        "lesson_id": 789,
        "title": "Replay title",
        "description": "Replay description",
        "date": "2024-04-01 12:00:00",
        "content": "<iframe ...></iframe>"
      }
    ]
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

## GET|POST /learner/course-webinar

API equivalent of the legacy `learner.course-webinar` web route.

Supports the same search inputs used by the Blade page:
- `search_upcoming`: filters upcoming webinars by webinar title.
- `search_replay`: filters replay lesson content by title.

When `search_replay` is not provided, `lesson_contents` is returned as an empty list.

**Request**
```http
GET /api/v1/learner/course-webinar?search_upcoming=skriv
Authorization: Bearer <access_token>
```

```http
POST /api/v1/learner/course-webinar?search_replay=plot
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": {
    "is_replay_search": true,
    "webinars": [
      {
        "id": 123,
        "courses_taken_id": 77,
        "course_id": 3,
        "course_title": "Romanforfatterstudiet",
        "title": "Webinar title",
        "description": "Details",
        "host": "Host name",
        "start_date": "2024-05-10 18:00:00",
        "end_date": "2024-05-10 20:00:00",
        "image_url": "https://www.forfatterskolen.no/uploads/webinar.png",
        "set_as_replay": false
      }
    ],
    "lesson_contents": [
      {
        "id": 456,
        "lesson_id": 12,
        "title": "Replay title",
        "description": "Replay details",
        "date": "2024-04-01 12:00:00",
        "content": "<iframe ...></iframe>"
      }
    ]
  },
  "meta": {
    "webinars": {
      "current_page": 1,
      "last_page": 4,
      "per_page": 8,
      "total": 30
    },
    "lesson_contents": {
      "current_page": 1,
      "last_page": 2,
      "per_page": 8,
      "total": 10
    }
  }
}
```

**Errors**
- **401** `unauthorized`

## GET /courses/{id}/webinars

Returns the webinars for a specific course the learner has access to.

**Request**
```http
GET /api/v1/courses/{id}/webinars
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": {
    "upcoming": [
      {
        "id": 123,
        "course_id": 3,
        "course_title": "Course Title",
        "title": "Upcoming webinar",
        "description": "Details",
        "host": "Host name",
        "start_date": "2024-05-10 18:00:00",
        "image_url": "https://www.forfatterskolen.no/uploads/webinar.png",
        "is_replay": false
      }
    ],
    "replays": [
      {
        "id": 124,
        "course_id": 3,
        "course_title": "Course Title",
        "title": "Replay webinar",
        "description": "Details",
        "host": "Host name",
        "start_date": "2024-04-01 12:00:00",
        "image_url": "https://www.forfatterskolen.no/uploads/webinar.png",
        "is_replay": true
      }
    ]
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`

## GET /webinars/{id}

Returns a webinar detail payload.

**Request**
```http
GET /api/v1/webinars/{id}
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": {
    "id": 123,
    "course_id": 3,
    "course_title": "Course Title",
    "title": "Upcoming webinar",
    "description": "Details",
    "host": "Host name",
    "start_date": "2024-05-10 18:00:00",
    "image_url": "https://www.forfatterskolen.no/uploads/webinar.png",
    "is_replay": false,
    "is_registered": true
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`

## GET /webinars/{id}/join

Returns a join URL or replay URL based on access.

**Request**
```http
GET /api/v1/webinars/{id}/join
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": {
    "join_url": "https://event-provider.example/join",
    "replay_url": null
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`
- **422** `unprocessable_entity`

## POST /webinars/{id}/register

Registers the authenticated user for a webinar and returns a join URL.

**Request**
```http
POST /api/v1/webinars/{id}/register
Authorization: Bearer <access_token>
```

**Response (201)**
```json
{
  "data": {
    "join_url": "https://event-provider.example/join"
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`
- **422** `unprocessable_entity`

---

# Assignments

## GET /assignments

Returns assignments available to the authenticated learner.

**Request**
```http
GET /api/v1/assignments
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": [
    {
      "id": 987,
      "title": "Assignment title",
      "course_id": 12,
      "submission_date": "2024-06-01T00:00:00Z",
      "status": "open"
    }
  ]
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

## GET /assignments/{id}

Returns assignment details for the authenticated learner.

**Request**
```http
GET /api/v1/assignments/987
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": {
    "id": 987,
    "title": "Assignment title",
    "description": "Assignment details",
    "course_id": 12,
    "submission_date": "2024-06-01T00:00:00Z"
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`

## POST /assignments/{id}/submit

Submits an assignment manuscript for the authenticated learner.

**Request**
```http
POST /api/v1/assignments/987/submit
Authorization: Bearer <access_token>
Content-Type: multipart/form-data

filename=<file>
type=1
manu_type=<optional>
join_group=0
letter_to_editor=<optional file>
```

**Response (201)**
```json
{
  "data": {
    "id": 456,
    "assignment_id": 987,
    "uploaded_at": "2024-06-01T10:45:00Z",
    "word_count": 1200
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`
- **409** `submission_exists | submission_locked`
- **422** `invalid_file | invalid_file_format | invalid_file_mime | invalid_type | validation_error`

## POST /assignments/submissions/{id}/replace

Replaces an existing assignment submission for the authenticated learner.

**Request**
```http
POST /api/v1/assignments/submissions/{id}/replace
Authorization: Bearer <access_token>
Content-Type: multipart/form-data

filename: <file>
```

**Response (200)**
```json
{
  "data": {
    "id": 123,
    "assignment_id": 456,
    "word_count": 789
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`
- **422** `invalid_file | invalid_file_format | max_words_exceeded`

## GET /assignments/submissions/{id}/download

Downloads an assignment submission file.

**Request**
```http
GET /api/v1/assignments/submissions/456/download
Authorization: Bearer <access_token>
```

**Response**
- **200** file download

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`

## GET /assignments/feedback/{id}/download

Downloads assignment feedback files for the authenticated learner.

**Request**
```http
GET /api/v1/assignments/feedback/321/download
Authorization: Bearer <access_token>
```

**Response**
- **200** file download (zip or single file)

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `not_found`

---

# Shop manuscripts (Learner)

## GET /learner/shop-manuscripts

Returns shop manuscript purchases for the authenticated learner.

**Request**
```http
GET /api/v1/learner/shop-manuscripts?per_page=10
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": [
    {
      "id": 55,
      "shop_manuscript_id": 7,
      "title": "Manuscript review",
      "genre": 2,
      "description": "Looking for feedback.",
      "status": "Pending",
      "is_active": true,
      "words": 12000,
      "max_words": 15000,
      "file": "/storage/shop-manuscripts/manuscript.pdf",
      "synopsis": "/storage/shop-manuscripts-synopsis/synopsis.pdf",
      "expected_finish": "2024-06-01",
      "created_at": "2024-05-01T10:00:00Z",
      "manuscript_uploaded_date": "2024-05-02T12:00:00Z",
      "feedback_user_id": 12,
      "coaching_time_later": false
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

## GET /learner/shop-manuscripts/{id}

Returns a single shop manuscript with feedback and comments.

**Request**
```http
GET /api/v1/learner/shop-manuscripts/55
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": {
    "id": 55,
    "shop_manuscript_id": 7,
    "title": "Manuscript review",
    "genre": 2,
    "description": "Looking for feedback.",
    "status": "Pending",
    "is_active": true,
    "words": 12000,
    "max_words": 15000,
    "file": "/storage/shop-manuscripts/manuscript.pdf",
    "synopsis": "/storage/shop-manuscripts-synopsis/synopsis.pdf",
    "expected_finish": "2024-06-01",
    "created_at": "2024-05-01T10:00:00Z",
    "manuscript_uploaded_date": "2024-05-02T12:00:00Z",
    "feedback_user_id": 12,
    "coaching_time_later": false,
    "feedbacks": [
      {
        "id": 5,
        "grade": "A",
        "notes": "Great work.",
        "hours_worked": 3,
        "notes_to_head_editor": null,
        "approved": true,
        "files": [
          "/storage/shop-manuscripts-feedback/feedback.pdf"
        ],
        "created_at": "2024-05-10T10:00:00Z"
      }
    ],
    "comments": [
      {
        "id": 44,
        "comment": "Thanks for the feedback!",
        "created_at": "2024-05-11T09:00:00Z",
        "user": {
          "id": 123,
          "first_name": "Ada",
          "last_name": "Lovelace",
          "full_name": "Ada Lovelace"
        }
      }
    ]
  }
}
```

**Errors**
- **401** `unauthorized`
- **404** `not_found`

## GET /learner/shop-manuscripts/{id}/download/synopsis

Downloads only the synopsis file for a shop manuscript.

**Request**
```http
GET /api/v1/learner/shop-manuscripts/55/download/synopsis
Authorization: Bearer <access_token>
```

**Response**
- **200** file download

**Errors**
- **401** `unauthorized`
- **404** `not_found`

## GET /learner/shop-manuscripts/{id}/download/{type}

Downloads a manuscript or synopsis file for a shop manuscript.

**Request**
```http
GET /api/v1/learner/shop-manuscripts/55/download/manuscript
Authorization: Bearer <access_token>
```

`type` must be `manuscript` or `synopsis`.

**Response**
- **200** file download

**Errors**
- **401** `unauthorized`
- **404** `not_found`
- **422** `validation_error` (invalid type)

## GET /learner/shop-manuscripts/{id}/feedback/{feedbackId}/download

Downloads feedback files for a shop manuscript.

**Request**
```http
GET /api/v1/learner/shop-manuscripts/55/feedback/5/download
Authorization: Bearer <access_token>
```

**Response**
- **200** file download (zip or single file)

**Errors**
- **401** `unauthorized`
- **404** `not_found`

## POST /learner/shop-manuscripts/{id}/comments

Posts a comment on a shop manuscript thread.

**Request**
```http
POST /api/v1/learner/shop-manuscripts/55/comments
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "comment": "Thanks for the feedback!"
}
```

**Response (200)**
```json
{
  "data": {
    "id": 44,
    "comment": "Thanks for the feedback!",
    "created_at": "2024-05-11T09:00:00Z",
    "user": {
      "id": 123,
      "first_name": "Ada",
      "last_name": "Lovelace",
      "full_name": "Ada Lovelace"
    }
  }
}
```

**Errors**
- **401** `unauthorized`
- **404** `not_found`
- **422** `validation_error`

## POST /learner/shop-manuscripts/{id}/upload

Uploads a manuscript (and optional synopsis) for a shop manuscript purchase.

**Request**
```http
POST /api/v1/learner/shop-manuscripts/55/upload
Authorization: Bearer <access_token>
Content-Type: multipart/form-data

manuscript=<file>
genre=2
description=<optional>
synopsis=<optional file>
```

**Response (200)**
```json
{
  "data": {
    "id": 55,
    "shop_manuscript_id": 7,
    "title": "Manuscript review",
    "status": "Pending",
    "words": 12000
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden` (upload not allowed)
- **404** `not_found`
- **422** `validation_error`
- **422** `word_limit_exceeded`

## POST /learner/shop-manuscripts/{id}/upload-synopsis

Uploads or replaces only the synopsis file.

**Request**
```http
POST /api/v1/learner/shop-manuscripts/55/upload-synopsis
Authorization: Bearer <access_token>
Content-Type: multipart/form-data

synopsis=<file>
```

**Response (200)**
```json
{
  "data": {
    "id": 55,
    "synopsis": "/storage/shop-manuscripts-synopsis/synopsis.pdf"
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden` (upload not allowed)
- **404** `not_found`
- **422** `validation_error`

## POST /learner/shop-manuscripts/{id}/update-uploaded

Updates manuscript metadata or replaces uploaded files.

**Request**
```http
POST /api/v1/learner/shop-manuscripts/55/update-uploaded
Authorization: Bearer <access_token>
Content-Type: multipart/form-data

manuscript=<optional file>
synopsis=<optional file>
genre=<optional>
description=<optional>
coaching_time_later=<optional boolean>
```

**Response (200)**
```json
{
  "data": {
    "id": 55,
    "genre": 2,
    "description": "Updated description",
    "coaching_time_later": true
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden` (update not allowed)
- **404** `not_found`
- **422** `validation_error`
- **422** `word_limit_exceeded`

## DELETE /learner/shop-manuscripts/{id}/uploaded

Deletes the uploaded manuscript and synopsis for a shop manuscript purchase.

**Request**
```http
DELETE /api/v1/learner/shop-manuscripts/55/uploaded
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": {
    "id": 55,
    "file": null,
    "synopsis": null
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden` (delete not allowed)
- **404** `not_found`

---

# Checkout

## GET /checkout/courses/{courseId}/discount
## POST /checkout/courses/{courseId}/discount

Returns the pricing breakdown for a checkout, including any coupon discount.
Coupon codes are case-sensitive.

**Request**
```http
GET /api/v1/checkout/courses/{courseId}/discount?package_id=123&payment_plan_id=8&coupon=OPTIONAL-COUPON
Authorization: Bearer <access_token>
```

```http
POST /api/v1/checkout/courses/{courseId}/discount
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "package_id": 123,
  "payment_plan_id": 8,
  "coupon": "OPTIONAL-COUPON"
}
```

**Response (200)**
```json
{
  "course_id": 12,
  "package_id": 123,
  "payment_plan_id": 8,
  "base_price": 14900,
  "price": 13900,
  "discount": 1000,
  "coupon": "OPTIONAL-COUPON"
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden` (course not purchasable / already owned)
- **404** `not_found` (course not found)
- **422** `validation_error` (invalid coupon, plan, or payload)

## POST /checkout/courses/{courseId}/start

Starts a checkout session for a course using the existing payment flow (Vipps/Svea/etc).
Pricing is derived server-side from the selected package and payment plan.

**Request**
```http
POST /api/v1/checkout/courses/{courseId}/start
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "package_id": 123,
  "payment_mode_id": 3,
  "payment_plan_id": 8,
  "coupon": "OPTIONAL-COUPON",
  "is_pay_later": false
}
```

**Response (201)**
```json
{
  "redirect_url": "https://checkout.svea.com/.../iframe",
  "reference": 98765
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden` (course not purchasable / already owned)
- **404** `not_found` (course not found)
- **422** `validation_error`

## GET /checkout/status/{reference}

Returns the current status of a checkout session.

**Request**
```http
GET /api/v1/checkout/status/{reference}
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "status": "pending",
  "order": {
    "order_id": 98765,
    "payment_mode": "Faktura",
    "is_processed": false,
    "svea_order_id": "123456789"
  }
}
```

**Errors**
- **401** `unauthorized`
- **404** `not_found`

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

# Invoices

## GET /invoices

Returns recent invoices for the authenticated learner.

**Request**
```http
GET /api/v1/invoices
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": [
    {
      "id": 123,
      "invoice_number": "INV-2024-001",
      "reference": "123456789",
      "status": "paid",
      "total": 14900,
      "due_date": "2024-02-01",
      "created_at": "2024-01-02T10:45:00Z"
    }
  ]
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`

## GET /invoices/{id}

Returns a single invoice for the authenticated learner.

**Request**
```http
GET /api/v1/invoices/123
Authorization: Bearer <access_token>
```

**Response (200)**
```json
{
  "data": {
    "id": 123,
    "invoice_number": "INV-2024-001",
    "reference": "123456789",
    "status": "paid",
    "total": 14900,
    "balance": 0,
    "due_date": "2024-02-01",
    "issue_date": "2024-01-01",
    "pdf_url": "https://fiken.no/api/v1/files/...",
    "fiken_weblink": "https://fiken.no/filer/...",
    "created_at": "2024-01-02T10:45:00Z"
  }
}
```

**Errors**
- **401** `unauthorized`
- **403** `forbidden`
- **404** `invoice_not_found`

## GET /invoices/{id}/pdf

Downloads the invoice PDF.

**Request**
```http
GET /api/v1/invoices/123/pdf
Authorization: Bearer <access_token>
```

**Response**
- **200** file download

**Errors**
- **401** `unauthorized`
- **403** `invoice_forbidden`
- **404** `invoice_not_found | invoice_pdf_missing`

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

## GET /courses/{id}/packages

Returns purchasable package options for a course. Each package can repeat for each enabled payment plan.

**Request**
```http
GET /api/v1/courses/12/packages
```

**Response (200)**
```json
{
  "data": [
    {
      "id": "88",
      "name": "Standard Package",
      "price_total": 14900,
      "currency": "NOK",
      "payment_type": "full",
      "is_default": true,
      "is_available": true
    },
    {
      "id": "88",
      "name": "Standard Package",
      "price_total": 14900,
      "currency": "NOK",
      "payment_type": "installment",
      "installments": 3,
      "first_payment": 4966.67,
      "is_default": false,
      "is_available": true
    }
  ]
}
```

**Errors**
- **404** `not_found` (course not found)

## GET /courses/{id}/plan

Returns the course plan content as displayed in the frontend course page. For webinar-only
courses (course id `17`), the response includes upcoming webinars instead of a text plan.

**Request**
```http
GET /api/v1/courses/12/plan
```

**Response (200, standard course)**
```json
{
  "data": {
    "type": "course_plan",
    "course_plan": "Week 1: Introduction\nWeek 2: Drafting",
    "course_plan_html": "Week 1: Introduction<br />\nWeek 2: Drafting",
    "course_plan_data": "<p>Optional schedule markup.</p>",
    "has_course_plan_data": true
  }
}
```

**Response (200, webinar course)**
```json
{
  "data": {
    "type": "webinars",
    "webinars": [
      {
        "id": 55,
        "title": "Opening session",
        "description": "<p>Meet the instructor.</p>",
        "short_description": "Meet the instructor.",
        "start_date": "2024-02-01T18:00:00Z",
        "image_url": "https://www.forfatterskolen.no/images/no_image.png"
      }
    ]
  }
}
```

**Errors**
- **404** `not_found` (course not found)

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
curl -s "https://<your-domain>/api/v1/profile" \
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
