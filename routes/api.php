<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LaravelSanctumLearningController;
use App\Http\Controllers\notifications\firebase_notification\FirebaseTopicController;
use App\Http\Controllers\notifications\mail_notification\MailController;
use App\Http\Controllers\notifications\telegram_notification\TelegramNotificationController;
use App\Http\Controllers\ValidationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check/token', [AuthController::class, 'check_token']);
    Route::get('/logout', [AuthController::class, 'log_out']);
    Route::post('/send/message', [ChatController::class, 'sendMessageController']);
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/validation', [ValidationController::class, 'validation']);
Route::get('/temp/image', [ValidationController::class, 'temp_image']);
Route::post('/store/multi/image', [ValidationController::class, 'store_multi_image']);
Route::get('/create/delete-table-factory', [LaravelSanctumLearningController::class, 'createFactories']);

Route::group(["middleware" => 'auth:sanctum', 'prefix' => 'laravel-sanctum'], function () {
    Route::get('/route/success', [LaravelSanctumLearningController::class, 'returnSuccess']);
    Route::get('/route/error', [LaravelSanctumLearningController::class, 'returnError']);
});

Route::get('/route/get/resources-deleted-table', [LaravelSanctumLearningController::class, 'get_delete_models_resources']);

Route::get('/checker', [LaravelSanctumLearningController::class, 'laravel_collections']);

Route::get('/get/video', [AuthController::class, 'get_video']);

Route::get('/update-and-get-refreshed-data', [
    LaravelSanctumLearningController::class,
    'update_than_get_freshed_data'
]);

Route::get('/get/rejection/collection', [
    LaravelSanctumLearningController::class,
    'get_rejecting_some_collection'
]);

Route::get('/check-if-model-changed-during-code', [
    LaravelSanctumLearningController::class,
    'check_if_model_changed_during_code'
]);

Route::get('/get-original-after-changing', [
    LaravelSanctumLearningController::class,
    'get_original_after_changing'
]);

Route::get('/deleting-models', [
    LaravelSanctumLearningController::class,
    'deleting_models'
]);

Route::get('/customer-invoices', [
    LaravelSanctumLearningController::class,
    'customer_invoices'
]);

Route::get('/image/blured', [
    LaravelSanctumLearningController::class,
    'image_blur_hash'
]);

Route::get('/image/url', [
    LaravelSanctumLearningController::class,
    'image_url'
]);


//create token apis
Route::get('create-token', [LaravelSanctumLearningController::class, 'create_token']);
Route::get('create-token-with-ability', [
    LaravelSanctumLearningController::class,
    'create_token_with_ability'
]);

Route::middleware('auth:sanctum')->prefix('auth-token-api')->group(function () {
    Route::get('get-all-user-tokens', [LaravelSanctumLearningController::class, 'all_user_tokens']);
    Route::get('token-check-ability', [
        LaravelSanctumLearningController::class,
        'token_can_do_any_abilities'
    ]);
    Route::get('delete-token', [
        LaravelSanctumLearningController::class,
        'deleting_tokens'
    ]);
});

//


Route::group(['prefix' => 'collections'], function () {
    Route::get(
        '/get/collection-average',
        [LaravelSanctumLearningController::class, 'collection_average']
    );

    Route::get('/contact-two-tables', [
        LaravelSanctumLearningController::class,
        'concatinate_two_tables_and_do_some_code'
    ]);

    Route::get('/method/collapse', [
        LaravelSanctumLearningController::class,
        'method_collaps'
    ]);

    Route::get('contain/and/every/methods', [
        LaravelSanctumLearningController::class,
        'contain_and_every_methods'
    ]);


    Route::get('/each-and-map-methods', [
        LaravelSanctumLearningController::class,
        'each_and_map_methods'
    ]);

    Route::get('/send/telegram/notification', [
        TelegramNotificationController::class,
        'sendTelegramNotification'
    ]);


    Route::get('/send/mail/notification', [
        MailController::class,
        'mail_notification'
    ]);
});

Route::get('/send-topic-url', [
    FirebaseTopicController::class,
    'send_topic'
]);

Route::get('/collect-several-array-of-collections', [
    LaravelSanctumLearningController::class,
    'collect_several_array_of_collections'
]);

Route::get('/send/test/message', [LaravelSanctumLearningController::class, 'sendMessage']);

Route::get('/simple-json', [AuthController::class, 'simple_json']);
