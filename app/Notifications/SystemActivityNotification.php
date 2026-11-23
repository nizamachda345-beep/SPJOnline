<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemActivityNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $icon;
    public $type;
    public $additionalData; // <-- Tambahan variabel untuk data di balik layar

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $icon = 'fa-info-circle', $type = 'info', array $additionalData = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->icon = $icon;
        $this->type = $type;
        $this->additionalData = $additionalData; // <-- Tangkap data tambahan
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Menyimpan notifikasi langsung ke database
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return array_merge([
            'title' => $this->title,
            'message' => $this->message,
            'icon' => $this->icon,
            'type' => $this->type,
        ], $this->additionalData);
    }
}
