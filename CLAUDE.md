# CLAUDE.md - Forfatterskolen

## Project Overview

Forfatterskolen (The Writer's School) is a Norwegian online writing education platform. It offers courses, webinars, manuscript feedback services, coaching, community features, and a self-publishing pipeline (Indiemoon). The platform serves three user roles across three subdomains:

- **www.forfatterskolen.no** - Learner-facing (students, public pages, shop)
- **admin.forfatterskolen.no** - Admin dashboard (staff, CRM, invoicing, course management)
- **editor.forfatterskolen.no** - Editor portal (manuscript feedback, coaching, assignments)

The project language is Norwegian (UI, routes, variables, comments). Respond in Norwegian when interacting with the owner.

## Tech Stack

### Backend
- **PHP** ^8.2
- **Laravel** ^12.18
- **MySQL** (utf8mb4_unicode_ci, strict mode OFF)
- **Queue**: database driver
- **Session**: file driver
- **Cache**: file driver

### Frontend
- **Vue.js** 2.7 (Options API, single-file components)
- **Bootstrap 3** (via bootstrap-sass ^3.3.7)
- **jQuery** ^3.1.1
- **Laravel Mix** 6 (webpack)
- **Sass** (SCSS)
- **TinyMCE** (via @tinymce/tinymce-vue ^3)
- **SweetAlert2** ^11
- **Bootstrap-Vue** ^2.21
- **Workbox** (PWA service worker, production only)

### Key PHP Packages
- `barryvdh/laravel-dompdf` - PDF generation
- `intervention/image` 2.7 - Image processing
- `maatwebsite/excel` ~3.1 - Excel import/export
- `phpoffice/phpword` - Word document generation
- `sveaekonomi/webpay` - Svea payment integration
- `league/omnipay` + `omnipay/paypal` - PayPal
- `laravel/socialite` - Social login (Facebook, Google)
- `anhskohbo/no-captcha` - reCAPTCHA
- `firebase/php-jwt` - JWT for API auth
- `spatie/flysystem-dropbox` - Dropbox file storage
- `league/flysystem-aws-s3-v3` - S3 storage
- `unisharp/laravel-filemanager` - File manager UI
- `van-ons/laraberg` - Gutenberg editor for Laravel

### Key JS Packages
- `vue-quill-editor` - Rich text editor
- `vue-form-wizard` - Multi-step forms
- `vue-select` - Searchable dropdowns
- `mammoth` - .docx to HTML conversion (client-side)
- `vue-moment` - Date formatting
- `vue-toasted` - Toast notifications

## Directory Structure

```
app/
  Console/Commands/     # 50+ artisan commands (scheduled tasks, imports)
  Http/
    Controllers/
      Api/              # REST API (V1 namespace, community SSO, shop, inbox webhook)
      Auth/             # Login, register, password reset, magic links
      Backend/          # Admin controllers (~97 controllers)
      Editor/           # Editor portal controllers (6 controllers)
      Frontend/         # Learner/public controllers (~29 controllers)
    Middleware/         # Admin, Editor, Learner, Cors, ApiJwt, LogsActivity, etc.
  Models/
    Inbox/             # InboxConversation, InboxMessage, InboxComment, etc.
    AdOs/              # Ad campaign optimization models
  Services/            # Business logic layer (~35 service classes)
  Mail/                # Mailable classes
  Jobs/                # Queue jobs
  (root)               # ~242 Eloquent models (legacy, not in Models/ subfolder)

config/                # Laravel config + services.php with all integrations
database/
  migrations/          # 544 migration files
  seeders/
resources/
  assets/
    js/                # Vue components, app.js entry point
    sass/              # SCSS files, app.scss entry point
  views/
    frontend/          # Public/learner Blade views
    frontend-easywrite/ # EasyWrite brand views
    backend/           # Admin dashboard views
      editor/          # Editor-specific admin views
      learner/         # Learner-specific admin views
      inbox/           # Inbox CRM views
      manuscript/      # Manuscript management views
    emails/            # Email templates
    vendor/            # Overridden vendor views (pagination, etc.)
routes/
  web.php             # Main routes (~1455 Route:: calls) - NEVER overwrite this file
  api.php             # API routes (V1, community, shop, webhooks)
  ad_os_routes.php    # Ad OS + Inbox routes (included in web.php)
  console.php         # Console routes
public/
  css/app.css         # Compiled CSS
  js/app.js           # Compiled JS
  blog/               # Static blog files (conflicts with /blog route - see Gotchas)
  push-sw.js          # Push notification service worker
webpack.mix.js        # Laravel Mix build config
```

## Multi-Domain Routing

Routes are split by subdomain using `Route::domain()` in `routes/web.php`. The `APP_SITE` env var controls which domain set is used:

| APP_SITE    | Front (www)              | Admin                          | Editor                          |
|-------------|--------------------------|--------------------------------|---------------------------------|
| `no`        | www.forfatterskolen.no   | admin.forfatterskolen.no       | editor.forfatterskolen.no       |
| `localhost` | forfatterskolen.local    | admin.forfatterskolen.local    | editor.forfatterskolen.local    |
| `dev.no`    | dev.forfatterskolen.no   | admin.dev.forfatterskolen.no   | editor.dev.forfatterskolen.no   |

### Route Naming Conventions
- `front.*` - Public/learner routes
- `learner.*` - Authenticated learner routes
- `admin.*` - Admin routes (via `Backend/` controllers)
- `editor.*` - Editor routes
- `api.v1.*` - API v1 routes

### Middleware
- **Admin** - Role check for admin access
- **Editor** - Role check for editor access
- **Learner** - Allows roles 1, 2, and 3 (not just role 1)
- **HeadEditor** - Head editor specific access
- **ApiJwt** - JWT authentication for API routes
- **Cors** - CORS headers for API
- **LogsActivity** - Activity tracking
- **Giutbok** - Special middleware for Giutbok section

## Database

MySQL database `forfatterskolen` with 544 migrations. Key tables include:

### Users & Auth
- `users` - All users (learners, editors, admins). Role field determines access level
- `magic_links` - Passwordless login (must support roles 1, 2, 3)
- `access_tokens`, `api_refresh_tokens` - API auth

### Courses & Learning
- `courses` - Course definitions
- `courses_taken` - Enrollment records (NOTE: table is `courses_taken`, not `course_taken`)
- `lessons` - Course lessons
- `assignments` - Writing assignments
- `assignment_submissions` - Student submissions
- `assignment_feedback` - Editor feedback on submissions
- `assignment_groups` - Peer review groups
- `assignment_learner` - Assignment-learner pivot
- `webinars` - Live webinar sessions
- `coaching_sessions` - 1-on-1 coaching

### Manuscripts & Publishing
- `shop_manuscripts` - Manuscript feedback shop items
- `shop_manuscripts_taken` - Purchased manuscript feedback
- `manuscript_feedback` - Detailed manuscript feedback
- `manuscript_projects` - Writing projects
- `manuscript_excerpts` - Manuscript text excerpts
- `publications` - Self-published works
- `project_books` - Books in publishing pipeline
- `book_orders` - Indiemoon bookshop orders
- `author_profiles` - Author public profiles

### Finance
- `invoices` - Invoice records
- `invoice_items` - Line items
- `payments` - Payment transactions
- `svea_orders` - Svea checkout orders
- `gift_cards` - Gift card codes
- `contracts` - Editor/author contracts
- `author_payouts` - Royalty payouts

### Communication
- `inbox_conversations` - Email CRM conversations (replaces Helpwise)
- `inbox_messages` - Individual email messages
- `inbox_comments` - Internal team comments
- `inbox_assignments` - Conversation assignments to staff
- `inbox_canned_responses` - Quick reply templates
- `inbox_auto_replies` - Automated responses
- `email_template` - Email templates (NOTE: table is `email_template`, not `templates`)
- `private_messages` - Internal messaging
- `newsletters` - Newsletter campaigns
- `newsletter_sends` - Newsletter send records
- `push_subscriptions` - Web push notification subscriptions

### Community
- `course_groups` - Course-based community groups
- `discussions` - Forum discussions
- `discussion_replies` - Discussion replies
- `posts` - Community feed posts
- `post_comments` - Post comments
- `post_reactions` - Emoji reactions on posts
- `direct_messages` - Community DMs

### CRM & Marketing
- `contacts` - CRM contacts
- `contact_tags` - Contact tagging
- `ad_campaigns` - Facebook/Google ad campaigns
- `ad_campaign_stats` - Campaign statistics
- `helpwise_webhook_logs` - Helpwise integration logs
- `email_automation_queue` - Automated email sequences
- `email_sequences` - Drip campaign definitions

## Key Models

Most models live directly in `app/` (legacy). Newer models use `app/Models/` subfolders.

### Core (app/)
- `User` - Central user model (roles: 1=learner, 2=editor, 3=admin)
- `Course` - Course with lessons, assignments, webinars
- `CourseTaken` - Enrollment (table: `courses_taken`)
- `Lesson` - Course lesson content
- `Assignment` / `AssignmentSubmission` / `AssignmentFeedback`
- `AssignmentGroup` / `AssignmentGroupLearner`
- `Webinar` - Live sessions (BigMarker/Whereby integration)
- `Invoice` / `Payment` / `GiftCard`
- `ShopManuscript` / `ShopManuscriptTaken`
- `Blog` - Blog posts
- `Advisory` - Advisory/counseling records
- `Application` - Course applications
- `Contract` / `AuthorPayout`
- `CoachingSession` - 1-on-1 coaching sessions
- `CalendarNote` - Calendar entries
- `ActivityLog` - User activity tracking

### Models/Inbox/
- `InboxConversation` - Email thread
- `InboxMessage` - Individual email
- `InboxComment` - Internal comment
- `InboxAssignment` - Staff assignment
- `InboxCannedResponse` - Quick replies
- `InboxAutoReply` - Auto-responses

### Models/ (other)
- `AdCampaign` / `AdCampaignStat` - Ad management
- `CourseGroup` - Community groups
- `Discussion` / `DiscussionReply` - Forum
- `Post` / `PostComment` / `PostReaction` - Community feed
- `DirectMessage` - Community DMs
- `PushSubscription` - Web push
- `Publication` - Self-published works
- `BookOrder` - Bookshop orders
- `AuthorProfile` - Author pages
- `Newsletter` / `NewsletterSend`
- `ManuscriptProject` / `ManuscriptExcerpt` / `ManuscriptFeedback`
- `MagicLink` - Passwordless auth
- `Profile` - Extended user profile
- `AssignmentExtensionRequest` - Deadline extensions
- `Contact` / `ContactTag` - CRM
- `Notification` - In-app notifications
- `HelpwiseReplyExample` / `HelpwiseWebhookLog`
- `EmailSequence` / `EmailSequenceStep` - Drip campaigns
- `EmailAutomationQueue` / `EmailAutomationExclusion`

## Key Controllers

### Backend (Admin) - app/Http/Controllers/Backend/ (~97 files)
- `AdminController` - Dashboard, settings
- `CourseController` - Course CRUD, enrollment management
- `AssignmentController` / `AssignmentGroupController` / `AssignmentReviewController`
- `InboxController` - Email CRM (replaces Helpwise)
- `EditorController` - Manage editors
- `BlogController` - Blog management
- `CrmController` - Contact CRM
- `ContractController` - Editor/author contracts
- `AdCampaignController` / `AdOsController` - Ad management
- `AdminEmailController` / `AdminMessageController` - Communications
- `BookPublisherController` / `BookForSaleController` - Publishing
- `AnthologyController` - Anthology management
- `CommunityController` - Community moderation
- `CompetitionController` - Writing competitions
- `CalendarNoteController` - Calendar
- `CheckoutLogController` - Payment logs
- `AdminAiController` - AI tools

### Editor - app/Http/Controllers/Editor/
- `PageController` - Editor dashboard
- `ManuscriptEditorCanTakeController` - Manuscript claim system
- `CoachingTimeController` / `CoachingSessionController` - Coaching management
- `AssignedWebinarController` - Webinar assignments
- `EditorMessageController` - Editor messaging

### Frontend (Learner/Public) - app/Http/Controllers/Frontend/
- `HomeController` - Homepage, public pages, webinars, FAQ
- `ShopController` - Course shop, Svea/Vipps checkout
- `CourseController` - Course catalog
- `LearnerController` - Student dashboard, progress
- `ShopManuscriptController` - Manuscript feedback shop
- `CommunityForumController` - Community access
- `PublicationController` - Publication pages
- `SelfPublishingController` / `PublishingServiceController`
- `VippsController` - Vipps payment flow
- `DocumentConversionController` - File format conversion
- `WorkshopController` - Workshop pages
- `PabyggTreffController` - "Pabygg Treff" feature
- `PrivateGroupsController` + related - Private writing groups

### API - app/Http/Controllers/Api/
- `V1/AuthController` - JWT login/refresh/logout
- `V1/CourseController` - Course listing, enrollment
- `V1/CheckoutController` - Payment flow
- `V1/AssignmentController` - Assignment submission/feedback
- `V1/WebinarController` - Webinar access
- `V1/ShopManuscriptController` - Manuscript shop API
- `V1/ProfileController` / `V1/DashboardController`
- `V1/VippsController` - Vipps payment callbacks
- `V1/InvoiceController` - Invoice PDF/receipt
- `V1/CoachingTimeController` - Coaching scheduling
- `CommunitySsoController` - SSO for community forum
- `InboxWebhookController` - Inbound email webhook
- `PushSubscriptionController` - Push notification management
- `Shop/ShopController` - Indiemoon bookshop API
- `Shop/ShopOrderController` - Order management
- `Shop/ShopPaymentController` - Vipps for bookshop

## Services Layer

Located in `app/Services/`:

- `InboxService` - Email CRM (IMAP polling, conversations, AI drafts)
- `ManuscriptFeedbackAiService` - AI-powered manuscript feedback
- `AiFeedbackService` - General AI feedback generation
- `HelpwiseService` / `HelpwiseImportService` - Helpwise integration (being replaced)
- `CourseService` - Course business logic
- `LearnerService` - Student operations
- `AssignmentService` - Assignment workflow
- `CoachingTimeService` - Coaching scheduling
- `ShopManuscriptService` / `ShopManuscriptApiCheckoutService` - Manuscript shop
- `ContactService` - CRM contact management
- `GiftService` - Gift card operations
- `FacebookAdsService` / `FacebookConversionService` - Meta ads
- `GoogleAdsService` - Google Ads conversions
- `ActiveCampaignService` - Email marketing
- `EmailAutomationService` - Drip campaigns
- `NewsletterService` - Newsletter sending
- `PushNotificationService` - Web push notifications
- `BigMarkerService` - Webinar platform API
- `WherebyService` - Video meeting rooms
- `WistiaService` - Video hosting
- `CloudConvertService` - File conversion
- `DocumentConversionService` / `DocxToPdfService` - Document conversion
- `FileIntegrityService` - File validation
- `ProjectService` - Writing project management
- `RoyaltyService` - Author royalty calculations
- `ReplayService` - Webinar replay management
- `LearnerCalendarService` - Calendar/ICS export
- `AdminAI/` - AI admin tools (subfolder)
- `Publishing/` - Publishing pipeline services (subfolder)
- `Helpwise/` - Helpwise API service (subfolder)
- `AdOs/` - Ad optimization services (subfolder)

## Naming Conventions

### Routes
- `front.*` / `learner.*` - Student-facing
- `admin.*` - Admin dashboard
- `editor.*` - Editor portal
- `api.v1.*` - REST API

### Views
- `frontend/` - Public/learner views
- `frontend-easywrite/` - EasyWrite brand
- `backend/` - Admin views
- `backend/editor/` - Editor-specific admin views
- `backend/learner/` - Learner-specific admin views
- `backend/inbox/` - Inbox CRM views
- `emails/` - Email templates

### CSS Class Prefixes
- `db-` - Dashboard components
- `co-` - Course components
- `ba-` - Base/general components
- `bl-` - Blog components
- `op-` - Option/settings components

## Brand & Design

### Colors
- `--wine` / `#862736` - Primary brand color (Forfatterskolen wine red)
- `--wine-dark` / `#5e1a26` - Dark variant
- `--wine-light` - Light variant for backgrounds
- `--bg` - Page background
- Standard Bootstrap colors for status indicators

### Fonts
- **DM Sans** - Primary UI font (headings, body)
- **Lora** - Serif font (literary/editorial content)

### Sub-brands
- **Forfatterskolen** - Main writing school
- **EasyWrite** - Simplified writing course brand
- **Indiemoon** - Self-publishing imprint / bookshop

## Environment Variables

Required env vars (do NOT commit actual values):

```
# App
APP_NAME, APP_ENV, APP_KEY, APP_DEBUG, APP_URL, APP_LIVE_URL, APP_SITE

# Database
DB_CONNECTION=mysql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Queue/Cache/Session
QUEUE_CONNECTION=database, CACHE_STORE=file, SESSION_DRIVER=file
SESSION_DOMAIN=.forfatterskolen.no  # Critical for cross-subdomain sessions

# Mail (use Resend, NOT Gmail SMTP)
MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_SCHEME
MAIL_FROM_ADDRESS, RESEND_API_KEY
MAIL_HOST_SITE, MAIL_PORT_SITE  # For direct SMTP from VPS

# Payments
VIPPS_CLIENT_ID, VIPPS_CLIENT_SECRET, VIPPS_SUBSCRIPTION, VIPPS_URL, VIPPS_MSN
VIPPS_CLIENT_ID_TEST, VIPPS_CLIENT_SECRET_TEST, VIPPS_SUBSCRIPTION_TEST, VIPPS_URL_TEST, VIPPS_MSN_TEST
SVEA_IDENTIFIER, SVEA_COUNTRY_CODE, SVEA_CURRENCY, SVEA_LOCALE
SVEA_CHECKOUTID, SVEA_CHECKOUT_SECRET
SVEA_CHECKOUTID_TEST, SVEA_CHECKOUT_SECRET_TEST
PAYPAL_USERNAME, PAYPAL_PASSWORD, PAYPAL_SIGNATURE

# Integrations
ANTHROPIC_API_KEY                    # Claude AI for drafts/feedback
HELPWISE_API_KEY, HELPWISE_API_SECRET
FIKEN_PERSONAL_API_KEY, FIKEN_API_URL, FIKEN_COMPANY_SLUG  # Accounting
ACTIVECAMPAIGN_URL, ACTIVECAMPAIGN_KEY
BIGMARKER_API_KEY, BIGMARKER_CHANNEL_ID
WISTIA_API_TOKEN
WHEREBY_API_KEY
CLOUDCONVERT_API_KEY
DROPBOX_APP_KEY, DROPBOX_APP_SECRET, DROPBOX_TOKEN, DROPBOX_REFRESH_TOKEN

# Social/Auth
FACEBOOK_APP_ID, FACEBOOK_APP_SECRET, FACEBOOK_ACCESS_TOKEN
FACEBOOK_AD_ACCOUNT_ID, FACEBOOK_PAGE_ID, FACEBOOK_WEBHOOK_VERIFY_TOKEN
GOOGLE_ADS_ID
META_PIXEL_ID
NOCAPTCHA_SECRET, NOCAPTCHA_SITEKEY
JWT_SECRET

# Tracking
TRACKING_ENABLED=true/false

# Community
COMMUNITY_FORUM_URL
```

## Local Development Environment

**Local dev runs in Docker.** The MySQL database is NOT exposed on the host — it lives inside the Docker network as a container called `mysql`. This means:

- Running `php artisan migrate` directly from Windows/host will FAIL with "getaddrinfo for mysql failed" because the host can't resolve the `mysql` container name
- All artisan commands, composer commands, npm commands etc. that need DB access must be run **inside** the app container

**Container names:**
- `forfatterskolen-master-app-1` — Laravel app
- `forfatterskolen-master-mysql-1` — MySQL 8.0

**Run commands inside the container:**
```bash
docker exec forfatterskolen-master-app-1 php artisan migrate
docker exec forfatterskolen-master-app-1 php artisan tinker
docker exec forfatterskolen-master-app-1 composer install
```

**Open a shell in the container** (for multiple commands):
```bash
docker exec -it forfatterskolen-master-app-1 bash
```

**Check what's running:**
```bash
docker ps
```

If `docker ps` shows nothing, Docker Desktop probably needs to be started first.

## Common Commands

### Development
```bash
composer dev              # Start server + queue + pail + mix (concurrently)
php artisan serve         # Laravel dev server only
npm run dev               # Compile assets (development)
npm run watch             # Watch + recompile on changes
npm run prod              # Compile assets (production, minified + service worker)
```

### Artisan
```bash
php artisan migrate                    # Run pending migrations
php artisan queue:listen --tries=1     # Process queue jobs
php artisan pail --timeout=0           # Tail logs in real-time
php artisan tinker                     # REPL
php artisan view:clear                 # Clear compiled views
php artisan cache:clear                # Clear application cache
php artisan config:clear               # Clear config cache
php artisan optimize                   # Cache config + routes
php artisan ide-helper:generate        # Generate IDE helper file
```

### Inbox/Email
```bash
php artisan inbox:poll --mark-read     # Poll IMAP for new emails (runs every minute)
```

### Deployment (Server)
```bash
git pull origin master
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run prod
php artisan queue:restart
```

## Integrations

| Service | Purpose | Config Key |
|---------|---------|------------|
| **Vipps** | Norwegian mobile payment (courses + bookshop) | `services.vipps` |
| **Svea Ekonomi** | Invoice/installment payment | `services.svea` |
| **PayPal** | International payments | `services.paypal` (via Omnipay) |
| **Fiken** | Norwegian accounting/invoicing | `services.fiken` |
| **Resend** | Transactional email delivery | `services.resend` |
| **Anthropic Claude** | AI draft replies, manuscript feedback | `services.anthropic` |
| **Helpwise** | Email CRM (being replaced by Inbox) | `services.helpwise` |
| **ActiveCampaign** | Email marketing automation | `services.activecampaign` |
| **BigMarker** | Webinar hosting | `services.big_marker` |
| **Whereby** | Video meeting rooms | `services.whereby` |
| **Wistia** | Video hosting/streaming | `services.wistia` |
| **Facebook/Meta** | Social login, lead ads, pixel, conversion API | `services.facebook`, `services.facebook_ads` |
| **Google Ads** | Conversion tracking | `services.google_ads` |
| **Dropbox** | File storage/backup | `services.dropbox` |
| **CloudConvert** | Document format conversion | `services.cloudconvert` |
| **reCAPTCHA** | Bot protection | `NOCAPTCHA_*` |
| **Bambora** | Legacy payment (may be inactive) | `services.bambora` |

## Scheduled Tasks (Kernel.php)

### Every Minute
- `inbox:poll --mark-read` - Poll IMAP for new emails (withoutOverlapping)
- `freecoursedelayedemail:command` - Process delayed free course emails

### Every 15 Minutes
- `community:sync-groups` - Sync course groups to community

### Every 30 Minutes
- `lockfinishedmanuscript:command` - Lock completed manuscript feedback

### Hourly
- `dropbox:refresh-token` - Refresh Dropbox OAuth token
- `ads:auto-stop` - Auto-stop underperforming ad campaigns
- `webinar:download-recordings` - Download webinar recordings

### Daily
| Time | Command | Purpose |
|------|---------|---------|
| 00:30 | `checkfikenpaymentdate:command` | Check Fiken payment dates |
| 01:00 | `coachingtimer:finalize` | Finalize coaching timer entries |
| 06:00 | `bookreminder:send` | Send book reading reminders |
| 06:30 | `sveadelivery:command` | Process Svea delivery reports |
| 07:00 | `autorenewreminder:command` | Auto-renewal reminders |
| 07:30 | `checksveaorder:command` | Check Svea order status |
| 07:30 | `checkfikencontact:command` | Sync Fiken contacts |
| 07:30 | `checkfikeninvoice:command` | Check Fiken invoices (morning) |
| 07:30 | `community:daily-news` | Post daily news to community |
| 08:00 | `dueinvoicecheck:command` | Check due invoices |
| 08:00 | `webinarpakkeexpiresinaweek:command` | Webinar package expiry warning |
| 08:00 | `courseemailout:command` | Course-related email sends |
| 08:00 | `invoiceduereminder:command` | Invoice due date reminders |
| 08:00 | `delayedemail:command` | Process delayed emails |
| 08:00 | `ads:update-stats` | Update ad campaign statistics |
| 08:00 | `contract:expiry-reminder` | Contract expiry reminders |
| 08:30 | `courseexpirationreminder:command` | Course expiration reminders |
| 08:30 | `checkexpiredcourse:command` | Deactivate expired courses |
| 08:30 | `invoicevippsefaktura:command` | Vipps eFaktura processing |
| 09:00 | `webinaremailout:command` | Webinar email notifications |
| 11:00 | `dontavailanything:command` | Availability check |
| 11:00 | `emails:inactive-nudge` | Nudge inactive users |
| 17:00 | `checkfikeninvoice:command` | Check Fiken invoices (afternoon) |
| 19:00 | `courseexpiresinamonth:command` | 1-month course expiry warning |
| 19:00 | `gotowebinarreminderday:command` | Webinar day-before reminder |
| 20:30 | `webinarscheduledregistration:command` | Process scheduled registrations |

### Weekly
- Monday 07:00: `emails:weekly-digest` - Weekly activity digest

## Gotchas

### CRITICAL
- **NEVER overwrite `routes/web.php`** with a local copy. Always merge changes carefully. This file has ~1455 route definitions across three domains.
- **`.env` was accidentally deleted once** - always back up before any operations that touch it. The file contains API keys and secrets that are difficult to recover.
- **`SESSION_DOMAIN`** must be set to `.forfatterskolen.no` (with leading dot) for cross-subdomain session sharing to work.

### Database Naming
- Table is `courses_taken` (NOT `course_taken`)
- Table is `email_template` (NOT `email_templates` or `templates`)
- Table is `shop_manuscripts_taken` (NOT `shop_manuscript_taken`)

### Routing & URLs
- `public/blog/` folder contains static files that conflict with the `/blog` Laravel route. The static folder takes precedence at the web server level.
- Svea callback route (`/svea-callback`) is defined outside domain groups so it works for server-to-server callbacks.

### Middleware
- **Learner middleware allows roles 1, 2, AND 3** (not just role 1). Editors and admins can also access learner routes.
- **Magic links must support roles 1, 2, 3** - all user types can use passwordless login.

### Queue & Workers
- Queue worker needs an auto-restart script on the server (it can die silently).
- Use `queue:listen` for development, `queue:work` with supervisor for production.

### Email
- **Gmail SMTP is deprecated** - use Resend for transactional email.
- MAIL_HOST_SITE / MAIL_PORT_SITE are for direct SMTP from the VPS (Domeneshop).

### Build
- `npm run prod` generates a service worker via Workbox (PWA). Dev builds skip this.
- Vue 2 is used throughout - do NOT upgrade components to Vue 3 syntax.

### Files & Storage
- Dropbox token needs hourly refresh (scheduled command handles this).
- Large file uploads go through signed URLs (`api.v1.files.upload`).

### Lessons Table
- Column is `content` (NOT `description`) for lesson body text
- `description` is in the model's `$fillable` but the actual DB column is `content`

### Coaching
- `CoachingTimerManuscript` has `suggested_date` (JSON), `preparation_file`, `preparation_notes`
- `EditorTimeSlot` stores editor availability (date, start_time, duration)
- `CoachingTimeRequest` links manuscripts to time slots (status: pending/accepted/declined)
- Coaching preparation page: `/account/coaching-timer/{id}/prepare`

### Course Builder (Kursbygger AI)
- Uses Claude Opus via Guzzle POST to `api.anthropic.com/v1/messages`
- Builds modules one at a time to avoid timeouts
- Markdown content is converted to HTML via `Str::markdown()` before saving to lessons
- Packages are NOT auto-created — add manually in course editor after creation
- Frontend stores chat in `localStorage` key `cb_messages`

### Community
- `isAdmin()` in `CommunityForumController` checks both `user->role == 1` AND `profile->badge === 'admin'`
- Admin sees all course groups filtered by `status=1` AND `show_in_course_groups=1`
- `course_group_id` in posts table references `courses.id`, not `course_groups.id`
- AI discussion generator in admin: `admin.community.discussions.generate-ai` and `store-ai`
- Course group dropdown in admin posts uses `Course` model (not `CourseGroup`) for `course_group_id`

### Landing Pages
- `/skriv-ditt-liv` — standalone Blade template at `resources/views/landing/skriv-ditt-liv.blade.php` (noindex)
- Uses `Route::view()` — no controller needed

### Editor Portal
- "Mine elever" page at `/mine-elever` — extension requests, active manuscripts, send reminders
- Editor can approve/decline extension requests → email sent to student automatically
- Manuscript lock toggle on dashboard — editors lock manus when reading
- `AdminHelpers::editorPageList()` defines sidebar menu items
- DataTables language set to Norwegian in `editor/layout.blade.php`
- `formatToYMDtoPrettyDate()` returns `d.m.Y kl. H:i` (Norwegian format)
- Årskurs/Påbygg excluded from "Hvor mange oppgaver kan du ta?" settings

### Assignments
- `auto_assign_editor` field on `assignments` table — auto-assigns editor on student submission
- When enabled, `editor_id` and `editor_expected_finish` set automatically at submission time
- Extension requests: student requests → admin/editor approves → deadline updated + email sent

### Inbox
- IMAP poller saves attachments to `storage/app/inbox-attachments/`
- Attachments stored as JSON array in `inbox_messages.attachments` column
- Download route: `admin.inbox.attachment`

### Webinars
- `webinarscheduledregistration:command` runs at 20:30 (day before) AND 07:00 (same day fallback)
- Fallback checks all webinars starting today with <50% registrants and registers missing students
- Registration uses BigMarker API PUT to `/api/v1/conferences/register`
- `webinar_scheduled_registrations` table controls when registration happens

### Email Deduplication
- `CourseEmailOut` tracks `$sentToday[user_id|subject]` to prevent duplicate emails
- Students on multiple courses only get one email per subject per run

## Recent Work (April 2026)

- **Regressions fra 8. april — blank password reset + doble webinar-mailer** - To regresjoner ble oppdaget dagen etter user-merge og BS5-arbeidet ble pushet. **Bug 1: blank password reset-tab på `/auth/login`**: Den gamle login-siden (`resources/views/frontend/auth/login.blade.php`) bruker fire tab-paneler (login/register/passwordreset/password-change) som var skrevet med Bootstrap 3-syntaks `class="tab-pane fade in active"`. BS5 bruker `.show` i stedet for `.in` for å markere at en fade-tab-pane skal være synlig — uten `.show` matcher BS5-regelen `.fade:not(.show) { opacity: 0 }` og hele content-området blir usynlig selv om HTML-en er der. Kunder som klikket "Glemt passordet?" eller "Register deg" fra checkout-sidene (~25 lenker fortsatt peker hit) endte på en helt blank side. Fikset ved å bytte alle fire `in active` til `show active`. Den nye `login-new.blade.php`-siden (vist når `?t=` ikke er med i URL-en) var ikke berørt — den har sin egen card-style. Commit `b934a694`. **Bug 2: doble webinar-reminder-mailer på torsdager kl 09**: Mentormøte-deltakere fikk samme `webinaremailout:command`-e-post to ganger. Root cause var i `UserMergeService` (commit `41851b23`): tjenesten har en `$uniqueUserIdColumns`-liste for tabeller med UNIQUE-constraint på `user_id` (profiles, user_preferences osv.) der secondary's rad slettes før UPDATE for å unngå duplicate-key error — men `courses_taken` har INGEN hard unique-constraint på `(user_id, package_id)`, så når sven mergere to brukere som BEGGE hadde kjøpt samme mentor-pakke, så kjørte UPDATE bare gjennom og primary endte med to identiske `courses_taken`-rader. `WebinarEmailOutCommand` har ingen deduplisering, så den looper over begge radene og dispatcher AddMailToQueueJob to ganger med samme adresse + subject. Tre koordinerte fikser: **(1)** `WebinarEmailOutCommand` har nå et `$sentTodayKeys`-array på tvers av alle WebinarEmailOut-rader i samme cron-kjøring, indeksert på `lowercased(email)|subject` (samme mønster som CourseEmailOut bruker). Også null-check på `courseTaken->user` for å unngå crash på broken FK-er. **(2)** `UserMergeService` har nå et `$compositeUniqueColumns`-map for tabeller der `(user_id + en annen kolonne)` må være logisk unik selv uten DB-constraint — courses_taken (package_id), shop_manuscripts_taken (shop_manuscript_id), webinar_registrants (webinar_id), assignment_submissions (assignment_id). Steg "1b" i `merge()` finner alle (other_col)-verdier som primary allerede har, og sletter secondary's tilsvarende rader før UPDATE. **(3)** Ny artisan-kommando `coursestaken:dedupe` med `--dry-run`-flag som walker `courses_taken`, finner `(user_id, package_id)`-grupper med >1 rad, beholder den nyeste, sletter resten. Må kjøres én gang på prod for å rense opp eksisterende duplikater fra merges som ble gjort før fix #2 var i pipeline. Commit `9234f6f1`.

- **BigMarker webinar registration recovery (env-katastrofen del 2)** - Tre webinarer som skulle starte 09.04.2026 kl 17:00 (Årskurs 2026, Barnebokkurs, Årskurs Høst) viste 0-8 påmeldte i BigMarker-dashboardet selv om vår DB hadde alle elever som registrert. Root cause var todelt: **(1)** `BIGMARKER_REGISTER_LINK`-env-variabelen manglet på produksjon etter env-katastrofen tidligere i april — `WebinarScheduledRegistrationCommand` kalte curl med tom URL (`config('services.big_marker.register_link')` returnerte `null`) og feilet stille på alle 142 elever. CronLog viste "running" og "done running" men ingen ble faktisk registrert. Fikset ved å legge til hardkodet fallback i `config/services.php`: `'register_link' => env('BIGMARKER_REGISTER_LINK', 'https://www.bigmarker.com/api/v1/conferences/register')`. Samme for `show_conference_link`. Etter env-fix gjenopprettet to av tre webinarer (3 → 33 og 8 → 118 påmeldte). **(2)** Webinar 3293 (Årskurs Høst) viste fortsatt 0 fordi `link`-feltet i DB inneholdt en gammel 12-sifret numerisk BigMarker-ID (`046926306049`) fra en deprecated oktober-2025-konferanse. **Viktig oppdagelse**: BigMarker har migrert fra 12-sifret numerisk ID-format til 12-tegns hex ID-format (f.eks. `e92ff2abfe03`, `7e8eea92d0d1`). Gamle numeriske ID-er får fortsatt HTTP 200 fra `/api/v1/conferences/register`, men de ruter registreringer til DØDE arkiverte konferanser og returnerer `conference_url` med den opprinnelige slug-en fra årevis tilbake (i vårt tilfelle `rskurswebinar-h-st-09-10-2025` — fra oktober 2025!). Cron-en lagrer dermed gyldig-utseende men totalt feil join_urls i `webinar_registrants.join_url`. `/api/v1/conferences/show?id=...` returnerer 404 for både gamle numeriske og nye slug-baserte identifikatorer, så API-lookup er upålitelig. **Webinar 3293 ble reddet** ved å laste opp en CSV med alle 43 elever manuelt via BigMarker UI's "Manage Registrations → Add Registrants → Import from CSV". BigMarker sender automatisk bekreftelses-e-post med riktig join-lenke ved bulk-upload. **Lesson learned**: når et webinar feiler, sjekk først om `webinar->link` er 12-sifret numerisk (gammelt format, må byttes ut) vs 12-tegns hex (nytt format, OK). **Spesielt nasty quirk**: BigMarker-dashboardets "Webinar ID:"-felt viser noen ganger en legacy-ID fra en gammel forfar-konferanse i stedet for den faktiske API-ID-en — dette skjer typisk for kopierte webinarer (Kristine kopierer ofte fra eldre webinarer). Den ekte hex-ID-en må finnes i Embed code eller URL-baren ved å redigere webinaret. Commit `cc657521`.

- **Private per-admin inboxes + multi-IMAP polling** - Each admin user can now have their own private inbox in the inbox CRM, routed by recipient email. New `private_to_user_id` column on `inbox_conversations` (nullable, indexed) hides private conversations from other admins via the `visibleToUser(userId)` Eloquent scope, enforced in every query in `InboxService` (getConversations, getConversation, getStats, getInboxes, getMentionsCount). The conversation-level guard returns 403 even on direct URL access. New `config/inbox.php` defines an `accounts` array of IMAP mailboxes to poll — `PollInboxEmails::handle()` loops over them and skips accounts with empty credentials gracefully. Each account auto-resolves its `private_to_user_id` by matching the configured `inbox_email` against active admin user emails. Default config has two accounts: shared `post@forfatterskolen.no` (forfatterskolen3 mailbox) and `sven.inge@forfatterskolen.no` (requires `IMAP_USERNAME_SVEN`/`IMAP_PASSWORD_SVEN` in .env + new mailbox in Domeneshop). For the public mailbox, `determineInboxRouting()` still parses To/Cc to catch admin emails received via aliases. AI is unchanged — same 12 tools, same knowledge base, same signature for every inbox.
- **Service worker cache crisis + login-help banner** - A customer (Bridgitt S. Lee) was completely locked out of her account because her browser's installed Workbox service worker was serving stale precached `/js/app.js` and `/css/app.css` from months ago. No amount of hard refresh, incognito mode, or manual clearing helped. Likely affecting many silent users. Fixed in three layers: (1) `public/service-worker.js` is now a kill-switch that on activation calls `self.clients.claim()`, deletes every Cache Storage key, unregisters itself, and navigates every open tab to force a fresh reload. (2) Inline cleanup script in `<head>` of every layout (extracted to reusable `partials/sw-cleanup-script.blade.php`) that runs on every page load with a `localStorage.sw_cleanup_v3` flag, listing all `serviceWorker.getRegistrations()`, unregistering them, deleting all caches, and reloading once. HTML is not precached so this reaches every stuck user. (3) Visible red `partials/login-help-banner.blade.php` at the very top of every page with a "Tøm cookies og cache" pill button — when clicked, deletes all cookies for `forfatterskolen.no` (and parent `.forfatterskolen.no`), clears localStorage/sessionStorage/SW/Cache Storage, and redirects to `/?fresh=<timestamp>`. Browser Same-Origin Policy guarantees only our own data is touched. Banner has × close button with 24h `loginHelpBanner_dismissed` localStorage suppression. Cleanup + banner are now included in frontend/layout, editor/layout, AND all standalone auth views (editor_login, forgot-password, passwordreset for both editor and backend). SW registration is temporarily DISABLED in both layouts via commented-out code — re-enable in ~2 weeks when stuck users are confirmed clean, with `{ updateViaCache: 'none' }` and `reg.update()` on load to prevent recurrence.
- **Agentic AI in inbox (Phase 1 complete)** - The inbox AI now suggests concrete actions alongside text drafts via Anthropic's tool_use API. 12 tools total: 4 read-only lookups (`get_user_courses`, `get_invoice_status`, `get_assignment_status`, `get_upcoming_webinars`) and 8 action tools (`send_login_link`, `send_password_reset`, `add_internal_note`, `extend_assignment_deadline`, `approve_extension_request`, `register_for_webinar`, `assign_editor_to_manuscript`, `mark_conversation_done`). Each tool is a class under `app/Services/AiTools/Tools/{Lookup,Action}/` implementing `AiToolInterface`. `AiToolRegistry` exposes them to Anthropic as tool definitions, `AiToolExecutor` runs them with pessimistic lock (idempotency), validation, and audit logging to the new `ai_tool_actions` table. Nothing auto-executes — AI suggests, admin clicks. Inline buttons render under AI drafts in `show.blade.php` with color-coded per-tool icons. New audit page at `/admin/ai-actions` with filters on status/tool/date. Daily cron `ai-tools:expire-old` marks suggestions older than 7 days as expired. `send_login_link` is role-aware (elever → forfatterskolen.no, redaktører → editor.forfatterskolen.no). Refactored `AssignmentController::decideExtension` into `AssignmentExtensionService` so both admin UI and `ApproveExtensionRequestTool` share the same logic. Config in `config/ai_tools.php` allows disabling individual tools. Commits `4a2b562c` through `ab7657e8`.
- **Inbox AI prompt improvements** - Multiple prompt refinements after real-world customer testing: injected today's date so AI can compute "X days until deadline"; added active discount codes as a fact-check list (prevents AI from promising expired coupons); mandated markdown-style `[tekst](url)` links with automatic conversion to clickable HTML via `InboxBodyFormatter`; stripped markdown bold/italic/headers that were showing as raw asterisks; `EmailQuoteStripper` removes Gmail/Outlook/Apple Mail quoted history so AI focuses on the new question, while a separate "full email body" section preserves quoted context for fact-lookup; `getMessageHistory` now fetches the last 10 messages instead of the first 10 and bumps per-message limit to 800 chars; hardcoded course IDs with /course/{id} URL mapping so AI never asks "which course?" when it's already in the thread; signature changed from "Skrivevarm hilsen" to "Ha en fin dag! / Mvh {name}"; encouraged unicode emojis (📚 ✍️ 🎉 etc). Added strict compliance rule: NEVER offer manual faktura/eFaktura outside checkout — all orders must go through `/course/{id}` checkout so angrerettskjema and legal docs are generated correctly. Knowledge for "Bestill nå, betal senere" distinguishes between instant-access courses (start immediately) and group courses with fixed start dates (place secured, course starts on announced date).
- **Production deploy automation** - `bash deploy.sh` script wraps the full production deploy: `git pull`, `php composer.phar dump-autoload --optimize`, `php artisan migrate --force`, `view:clear` + `route:clear` + `config:clear` + `cache:clear`, and `queue:restart`. Solves a class of bugs where pushing new PHP classes without running `composer dump-autoload` would leave the route cache referencing non-autoloaded controllers and take down admin. The Nordic Hosting cPanel server uses optimized classmap autoload, so new classes MUST be added to the classmap after git pull. Local dev runs in Docker (`forfatterskolen-master-app-1` + `forfatterskolen-master-mysql-1`) — artisan commands must be run inside the container because MySQL isn't exposed on the host.
- **AI knowledge system for inbox** - Two-source curated knowledge that the inbox AI consults before generating drafts: `docs/ai-knowledge.md` (version-controlled markdown with FAQ, technical workarounds like cache/cookie clearing, recent fixes) and `ai_known_issues` table managed via `/admin/ai-knowledge` (admin UI to add ad-hoc bugs with severity/category/workaround). Both sources are merged into the AI prompt under "AKTUELL KUNNSKAP OG TEKNISKE FORHOLD". Active issues count badge in sidebar. Built in `HelpwiseReplyAiService::getKnowledgeContext()`.
- **Password reset page redesign** - Old Bootstrap 3 panel classes rendered white-on-white after BS5 migration making the form look greyed out (especially on Mac). Replaced with the same clean card style used by thank-you pages — wine-red icon, visible inputs, clear button.
- **AI draft signature deduplication** - Two related bugs caused "Forfatterskolen" to appear twice in inbox AI signatures. Fixed: (1) `getSenderName()` no longer falls back to "Forfatterskolen" when no auth user exists (webhook context), so the personal-name line is omitted; (2) `InboxService::sendReply()` now skips appending a signature if the body already contains one (e.g. when using an AI draft as-is).
- **PWA with push notifications** - Service worker, `PushSubscription` model, web push API
- **Inbox system replacing Helpwise** - Full email CRM with IMAP polling, AI draft replies, conversation assignment, canned responses, auto-replies, attachment support
- **Community with course groups** - Course-based groups, discussions, posts with reactions, direct messages, daily news sync, AI-generated discussions
- **Blog redesign** - Updated blog views and routing
- **Svea callback** - Server-to-server payment push callback
- **Deadline extension requests** - `AssignmentExtensionRequest` model, admin + editor portal approval with email notifications
- **Ad OS** - Ad campaign optimization system with Facebook/Google integration
- **Indiemoon bookshop** - API for book orders, author profiles, e-book downloads
- **Publishing pipeline** - `Publication` model, manuscript-to-book workflow
- **AI manuscript feedback** - `ManuscriptFeedbackAiService` for AI-assisted editor feedback
- **Coaching system** - Session-based coaching with timer, preparation upload, suggest-your-own-time (shown in editor portal), redesigned booking UX
- **Kursbygger AI** - Course builder with Claude Opus, module-by-module generation, auto-create course with lessons, markdown→HTML conversion
- **Landing pages** - `/skriv-ditt-liv` memoir course page with real student photos and 3-tier pricing
- **Login fixes** - Google OAuth field format update, session handling for Login-as-user
- **Editor portal redesign** - New assignments, archive, settings, editors note pages with modern UX. "Mine elever" for extension management and student reminders. Manuscript lock toggle. All Norwegian text.
- **Auto-assign editor** - `auto_assign_editor` flag on assignments for automatic editor assignment on submission
- **Webinar fallback registration** - Same-day safety net at 07:00 for missed scheduled registrations
- **Email deduplication** - Prevents students on multiple courses from receiving duplicate emails
- **Inbox attachments** - IMAP poller extracts and stores email attachments with download support

- **SEO-optimalisering 78→92** — Massiv SEO-forbedring på én dag. (1) Alle 530+ Blade views konvertert fra dobbel `<title>`-tag (layout + child) til `@yield('page_title', $meta_title)` med inline `@section('page_title', ...)`. Block-form `@section()...@endsection` fungerer IKKE med `@yield` i denne Laravel-versjonen — bruk alltid inline-form. (2) 130+ ikke-offentlige sider (checkout, auth, learner-portal, community, selvpublisering) fikk noindex via PHP-logikk direkte i layout (`$noindexPaths`-array sjekket mot `request()->path()`), fordi `@section('robots')`/`@yield('robots')` heller ikke fungerte. (3) 6.100 linjer inline CSS ekstrahert til 12 eksterne filer i `public/css/pages/` (navbar, footer, hjemmeside, kurssider, blogg, kontakt etc.). (4) H2-er på kurssider gjort dynamiske med `$course->title` og overflødige H2→H3. (5) Blogginnlegg fikk visuelt skjult H2 + dynamisk meta_desc fra innhold. (6) Google Snippets optimalisert: "(Øyeblikkelig tilgang)" strippet fra kurs-SEO-titler, korte titler forlenget, lange forkortet. Viktig lærdom: `.gitignore` hadde `/public/*` som blokkerte `public/css/pages/` — måtte whiteliste.
- **Inbox redesign — visuell hierarki + autosvar + regler** — Inbox-listen har nå prioritetsfarge (rød/oransje/grå venstrekant), tag-badges, responstid-indikator (⚡<2t/⏱️<8t/⚠️>8t), snooze-ikon, og kategori-sidebar med teller. Quick-actions (lukk/snooze) rett fra listen. Nye controller-metoder: `snooze()`, `updatePriority()`, `updateTags()`, `dismissMention()`. `/inbox` uten filtre redirecter nå til "Mine" automatisk. Lukk/snooze går tilbake til forrige liste (session-basert return-URL). "Bekreft nevning"-knapp fjerner brukerens mention fra kommentarer. Elevinfo-panelet viser klikkbare lenker til kurs, manusutvikling og prosjekter. Autosvar-admin (`/admin/inbox/auto-replies`) med CRUD for InboxAutoReply — type (bekreftelse/fravær), AI-tilpasning, forsinkelse. Regler-admin (`/admin/inbox/rules`) for nøkkelord-basert auto-tildeling med prioritet/kategori. Kontaktbok med autocomplete i compose-modal via `/learners/search`.
- **Auto-assign forbedret** — Tidligere ble samtaler fra eksisterende kunder stående utildelt. Nå tildeles de til teammedlemmet som SIST svarte kunden. Fallback: alltid `default_user_id` (aldri null). Ingen samtaler blir stående utildelt.
- **Ad OS auto-pilot aktivert** — 7 schedulerte jobs: SyncAdCampaigns (30 min), SyncAdMetrics (15 min), EvaluateAdRules (15 min), GenerateRecommendations (1t), ExecuteApprovedActions (5 min), GenerateAdSummary (kl 22). Modus: supervised (lavrisiko auto-utføres, høyrisiko krever godkjenning). Config: `ADS_OS_ENABLED=true`, `auto_apply_enabled=true`. Dashboard med Chart.js: spend/leads dual-axis graf, CPA-trend, budsjett-doughnut, kampanjeliste. API-endpoint `/admin/ads/api/metrics`. Google Ads Developer Token søkt (Basic Access, venter godkjenning). Google Ads config klargjort med developer_token + OAuth-felter i `config/services.php`.
- **Faktura bulk-handlinger** — Ny "Endre alle forfallsdatoer"-knapp på elevsiden: velg dag (1-28), oppdaterer alle UBETALTE fakturaer lokalt og i Fiken via API. Ny "Krediter alle ubetalte"-knapp: krediterer i Fiken med ett klikk (dobbel bekreftelse). Begge bruker `Http::withHeaders()` i stedet for curl.
- **Fiks: dobbel fakturering ved auto-fornyelse** — `CheckAutoRenewCourses` middleware opprettet duplikat-fakturaer for Mentormøter (kurs 17) når brukeren hadde flere `courses_taken`-rader. Bug: ytre foreach loopet over alle coursesTaken, og for hver rad ble det opprettet en Fiken-faktura — selv om inner loop satte `renewed_at` på alle. Tre lag med beskyttelse: (1) `$invoiceCreatedThisRequest`-flag + break etter første faktura, (2) `Invoice::whereDate(today())`-sjekk hindrer duplikat ved flere requests, (3) Eksisterende `$alreadyRenewed`-sjekk forsterket.
