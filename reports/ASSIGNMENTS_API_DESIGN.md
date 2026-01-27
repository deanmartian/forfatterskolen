# Assignments API Design for Lovable Portal

**Date**: 2026-01-27
**Based on**: Full codebase analysis of admin.easywrite.se / admin.forfatterskolen.no

---

## PHASE 0: Discovery Findings

### 1. Models & File Locations

| Model | File | Table | Purpose |
|-------|------|-------|---------|
| `Assignment` | `app/Assignment.php:11` | `assignments` | Core assignment definition |
| `AssignmentManuscript` | `app/AssignmentManuscript.php:12` | `assignment_manuscripts` | Learner submissions |
| `AssignmentGroup` | `app/AssignmentGroup.php:10` | `assignment_groups` | Peer feedback groups |
| `AssignmentGroupLearner` | `app/AssignmentGroupLearner.php:10` | `assignment_group_learners` | Group membership |
| `AssignmentFeedback` | `app/AssignmentFeedback.php:9` | `assignment_feedbacks` | Peer feedback (in groups) |
| `AssignmentFeedbackNoGroup` | `app/AssignmentFeedbackNoGroup.php:9` | `assignment_feedbacks_no_group` | Editor feedback (no group) |
| `AssignmentTemplate` | `app/AssignmentTemplate.php:8` | `assignment_templates` | Reusable assignment blueprints |
| `AssignmentAddon` | `app/AssignmentAddon.php:8` | `assignment_addons` | Extra assignment purchases |
| `AssignmentLearnerConfiguration` | `app/AssignmentLearnerConfiguration.php:7` | `assignment_learner_configurations` | Per-learner word limits |
| `AssignmentLearnerSubmissionDate` | `app/AssignmentLearnerSubmissionDate.php:8` | `assignment_learner_submission_dates` | Per-learner deadlines |
| `AssignmentDisabledLearner` | `app/AssignmentDisabledLearner.php:7` | `assignment_disabled_learners` | Learners excluded from assignment |

### 2. Database Tables & Columns

#### `assignments`
```
id                        INT (PK, auto)
course_id                 INT (FK to courses, nullable)
title                     VARCHAR(100)
description               TEXT
submission_date           VARCHAR(100) -- can be date or number of days
available_date            DATE (nullable)
allowed_package           VARCHAR (JSON array of package IDs, nullable)
add_on_price              DECIMAL(11) default 0
max_words                 INT
allow_up_to               INT (added later, overrides max_words)
for_editor                INT (boolean)
editor_id                 INT default 0
editor_manu_generate_count VARCHAR(10) (nullable)
generated_filepath        VARCHAR (nullable)
show_join_group_question  BOOLEAN default 1
send_letter_to_editor     BOOLEAN default 0
check_max_words           BOOLEAN default 0
assigned_editor           INT (nullable)
parent_id                 INT (nullable) -- FK to users.id or assignments.id
parent                    VARCHAR(100) (nullable) -- 'users', 'assignment', 'course', or NULL
editor_expected_finish    DATE (nullable)
expected_finish           DATE (nullable)
created_at                TIMESTAMP
updated_at                TIMESTAMP
```

**`parent` field values:**
- `NULL` or `'course'` = Course Assignment (visible to all enrolled learners)
- `'users'` = Personal/Learner Assignment (specific to one learner, `parent_id` = `user_id`)
- `'assignment'` = Linked to another assignment

#### `assignment_manuscripts`
```
id                    INT (PK)
assignment_id         INT (FK)
user_id               INT (FK to users)
filename              VARCHAR (file path)
words                 INT default 0
grade                 DECIMAL(11) (nullable)
type                  INT default 0 (genre type)
manu_type             INT default 0 (where in script)
locked                BOOLEAN default 0
text_number           INT default 0
editor_id             INT default 0
has_feedback          BOOLEAN default 0
join_group            BOOLEAN default 0
letter_to_editor      VARCHAR (nullable, file path)
expected_finish       DATE (nullable)
editor_expected_finish DATE (nullable)
status                INT default 0 (0=pending, 1=approved, 2=finished)
show_in_dashboard     BOOLEAN default 0
uploaded_at           DATETIME (nullable)
created_at            TIMESTAMP
updated_at            TIMESTAMP
```

