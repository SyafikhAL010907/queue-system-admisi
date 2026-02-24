<?php

use App\Models\Queue;

Route::get('/', function () {
    $queues = Queue::orderBy('created_at')->get();
    return view('queue', compact('queues'));
});

Route::post('/add', function (\Illuminate\Http\Request $request) {
    $lastQueue = Queue::latest()->first();
    $number = $lastQueue ? $lastQueue->id + 1 : 1;

    Queue::create([
        'queue_number' => 'A' . str_pad($number, 3, '0', STR_PAD_LEFT),
        'name' => $request->name
    ]);

    return redirect('/');
});

Route::post('/update/{id}/{status}', function ($id, $status) {
    $queue = Queue::findOrFail($id);
    $queue->update(['status' => $status]);
    return redirect('/');
});

Route::delete('/delete/{id}', function ($id) {
    $queue = \App\Models\Queue::findOrFail($id);
    $queue->delete();
    return redirect('/');
});

Route::get('/display', function () {
    $current = Queue::where('status','called')->latest()->first();
    return view('display', compact('current'));
});

/* API JSON untuk AJAX */
Route::get('/queues-json', function () {
    return response()->json(
        Queue::orderBy('created_at')->get()
    );
});

Route::view('/mobile', 'mobile');
