<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TajmixNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $title;
    public $body;
    // public $firebaseTokens;
    public $image;

    public $editional_data;


    public function __construct(
        $title = null,
        $body = null,
        $image = null,
        $editional_data = null,
        // $firebaseTokens = null,
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->image = $image;
        // $this->firebaseTokens = $firebaseTokens;
        $this->editional_data = $editional_data;
    }



    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toFcm(object $notifiable)
    {
        // notification: new FcmNotification(
        //     title: $this->title,
        //     body: $this->body,
        //     image: $this->image,
        // )

        // "data" is necessary only for andorid
        // cause after turning internet on
        // Android shows only last notification
        // that is why "data" is necessary
        $notification = (new FcmMessage())
            ->data([
                'title' => $this->title,
                'body' => $this->body,
                'image' => $this->image,
                ...$this->editional_data,
            ])
            ->custom([
                'android' => [
                    'priority' => 'high',
                ],
                // notification will be sent directly to iOS system
                // so, I did not write any logic for iOS in Flutter
                // cause firebase_messaing package handles that by itself
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $this->title,
                                'body' => $this->body,
                            ],
                            'mutable-content' => 1,
                            'sound' => 'default',
                            "content-available" => 1,
                        ],
                    ],
                ],
            ]);

        return $notification;
    }



    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