#### `assignment_groups`
```
id                      INT (PK)
assignment_id           INT (FK)
title                   VARCHAR
submission_date         DATETIME (nullable)
allow_feedback_download BOOLEAN default 0
availability            DATE (nullable)
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

#### `assignment_group_learners`
```
id                      INT (PK)
assignment_group_id     INT (FK)
user_id                 INT (FK)
could_send_feedback_to  VARCHAR (nullable, comma-separated group_learner IDs)
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

#### `assignment_feedbacks` (Peer feedback)
```
id                          INT (PK)
assignment_group_learner_id INT (FK)
user_id                     INT (FK, who submitted)
filename                    VARCHAR (file path)
is_admin                    BOOLEAN default 0
is_active                   BOOLEAN default 0
availability                DATE (nullable)
locked                      BOOLEAN default 0
hours_worked                DECIMAL (nullable)
notes_to_head_editor        TEXT (nullable)
created_at                  TIMESTAMP
updated_at                  TIMESTAMP
```

#### `assignment_feedbacks_no_group` (Editor feedback)
```
id                      INT (PK)
assignment_manuscript_id INT (FK)
learner_id              INT (FK)
feedback_user_id        INT (FK, editor who gave feedback)
filename                TEXT (file paths, comma-separated)
is_admin                BOOLEAN default 0
is_active               INT default 0
availability            DATE (nullable)
locked                  BOOLEAN default 0
hours_worked            DECIMAL (nullable)
notes_to_head_editor    TEXT (nullable)
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

#### `assignment_templates`
```
id              INT (PK)
title           VARCHAR(100)
description     TEXT
submission_date VARCHAR(100) (nullable)
available_date  DATE (nullable)
max_words       INT
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### `assignment_addons`
```
id            INT (PK)
user_id       INT (FK)
assignment_id INT (FK)
created_at    TIMESTAMP
updated_at    TIMESTAMP
```

### 3. Relationships Diagram

```
                         +-----------------+
                         |    courses      |
                         +-----------------+
                                 |
                                 | 1:N
                                 v
+------------------+     +-------------------+     +--------------------+
| assignment_      |     |   assignments     |     |  assignment_       |
| templates        |     +-------------------+     |  addons            |
| (blueprints)     |     | parent=NULL/course|<----| (extra purchases)  |
+------------------+     | parent='users'    |     +--------------------+
                         | parent='assignment'|
                         +-------------------+
                                 |
            +--------------------+--------------------+
            |                    |                    |
            | 1:N                | 1:N                | 1:N
            v                    v                    v
+---------------------+  +----------------+  +------------------------+
| assignment_         |  | assignment_    |  | assignment_disabled_   |
| manuscripts         |  | groups         |  | learners               |
| (submissions)       |  | (peer groups)  |  | (excluded learners)    |
+---------------------+  +----------------+  +------------------------+
            |                    |
            | 1:N                | 1:N
            v                    v
+---------------------+  +------------------------+
| assignment_         |  | assignment_group_      |
| feedbacks_no_group  |  | learners               |
| (editor feedback)   |  | (group membership)     |
+---------------------+  +------------------------+
                                  |
                                  | 1:1
                                  v
                         +------------------------+
                         | assignment_feedbacks   |
                         | (peer feedback)        |
                         +------------------------+
```

### 4. Admin Tabs Explained

From `resources/views/backend/assignment/index.blade.php`:

| Tab | Description | Data Source |
|-----|-------------|-------------|
| **Course Assignments** | Assignments tied to a course, available to enrolled learners based on package | `Assignment::forCourseOnly()` - where `parent` is NULL or 'course' |
| **Learner Assignments** | Personal assignments created for specific learners (one-on-one) | `Assignment::forLearnerOnly()` - where `parent='users'` |
| **Assignment Templates** | Reusable templates for quickly creating learner assignments | `AssignmentTemplate::all()` |

