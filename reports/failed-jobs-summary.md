# Failed job failure modes (top 3)

This summary captures the three most common failure modes seen in the code paths that enqueue email jobs and that can lead to failed queue entries. The fixes in this patch focus on making password reset email delivery resilient without relying on the queue worker.

## 1) Queued mail when no worker is running

Large parts of the application dispatch email work to the queue (for example, the mail queue job sends messages via `AddMailToQueueJob`). If the queue worker is down or misconfigured, these jobs will be retried and can end up in `failed_jobs`, resulting in missing email delivery until they are retried.【F:app/Jobs/AddMailToQueueJob.php†L13-L68】

## 2) Missing attachment paths in queued mail

Queued mail supports attachments. When a job references an attachment that is missing or not accessible in the runtime environment, `attach()` will throw and the job will fail. This is a frequent source of failed jobs when files are deleted or moved after enqueuing.【F:app/Mail/AddMailToQueueMail.php†L33-L61】

## 3) SMTP configuration or transport issues

The default mailer is SMTP, which depends on environment-provided host/port/credentials. If these values are missing or invalid (or the SMTP server is unreachable), mail sends will fail and queued jobs will be recorded as failed.【F:config/mail.php†L16-L53】
