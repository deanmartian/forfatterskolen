# Community Integration Plan (Laravel → Lovable / Supabase)

## Architecture Principle

Forfatterskolen (Laravel / MySQL) remains the source of truth for:

- users
- courses
- permissions
- roles
- community access

Lovable + Supabase store **community-specific data only**, such as:

- posts
- comments
- reactions
- messages
- groups
- notifications
- community profiles

Supabase must **not** become the source of truth for access control.

## 1) Laravel API Endpoint

Create a secure endpoint in Laravel that returns the community data for the currently logged-in user.

**Endpoint:** `GET /api/community/user`

The endpoint should return:

- `external_user_id`
- `name`
- `email`
- `community_access`
- `course_access`
- `roles`

Example response:

```json
{
  "external_user_id": 27,
  "name": "Helge Skurtveit",
  "email": "hyss37@gmail.com",
  "community_access": true,
  "course_access": ["barnebokkurs", "dramaturgi"],
  "roles": ["member"]
}
```

The endpoint must only be accessible by authenticated users.

## 2) Supabase Community Tables

Supabase stores community-related data.

### `community_members`

- `id`
- `external_user_id`
- `name`
- `email`
- `community_access`
- `created_at`
- `updated_at`

### `community_user_courses`

- `external_user_id`
- `course_slug`
- `expires`

These tables are only used by the community system.

## 3) Lovable → Laravel Integration

When the community application loads, Lovable calls Laravel:

- `GET /api/community/user`

Use the response to:

- create a user in Supabase if not found
- update the user if already found
- synchronize course access

`external_user_id` must be the primary cross-system identifier.

## 4) Access Logic

Community visibility should be controlled using Laravel-provided access data.

### Main community

Visible if:

- `community_access = true`

### Course groups

Visible if the user has the relevant course in `course_access[]`.

Example:

- If the user has `"dramaturgi"` in `course_access`, the Dramaturgi group is visible.

This avoids manual group management.

## 5) Community MVP Features

Initial community functionality should include:

- main community feed
- posts
- comments
- reactions
- member profiles
- course-based groups
- basic moderation tools

All access control is based on data from Laravel.

## 6) Pilot Reader Integration

Pilot Reader remains responsible for:

- manuscript viewing
- HTML reading interface

The community system handles discussion around manuscripts.

In Pilot Reader, add a button:

- **"Share in Community"**

When clicked, it opens the community "create post" page with:

- category: Pilot Reader
- title
- link to the manuscript
- optional description

Example link:

- `/community/new-post?category=pilotleser&doc=123`

Discussion then happens inside the community thread.

## Key Principle

Laravel manages:

- identity
- courses
- permissions

Supabase manages:

- community activity

This architecture should support reuse of the same integration model for other Lovable tools in the future.

## 7) One-time Code Flow (ny.fs → forum)

Use a short-lived one-time code in URL redirects instead of placing JWTs in URL query params.

### Step A (ny.fs / Laravel API v1)

- Authenticated ny.fs client calls: `POST /api/v1/community/issue-code`
- Response returns a one-time `code` and `expires_at`

### Step B (forum / Laravel community API)

- Forum backend receives redirect query param: `?code=...`
- Forum backend calls: `POST /api/community/exchange-code` with `{ "code": "..." }`
- Laravel validates code (exists, not expired, not used) and returns an `access_token`
- Forum uses `Authorization: Bearer <access_token>` for `GET /api/community/user`

This keeps JWT transport in headers and uses URL-safe one-time codes only for handoff.