### 5. Controllers & Routes

#### Backend (Admin) - `app/Http/Controllers/Backend/AssignmentController.php`
| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `GET /assignment` | index | `AssignmentController@index` | List all assignments |
| `GET /course/{course_id}/assignment/{id}` | show | `AssignmentController@show` | View assignment details |
| `POST /course/{course_id}/assignment` | store | `AssignmentController@store` | Create course assignment |
| `PUT /course/{course_id}/assignment/{id}` | update | `AssignmentController@update` | Update assignment |
| `DELETE /course/{course_id}/assignment/{id}` | destroy | `AssignmentController@destroy` | Delete assignment |
| `POST /assignment/{id}/uploadManuscript` | uploadManuscript | `AssignmentController@uploadManuscript` | Admin upload for learner |
| `POST /assignment_manuscript/{id}/learner/{learner_id}/feedback` | manuscriptFeedbackNoGroup | Add editor feedback |
| `POST /assignment/learner-assignment/save/{id?}` | learnerAssignment | Create/update personal assignment |
| `POST /assignment/template/save/{id?}` | saveAssignmentTemplate | Create/update template |

#### Frontend (Learner) - `app/Http/Controllers/Frontend/LearnerController.php`
| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `GET /account/assignment` | assignment | `LearnerController@assignment` | View learner's assignments |
| `POST /account/assignment/{id}/upload` | assignmentManuscriptUpload | Upload submission |
| `POST /account/assignment/{id}/replace_manuscript` | replaceAssignmentManuscript | Replace submission |
| `GET /account/assignment/group/{id}` | group_show | View peer group |
| `POST /account/group/{group_id}/learner/{id}/submit_feedback` | submit_feedback | Submit peer feedback |
| `GET /account/assignment/feedback/{id}/download` | downloadAssignmentGroupFeedback | Download feedback |
| `GET /account/assignment/feedback-no-group/{id}/download` | downloadAssignmentNoGroupFeedback | Download editor feedback |

### 6. Current Behaviors

#### A) Assignment Visibility for Learners

From `LearnerController@assignment` (line 1185-1431):

1. **Course Assignments** are visible if:
   - Learner has `coursesTaken` with valid `end_date`
   - Assignment `allowed_package` includes learner's package OR is null
   - Learner is NOT in `assignment_disabled_learners`
   - Either: submission_date hasn't passed, OR manuscript isn't locked/has no feedback

2. **Personal Assignments** (`parent='users'`) are visible if:
   - `parent_id` matches the authenticated user's ID
   - `available_date` has passed (or is null)
   - `submission_date` hasn't passed

3. **Add-on Assignments**: Learners can purchase access via `assignment_addons`

#### B) Submission Flow

1. Learner uploads manuscript file (`.doc`, `.docx`, `.odt`, `.pdf`)
2. Word count is extracted and validated against `max_words` (with 2% tolerance)
3. `AssignmentManuscript` record created with:
   - `filename`: stored at `storage/assignment-manuscripts/`
   - `words`: word count
   - `type`, `manu_type`: genre and position metadata
   - `join_group`: whether learner wants peer feedback
   - `letter_to_editor`: optional accompanying letter
4. Email notifications sent to admin and learner

#### C) Feedback Flow

**Editor Feedback (No Group):**
1. Editor uploads feedback file(s) to `storage/assignment-feedbacks/`
2. `AssignmentFeedbackNoGroup` created
3. `manuscript.has_feedback = 1`, `manuscript.status = 1` (approved)
4. `availability` date controls when learner can see it
5. Email sent to learner

**Peer Feedback (Groups):**
1. Admin generates groups from learners with `join_group=1`
2. Groups of ~3 learners created in `assignment_groups`
3. Learners see each other's manuscripts
4. Learners upload feedback files for group members
5. `AssignmentFeedback` records created

#### D) File Upload Flow

