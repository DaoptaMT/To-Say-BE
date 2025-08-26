<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ActionMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin']);
    }

    /**
     * Auth: DaoPT
     * CreateAt: 2025-08-14
     * Description: List all messages (pending, approved, rejected)
     * @return \Illuminate\Http\JsonResponse
     */
    public function listMessages()
    {
        $listMessage = Message::with('sender')->get();
        return response()->json([
            'message' => 'List of action messages',
            'data' => $listMessage
        ]);
    }

    /**
     * Auth: DaoPT
     * CreateAt: 2025-08-14
     * Description: Get details of a specific message
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function details($id)
    {
        $message = Message::with('sender')->find($id);
        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }
        return response()->json(['message' => 'Message details', 'data' => $message]);
    }

    /**
     * Auth: DaoPT
     * CreateAt: 2025-08-14
     * Description: Review a message (approve/reject)
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function review(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'nullable|string',
        ]);

        $message = Message::with('sender')->findOrFail($id);

        if ($request->action === 'approve') {
            $message->approval_status = 'approved';
        } else {
            $message->approval_status = 'rejected';
            $message->rejection_reason = $request->reason ?? 'Vi phạm tiêu chí cộng đồng';
        }

        $message->save();

        // TODO

        return response()->json([
            'message' => "Message {$request->action}d successfully",
            'data' => $message
        ]);
    }

    /**
     * Auth: DaoPT
     * CreateAt: 2025-08-14
     * Description: List approved messages
     * @return \Illuminate\Http\JsonResponse
     */
    public function reviewedApproved()
    {
        $messages = Message::where('approval_status', 'approved')->with('sender')->get();
        return response()->json($messages);
    }

    /**
     * Auth: DaoPT
     * CreateAt: 2025-08-14
     * Description: List rejected messages
     * @return \Illuminate\Http\JsonResponse
     */
    public function reviewedRejected()
    {
        $messages = Message::where('approval_status', 'rejected')->with('sender')->get();
        return response()->json($messages);
    }
}
