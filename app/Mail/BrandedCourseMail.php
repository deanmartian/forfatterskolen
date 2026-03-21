<?php

namespace App\Mail;

use App\EmailOut;
use App\User;
use App\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BrandedCourseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailOut;
    public $user;
    public $course;
    public $templateData;

    public function __construct(EmailOut $emailOut, User $user, Course $course)
    {
        $this->emailOut = $emailOut;
        $this->user = $user;
        $this->course = $course;
        $this->templateData = $emailOut->template_data ?? [];
    }

    public function build()
    {
        $viewMap = [
            'welcome' => 'emails.branded.welcome',
            'module_available' => 'emails.branded.module-available',
            'assignment_available' => 'emails.branded.assignment-available',
            'assignment_reminder' => 'emails.branded.assignment-reminder',
            'assignment_deadline' => 'emails.branded.assignment-deadline',
            'feedback_ready' => 'emails.branded.feedback-ready',
            'weekly_update' => 'emails.branded.weekly-update',
        ];

        $templateType = $this->emailOut->template_type;
        $view = $viewMap[$templateType] ?? null;

        if (!$view) {
            // Fallback: use existing mail_to_queue view with message content
            return $this->from($this->emailOut->from_email ?: 'post@forfatterskolen.no', $this->emailOut->from_name ?: 'Forfatterskolen')
                ->subject($this->emailOut->subject)
                ->view('emails.mail_to_queue', [
                    'email_message' => $this->emailOut->message,
                    'track_code' => null,
                ]);
        }

        $data = $this->buildTemplateData();

        return $this->from($this->emailOut->from_email ?: 'post@forfatterskolen.no', $this->emailOut->from_name ?: 'Forfatterskolen')
            ->subject($this->emailOut->subject)
            ->view($view, $data);
    }

    protected function buildTemplateData(): array
    {
        $encode_email = encrypt($this->user->email);
        $loginUrl = route('auth.login.email', $encode_email);
        $portalUrl = $loginUrl;

        $tplData = is_array($this->templateData) ? $this->templateData : (json_decode($this->templateData, true) ?? []);
        $customMessage = trim($this->emailOut->message ?? '');
        $data = array_merge($tplData, [
            'firstName' => $this->user->first_name,
            'courseName' => $this->course->title,
            'portalUrl' => $portalUrl,
            'loginUrl' => $loginUrl,
            'customMessage' => $customMessage ?: null,
        ]);

        // Calculate progression for module_available
        if ($this->emailOut->template_type === 'module_available') {
            $totalLessons = $this->course->lessons()->count();
            if (!empty($data['modules'])) {
                // Kombinert e-post — bruk høyeste modul-ordre for progresjon
                $maxOrder = max(array_column($data['modules'], 'order'));
                $data['progressPercent'] = $totalLessons > 0 ? round(($maxOrder / $totalLessons) * 100) : 0;
            } else {
                $lessonOrder = $data['lessonOrder'] ?? 1;
                $data['progressPercent'] = $totalLessons > 0 ? round(($lessonOrder / $totalLessons) * 100) : 0;
            }
            $data['totalLessons'] = $totalLessons;
        }

        return $data;
    }
}