**Storage Location**: `public/storage/` directory
- Manuscripts: `storage/assignment-manuscripts/{user_id}_{timestamp}.{ext}`
- Feedback: `storage/assignment-feedbacks/{user_id}f_{timestamp}.{ext}`
- Letters: `storage/letter-to-editor/{timestamp}.{ext}`

**File Integrity**: `FileIntegrityService` validates uploaded files

**Allowed Extensions**:
- Manuscripts: `doc`, `docx`, `odt`, `pdf` (for editor: `doc`, `docx` only)
- Feedback: `pdf`, `docx`, `odt`

**Downloads**: Direct file access via `response()->download(public_path($filename))`

### 7. Authorization Patterns

**Admin Routes**: Protected by `middleware('checkPageAccess:5')` in controller constructor

**Frontend Routes**: User must be authenticated (`auth` middleware via route group)

**Ownership Checks** in `LearnerController`:
- Manuscript ownership: `where('user_id', Auth::user()->id)`
- Group membership: `whereHas('learners', fn($q) => $q->where('user_id', Auth::user()->id))`
- Course enrollment: checked via `coursesTaken` relationship

---

## PHASE 1: Minimal API Design for Lovable Portal

### Existing API Infrastructure

From `routes/api.php` and `app/Http/Middleware/`:
- JWT authentication via `apiJwt` middleware
- User retrieved via `$request->attributes->get('api_user')`
- Base controller: `ApiController` with `userOwnsCourse()` helper
- CORS configured for Lovable origins in `config/api.php`

### Proposed Endpoints

#### A) List Assignments for Enrolled Courses

```
GET /api/v1/assignments
```

**Auth**: `apiJwt` middleware (Bearer token required)

**Authorization**: Only returns assignments for courses the user is enrolled in

**Query Parameters**:
- `status`: `active`, `expired`, `upcoming`, `waiting` (optional, default: all)

**Eloquent Query**:
```php
// Get user's enrolled course IDs
$user = $this->apiUser($request);
$courseIds = CoursesTaken::where('user_id', $user->id)
    ->whereNotNull('end_date')
    ->where('end_date', '>=', Carbon::now())
    ->with('package')
    ->get()
    ->pluck('package.course_id')
    ->unique();

// Course assignments
$courseAssignments = Assignment::forCourseOnly()
    ->whereIn('course_id', $courseIds)
    ->where(function($q) {
        $q->where('available_date', '<=', Carbon::now())
          ->orWhereNull('available_date');
    })
    ->get();

// Personal assignments
$personalAssignments = Assignment::forLearnerOnly()
    ->where('parent_id', $user->id)
    ->get();

// Filter by allowed_package and disabled learners (see existing logic)
```

**Response Shape**:
```json
{
  "data": [
    {
      "id": 123,
      "title": "Oppgave 1",
      "description": "Submit your first chapter...",
      "course": {
        "id": 5,
        "title": "Romanskriving"
      },
      "submission_date": "2026-02-15T23:59:00",
      "submission_date_text": "15 Feb 2026 kl 23:59",
      "available_date": "2026-01-01",
      "max_words": 5000,
      "allow_up_to": 5500,
      "type": "course",
      "show_join_group_question": true,
      "send_letter_to_editor": false,
      "status": "active",
      "my_submission": {
        "id": 456,
        "filename": "chapter1.docx",
        "words": 4200,
        "uploaded_at": "2026-01-20T14:30:00",
        "locked": true,
        "has_feedback": false
      }
    }
  ]
}
```

**Errors**:
- `401 Unauthorized`: Missing/invalid token
- `403 Forbidden`: User inactive

---

#### B) Get Single Assignment

```
GET /api/v1/assignments/{id}
```

**Auth**: `apiJwt` middleware

**Authorization**: User must be enrolled in the assignment's course OR be the `parent_id` for personal assignments

**Eloquent Query**:
```php
$assignment = Assignment::with(['course', 'manuscripts' => function($q) use ($user) {
    $q->where('user_id', $user->id);
}])->findOrFail($id);

// Verify access
if ($assignment->parent === 'users') {
    abort_if($assignment->parent_id !== $user->id, 403);
} else {
    abort_if(!$this->userOwnsCourse($user, $assignment->course), 403);
}
```

