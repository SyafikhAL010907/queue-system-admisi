<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    // Get all queue
    public function index()
    {
        return response()->json(
            Queue::orderBy('created_at')->get()
        );
    }

    // Add customer
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $lastQueue = Queue::latest()->first();
        $number = $lastQueue ? $lastQueue->id + 1 : 1;

        $queue = Queue::create([
            'queue_number' => 'A' . str_pad($number, 3, '0', STR_PAD_LEFT),
            'name' => $request->name,
            'status' => 'waiting'
        ]);

        return response()->json($queue);
    }

    // ✅ CALL WITH LOKET (MULTI LOKET FIX)
    public function call(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);

        $queue->status = 'called';
        $queue->loket = $request->loket; // ambil dari JS
        $queue->save();

        return response()->json($queue);
    }

    // Complete
    public function complete($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update(['status' => 'completed']);

        return response()->json($queue);
    }

    // Cancel
    public function cancel($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update(['status' => 'canceled']);

        return response()->json($queue);
    }

    // Stats
    public function stats()
    {
        return response()->json([
            'waiting' => Queue::where('status','waiting')->count(),
            'called' => Queue::where('status','called')->count(),
            'completed' => Queue::where('status','completed')->count(),
            'canceled' => Queue::where('status','canceled')->count(),
        ]);
    }
}
