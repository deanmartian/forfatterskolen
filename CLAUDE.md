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

### Landing Pages
- `/skriv-ditt-liv` — standalone Blade template at `resources/views/landing/skriv-ditt-liv.blade.php` (noindex)
- Uses `Route::view()` — no controller needed

## Recent Work (April 2026)

- **PWA with push notifications** - Service worker, `PushSubscription` model, web push API
- **Inbox system replacing Helpwise** - Full email CRM with IMAP polling from Domeneshop, internal comments, AI draft replies using Claude, conversation assignment, canned responses, auto-replies
- **Community with course groups** - Course-based groups, discussions, posts with reactions, direct messages, daily news sync, AI-generated discussions
- **Blog redesign** - Updated blog views and routing
- **Svea callback** - Server-to-server payment push callback
- **Deadline extension requests** - `AssignmentExtensionRequest` model for students requesting deadline changes
- **Ad OS** - Ad campaign optimization system with Facebook/Google integration
- **Indiemoon bookshop** - API for book orders, author profiles, e-book downloads
- **Publishing pipeline** - `Publication` model, manuscript-to-book workflow
- **AI manuscript feedback** - `ManuscriptFeedbackAiService` for AI-assisted editor feedback
- **Coaching system** - Session-based coaching with timer, preparation upload, suggest-your-own-time (shown in editor portal), redesigned booking UX
- **Kursbygger AI** - Course builder with Claude Opus, module-by-module generation, auto-create course with lessons, markdown→HTML conversion
- **Landing pages** - `/skriv-ditt-liv` memoir course page with real student photos and 3-tier pricing
- **Login fixes** - Google OAuth field format update, session handling for Login-as-user
