<?php

use App\Services\KafkaProducerService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-kafka', function (KafkaProducerService $kafka) {
    $message = [
        'key' => 'lctiendat',
        'content' => 'Hello from Kafka!',
        'timestamp' => now()->toISOString(),
    ];

    $success = $kafka->sendMessage('chat-message', $message);

    \Log::info($success);

    return response()->json([
        'status' => $success ? 'sent' : 'failed'
    ]);
});
