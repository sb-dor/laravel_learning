<?php

namespace App\Http\Controllers\Messaging;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Notifications\RoutesNotifications;
use Illuminate\Support\Facades\File;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Intervention\Image\Facades\Image;

class FirebaseMessagingController extends Controller
{
    use RoutesNotifications;


    private $path_for_firebase_key = '/app/firebase_config.json';
    private $urlToImage = '/api/notification/image/';

    private function randd()
    {
        return rand(0, pow(10, 10));
    }

    // sends notification to the topic named "all"
    public function send_all(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'test'    => 'required|boolean',
            'body'    => 'nullable|string|max:1000',
        ]);

        $topic_name = "all";

        return $this->notify($request, $validated, $topic_name);
    }

    // sends notification to the topic named "city_{id}".
    // Id is the city's id
    public function send_to_city(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|integer|exists:cities,id',
            'test'    => 'required|boolean',
            'title'   => 'required|string|max:255',
            'body'    => 'nullable|string|max:1000',
        ]);

        $topic_name = "city_{$validated['city_id']}";

        return $this->notify($request, $validated, $topic_name);
    }

    private function notify(Request $request, array $validated, $topic_name)
    {
        $image_name = null;
        if ($request->file('image')) {
            $directory = 'app/public/notification/images';
            $path = storage_path($directory);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $randomInt = $this->randd();
            $image_name = "image_{$originalName}_{$randomInt}.{$extension}";

            // Make sure GD extension is enabled
            $save_image = Image::make($image);
            $save_image->save(storage_path($directory . '/' . $image_name));
        }

        $test_value = $request->boolean('test') ? "test_" : "";

        $topic = $test_value . $topic_name;

        $factory = (new Factory)->withServiceAccount(storage_path($this->path_for_firebase_key));

        $messaging = $factory->createMessaging();

        $notification = [
            'title' => $validated['title'],
            "body" => $validated['body'] ?? null,
            "image" =>  $image_name ? env('APP_DATA_URL') . $this->urlToImage  . $image_name  : null,
            'screen' => "simpleNotificationScreen",
        ];

        // "withData" is neceassary for foreground mode
        // "withNotification" is mostly for background mode
        $message = CloudMessage::new()
            ->toTopic($topic)
            ->withData($notification)
            ->withDefaultSounds()
            ->withHighestPossiblePriority()
            ->withAndroidConfig([
                'priority' => 'high',
            ])
            ->withApnsConfig([
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => $validated['title'],
                            'body' => $validated['body'] ?? null,
                        ],
                        'mutable-content' => 1,
                        'sound' => 'default',
                        "content-available" => 1,
                    ],
                ],
            ]);

        $messaging->send($message);

        return response()->json([
            'success' => true,
            'topic' => $topic,
        ]);
    }

    public function image($image)
    {
        $path = storage_path(
            "app/public/notification/images/" . $image
        );

        if (File::exists($path)) {
            return response()->file($path);
        }

        return response()->file(
            public_path("autos/images/default_car_image.jpg")
        );
    }
}
