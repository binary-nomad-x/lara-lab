<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeTenantNotification extends Notification
{
    use Queueable;

    protected $tenant;

    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Welcome to Nexus EIAMS - ' . $this->tenant->name)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Thank you for choosing Nexus EIAMS for your Enterprise Inventory management.')
                    ->line('Your tenant account for ' . $this->tenant->name . ' has been successfully setup.')
                    ->action('Go to Dashboard', url('/dashboard'))
                    ->line('Thank you for using our platform!');
    }

    public function toArray($notifiable): array
    {
        return [
            'tenant_id' => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
        ];
    }
}
