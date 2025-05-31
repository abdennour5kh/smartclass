<?php

namespace App\Notifications;

use App\Models\Justification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JustificationReviewed extends Notification implements ShouldQueue
{
    use Queueable;

    protected Justification $justification;

    public function __construct(Justification $justification)
    {
        $this->justification = $justification;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $date = $this->justification->session->session_date;
        $module = $this->justification->session->classe->module->name;
        $status = ucfirst($this->justification->status);
        $reviewer = $this->getReviewer();

        return (new MailMessage)
            ->subject('Your Justification Has Been Reviewed')
            ->greeting('Hello!')
            ->line("Your justification for the session on {$date} in {$module} has been reviewed by {$reviewer}.")
            ->line("Status: {$status}")
            ->line('Visit SmartClass for more information.');
    }

    public function toArray(object $notifiable): array
    {
        $date = $this->justification->session->session_date;
        $module = $this->justification->session->classe->module->name;
        $status = ucfirst($this->justification->status);
        $reviewer = $this->getReviewer();

        return [
            'message' => "Your justification for the session on {$date} in {$module} has been reviewed by {$reviewer}.",
            'status' => $status,
            'session_date' => $date,
            'reviewed_by' => $reviewer,
        ];
    }

    protected function getReviewer(): string
    {
        $teacher = $this->justification->teacher_decision;
        $admin = $this->justification->admin_decision;

        return match (true) {
            $teacher !== '2' && $admin !== '2' => 'Teacher & Administration',
            $teacher !== '2' => 'Teacher',
            $admin !== '2' => 'Administration',
            default => 'System',
        };
    }
}
