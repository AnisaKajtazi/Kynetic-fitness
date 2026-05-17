<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function conversations(Request $request)
    {
        $me = auth()->user();

        $partnerIds = Message::where('sender_id', $me->UserID)
            ->orWhere('recipient_id', $me->UserID)
            ->get()
            ->flatMap(function ($m) use ($me) {
                return [$m->sender_id === $me->UserID ? $m->recipient_id : $m->sender_id];
            })
            ->unique()
            ->values();

        $conversations = $partnerIds->map(function ($pid) use ($me) {
            $partner = User::find($pid);
            if (!$partner) return null;

            $last = Message::where(function ($q) use ($me, $pid) {
                $q->where('sender_id', $me->UserID)->where('recipient_id', $pid);
            })->orWhere(function ($q) use ($me, $pid) {
                $q->where('sender_id', $pid)->where('recipient_id', $me->UserID);
            })->orderBy('created_at', 'desc')->first();

            $unread = Message::where('sender_id', $pid)
                ->where('recipient_id', $me->UserID)
                ->whereNull('read_at')
                ->count();

            return [
                'user' => $partner,
                'last_message' => $last,
                'unread_count' => $unread,
            ];
        })->filter()->values();

        return response()->json($conversations);
    }

    public function messages(Request $request, $userId)
    {
        $me = auth()->user();

        $messages = Message::where(function ($q) use ($me, $userId) {
            $q->where('sender_id', $me->UserID)->where('recipient_id', $userId);
        })->orWhere(function ($q) use ($me, $userId) {
            $q->where('sender_id', $userId)->where('recipient_id', $me->UserID);
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    public function send(Request $request, $userId)
    {
        $me = auth()->user();

        $data = $request->validate([
            'body' => 'required|string',
        ]);

        $recipient = User::findOrFail($userId);

        $message = Message::create([
            'sender_id' => $me->UserID,
            'recipient_id' => $recipient->UserID,
            'body' => $data['body'],
        ]);

        return response()->json($message, 201);
    }

    public function markRead(Request $request, $userId)
    {
        $me = auth()->user();

        $updated = Message::where('sender_id', $userId)
            ->where('recipient_id', $me->UserID)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['updated' => $updated]);
    }
}
