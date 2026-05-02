<?php

namespace App\Notifications;

use App\Models\SmtpSetting;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Task $task) {}

    public function via(object $notifiable): array
    {
        $smtp = SmtpSetting::getActive();
        if ($smtp) {
            return ['mail', 'database'];
        }
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $smtp = SmtpSetting::getActive();
        if ($smtp) {
            config([
                'mail.mailers.smtp.host' => $smtp->mail_host,
                'mail.mailers.smtp.port' => $smtp->mail_port,
                'mail.mailers.smtp.username' => $smtp->mail_username,
                'mail.mailers.smtp.password' => $smtp->mail_password_decrypted,
                'mail.mailers.smtp.encryption' => $smtp->mail_encryption,
                'mail.from.address' => $smtp->mail_from_address,
                'mail.from.name' => $smtp->mail_from_name,
            ]);
        }

        return (new MailMessage)
            ->subject('Task Assigned: ' . $this->task->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned a new task:')
            ->line('**' . $this->task->title . '**')
            ->line('Project: ' . $this->task->project->name)
            ->line('Priority: ' . ucfirst($this->task->priority))
            ->action('View Task', url("/projects/{$this->task->project_id}/tasks/{$this->task->id}"))
            ->line('Thank you for using PMO!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'project_name' => $this->task->project->name,
            'message' => "You were assigned to task: {$this->task->title}",
        ];
    }
}
