<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Queue;

Route::get('/queues', function () {
    return Queue::orderBy('created_at')->get();
});

Route::post('/queues', function (Request $request) {

    $request->validate([
        'name' => 'required|string|max:255'
    ]);

    $lastQueue = Queue::latest()->first();
    $number = $lastQueue ? $lastQueue->id + 1 : 1;

    return Queue::create([
        'queue_number' => 'A' . str_pad($number, 3, '0', STR_PAD_LEFT),
        'name' => $request->name,
        'status' => 'waiting'
    ]);
});

// CALL (harus sebelum generic)
Route::post('/queues/{id}/call', function ($id, Request $request) {

    $queue = Queue::findOrFail($id);

    $queue->update([
        'status' => 'called',
        'loket'  => $request->loket
    ]);

    return $queue;
});

// COMPLETE
Route::post('/queues/{id}/complete', function ($id) {
    return tap(Queue::findOrFail($id))->update([
        'status'=>'completed',
        'loket'=>null
    ]);
});

// CANCEL
Route::post('/queues/{id}/cancel', function ($id) {
    return tap(Queue::findOrFail($id))->update([
        'status'=>'canceled',
        'loket'=>null
    ]);
});
