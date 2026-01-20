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