**Response Shape**:
```json
{
  "data": {
    "id": 123,
    "title": "Oppgave 1",
    "description": "<p>Full description with HTML...</p>",
    "course": {
      "id": 5,
      "title": "Romanskriving"
    },
    "submission_date": "2026-02-15T23:59:00",
    "submission_date_text": "15 Feb 2026 kl 23:59",
    "available_date": "2026-01-01",
    "max_words": 5000,
    "allow_up_to": 5500,
    "check_max_words": true,
    "type": "course",
    "show_join_group_question": true,
    "send_letter_to_editor": false,
    "my_submission": null,
    "my_group": null,
    "my_feedback": null
  }
}
```

**Errors**:
- `401 Unauthorized`: Missing/invalid token
- `403 Forbidden`: Not enrolled / not owner
- `404 Not Found`: Assignment doesn't exist

---

#### C) List My Submissions & Status

```
GET /api/v1/assignments/submissions
```

**Auth**: `apiJwt` middleware

**Query Parameters**:
- `assignment_id`: Filter by specific assignment (optional)

**Eloquent Query**:
```php
$user = $this->apiUser($request);

$submissions = AssignmentManuscript::with(['assignment.course', 'noGroupFeedbacks'])
    ->where('user_id', $user->id)
    ->when($request->assignment_id, fn($q, $id) => $q->where('assignment_id', $id))
    ->orderBy('created_at', 'desc')
    ->get();
```

**Response Shape**:
```json
{
  "data": [
    {
      "id": 456,
      "assignment": {
        "id": 123,
        "title": "Oppgave 1",
        "course_title": "Romanskriving"
      },
      "filename": "chapter1.docx",
      "words": 4200,
      "grade": null,
      "uploaded_at": "2026-01-20T14:30:00",
      "locked": true,
      "status": "waiting_feedback",
      "has_feedback": false,
      "feedback": null,
      "download_url": "/api/v1/assignments/submissions/456/download"
    }
  ]
}
```

**Status Values**:
- `draft`: Submission exists but not locked
- `waiting_feedback`: Locked, awaiting editor/peer response
- `feedback_available`: Feedback ready to view
- `completed`: Fully processed (status=2)

---

#### D) Create/Update Submission

```
POST /api/v1/assignments/{id}/submit
```

**Auth**: `apiJwt` middleware

**Authorization**: User must have access to assignment AND not already have a locked submission

**Request Body** (multipart/form-data):
```
manuscript: File (required, .doc/.docx/.odt/.pdf)
letter_to_editor: File (optional, if send_letter_to_editor=true)
type: Integer (optional, genre code)
manu_type: Integer (optional, where in script code)
join_group: Boolean (optional, default false)
```

**Validation**:
```php
$rules = [
    'manuscript' => 'required|file|mimes:doc,docx,odt,pdf|max:20480',
    'letter_to_editor' => 'nullable|file|mimes:doc,docx,odt,pdf|max:10240',
    'type' => 'nullable|integer',
    'manu_type' => 'nullable|integer',
    'join_group' => 'nullable|boolean',
];
```

**Logic** (mirror existing `assignmentManuscriptUpload`):
1. Validate file format
2. Extract word count
3. Check against `max_words` if `check_max_words` is enabled (with 2% tolerance)
4. Store file in `storage/assignment-manuscripts/`
5. Create `AssignmentManuscript` record
6. Queue notification emails

**Response**:
```json
{
  "data": {
    "id": 789,
    "assignment_id": 123,
    "filename": "chapter1.docx",
    "words": 4200,
    "uploaded_at": "2026-01-27T10:00:00",
    "locked": false,
    "message": "Submission uploaded successfully"
  }
}
```

**Errors**:
- `400 Bad Request`: Invalid file format, exceeds word limit
- `401 Unauthorized`: Missing/invalid token
- `403 Forbidden`: Not enrolled, already submitted & locked
- `404 Not Found`: Assignment doesn't exist
- `422 Unprocessable`: Validation errors

