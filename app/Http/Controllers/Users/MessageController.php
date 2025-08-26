<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:user']);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-15
     * Description: Create message
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'content' => 'required_without_all:image,voice,music|string',
            'image' => 'nullable|string',
            'voice' => 'nullable|string',
            'music' => 'nullable|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'recipient_email' => $request->recipient_email ?? null,
            'recipient_phone' => $request->recipient_phone ?? null,
            'message_text' => $request->content ?? null,
            'image' => $request->image ?? null,
            'voice' => $request->voice ?? null,
            'music' => $request->music ?? null,
            'approval_status' => 'pending',
        ]);

        return response()->json(['message' => 'Message created successfully', 'data' => $message], 201);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-15
     * Description: Update message
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $message = Message::where('id', $id)->where('sender_id', Auth::id())->first();

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        if ($message->approval_status !== 'pending') {
            return response()->json(['message' => 'Cannot edit message after review'], 403);
        }

        $request->validate([
            'content' => 'required_without_all:image,voice,music|string',
            'image' => 'nullable|string',
            'voice' => 'nullable|string',
            'music' => 'nullable|string',
        ]);

        $message->update([
            'message_text' => $request->content ?? $message->message_text,
            'image' => $request->image ?? $message->image,
            'voice' => $request->voice ?? $message->voice,
            'music' => $request->music ?? $message->music,
        ]);

        return response()->json(['message' => 'Message updated successfully', 'data' => $message]);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-15
     * Description: Delete message
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $message = Message::where('id', $id)->where('sender_id', Auth::id())->first();

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        if ($message->approval_status !== 'pending') {
            return response()->json(['message' => 'Cannot delete message after review'], 403);
        }

        $message->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-15
     * Description: Get my messages
     * @return \Illuminate\Http\JsonResponse
     */
    public function myMessages()
    {
        $messages = Message::where('sender_id', Auth::id())->get();
        return response()->json(['data' => $messages]);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-15
     * Description: Get message details
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function details($id)
    {
        $message = Message::where('id', $id)->where('sender_id', Auth::id())->first();

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        return response()->json(['data' => $message]);
    }
}
