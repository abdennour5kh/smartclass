<?php

namespace App\Notifications;

use App\Models\Justification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JustificationSubmitted extends Notification
{
    use Queueable;

    public $justification;

    public function __construct(Justification $justification)
    {
        $this->justification = $justification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $date = $this->justification->session->session_date;
        $module = $this->justification->session->classe->module->name;
        $_firstName = $this->justification->student->first_name;
        $_lastName = $this->justification->student->last_name;
        $studentName = $_firstName . ' ' . $_lastName;

        return (new MailMessage)
                    ->subject('New Justification Submitted')
                    ->greeting('Hello!')
                    ->line("New Justification request was submitted by {$studentName} for {$module} on ({$date})")
                    ->line("you can review this request on your Inbox center in SmartClass")
                    ->line("Thank you for using SmartClass.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $date = $this->justification->session->session_date;
        $module = $this->justification->session->classe->module->name;
        $_firstName = $this->justification->student->first_name;
        $_lastName = $this->justification->student->last_name;
        $studentName = $_firstName . ' ' . $_lastName;

        return [
            'message' => "{$studentName} submitted a justification for {$module} ({$date}).",
            'session_date' => $date,
        ];
    }
}