---

#### E) Download Submission/Feedback

```
GET /api/v1/assignments/submissions/{id}/download
GET /api/v1/assignments/feedback/{id}/download
```

**Auth**: `apiJwt` middleware

**Authorization**:
- Submission: Must be owner (`user_id` matches)
- Feedback: Must be the learner who received it (`learner_id` matches)

**Response**: Binary file download

**Implementation**:
```php
public function downloadSubmission(Request $request, int $id)
{
    $user = $this->apiUser($request);
    $manuscript = AssignmentManuscript::where('id', $id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    $path = public_path($manuscript->filename);
    abort_if(!file_exists($path), 404, 'File not found');

    return response()->download($path);
}

public function downloadFeedback(Request $request, int $id)
{
    $user = $this->apiUser($request);
    $feedback = AssignmentFeedbackNoGroup::where('id', $id)
        ->where('learner_id', $user->id)
        ->where('is_active', 1)
        ->where(function($q) {
            $q->whereNull('availability')
              ->orWhere('availability', '<=', Carbon::today());
        })
        ->firstOrFail();

    // Handle multiple files (comma-separated)
    $files = array_filter(array_map('trim', explode(',', $feedback->filename)));

    if (count($files) === 1) {
        return response()->download(public_path($files[0]));
    }

    // Multiple files: return zip
    // ... zip creation logic
}
```

---

### Endpoint Summary Table

| Method | Path | Purpose | Auth |
|--------|------|---------|------|
| `GET` | `/api/v1/assignments` | List learner's assignments | JWT |
| `GET` | `/api/v1/assignments/{id}` | Get single assignment | JWT |
| `GET` | `/api/v1/assignments/submissions` | List my submissions | JWT |
| `POST` | `/api/v1/assignments/{id}/submit` | Upload submission | JWT |
| `GET` | `/api/v1/assignments/submissions/{id}/download` | Download my submission | JWT |
| `GET` | `/api/v1/assignments/feedback/{id}/download` | Download my feedback | JWT |

---

## PHASE 2: Implementation Plan

### 1. File Structure

```
app/Http/Controllers/Api/V1/
  AssignmentController.php        # New controller

app/Http/Resources/Api/V1/
  AssignmentResource.php          # Assignment JSON transformer
  AssignmentSubmissionResource.php # Submission JSON transformer

routes/api.php                    # Add new routes
```

### 2. Routes to Add

In `routes/api.php`, within the existing `apiJwt` middleware group:

```php
Route::middleware('apiJwt')->group(function () {
    // ... existing routes ...

    // Assignments
    Route::get('/assignments', [AssignmentController::class, 'index']);
    Route::get('/assignments/submissions', [AssignmentController::class, 'submissions']);
    Route::get('/assignments/{id}', [AssignmentController::class, 'show']);
    Route::post('/assignments/{id}/submit', [AssignmentController::class, 'submit']);
    Route::get('/assignments/submissions/{id}/download', [AssignmentController::class, 'downloadSubmission']);
    Route::get('/assignments/feedback/{id}/download', [AssignmentController::class, 'downloadFeedback']);
});
```

### 3. Controller Skeleton

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Assignment;
use App\AssignmentFeedbackNoGroup;
use App\AssignmentManuscript;
use App\CoursesTaken;
use App\Http\Resources\Api\V1\AssignmentResource;
use App\Http\Resources\Api\V1\AssignmentSubmissionResource;
use App\Services\FileIntegrityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentController extends ApiController
{
    protected FileIntegrityService $fileIntegrityService;

    public function __construct(FileIntegrityService $fileIntegrityService)
    {
        $this->fileIntegrityService = $fileIntegrityService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);
        // ... implementation from Phase 1A
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);
        // ... implementation from Phase 1B
    }

    public function submissions(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);
        // ... implementation from Phase 1C
    }

    public function submit(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);
        // ... implementation from Phase 1D
    }

    public function downloadSubmission(Request $request, int $id)
    {
        // ... implementation from Phase 1E
    }

    public function downloadFeedback(Request $request, int $id)
    {
        // ... implementation from Phase 1E
    }
}
```

### 4. Authorization Helpers

Add to `ApiController.php`:

```php
protected function userHasAssignmentAccess(User $user, Assignment $assignment): bool
{
    if ($assignment->parent === 'users') {
        return $assignment->parent_id === $user->id;
    }

    if (!$assignment->course_id) {
        return false;
    }

    return $this->userOwnsCourse($user, $assignment->course);
}

protected function isLearnerDisabled(User $user, Assignment $assignment): bool
{
    return $assignment->disabledLearners()
        ->where('user_id', $user->id)
        ->exists();
}
```

### 5. CORS Configuration

Already configured in `config/api.php`:
```php
'cors' => [
    'lovable_origins' => env('LOVABLE_CORS_ORIGINS', 'https://lovable.app,https://staging.lovable.app'),
],
```

**Action**: Add production Lovable domain to `.env`:
```
LOVABLE_CORS_ORIGINS=https://lovable.app,https://staging.lovable.app,https://your-app.lovable.app
```

### 6. Caching Suggestions

**Safe to cache** (read-only, changes rarely):
- Assignment list for a course (5 min TTL, bust on assignment create/update)
- Assignment templates (10 min TTL)

**Do NOT cache**:
- User's submissions (changes frequently)
- Feedback availability (date-dependent)
- Enrollment checks (can change)

```php
// Example: Cache assignment list per course
$assignments = Cache::remember(
    "api.v1.course.{$courseId}.assignments",
    300, // 5 minutes
    fn() => Assignment::forCourseOnly()->where('course_id', $courseId)->get()
);
```

---

## Implementation Checklist

- [ ] Create `app/Http/Controllers/Api/V1/AssignmentController.php`
- [ ] Create `app/Http/Resources/Api/V1/AssignmentResource.php`
- [ ] Create `app/Http/Resources/Api/V1/AssignmentSubmissionResource.php`
- [ ] Add routes to `routes/api.php`
- [ ] Add helper methods to `ApiController.php`
- [ ] Add Lovable production domain to CORS allowlist
- [ ] Write feature tests for each endpoint
- [ ] Test file upload with different file types
- [ ] Test authorization edge cases (disabled learner, expired course)
- [ ] Document API in OpenAPI/Swagger format

---

## What's Missing / Assumptions NOT Made

1. **No API for peer feedback submission**: The existing peer feedback system is complex (groups, feedback-to-other assignments). Implementing this requires more analysis.

2. **No API for group viewing**: `assignment_groups` and peer manuscript reading is out of scope for minimal API.

3. **No real-time status updates**: Webhook or polling mechanism for feedback availability not designed.

4. **File upload to external storage**: Current system uses local `public/storage`. For Lovable, consider S3 with signed URLs.

5. **Word count extraction**: Currently done server-side with `Docx2Text`, `PdfToText`. This is compute-intensive; consider moving to a queue job for large files.

6. **Grade/feedback text**: The system stores `grade` as a decimal but there's no structured feedback text field for editor comments beyond the uploaded file.

---

## Key File References

| Component | File:Line |
|-----------|-----------|
| Assignment model | `app/Assignment.php:11` |
| AssignmentManuscript model | `app/AssignmentManuscript.php:12` |
| Backend AssignmentController | `app/Http/Controllers/Backend/AssignmentController.php:50` |
| Frontend assignment() method | `app/Http/Controllers/Frontend/LearnerController.php:1185` |
| Frontend upload method | `app/Http/Controllers/Frontend/LearnerController.php:1450` |
| API auth middleware | `app/Http/Middleware/ApiJwtAuth.php:12` |
| API base controller | `app/Http/Controllers/Api/V1/ApiController.php:13` |
| CORS middleware | `app/Http/Middleware/Cors.php:9` |
| Routes (API) | `routes/api.php:34` |
| Routes (web assignments) | `routes/web.php:455` |
