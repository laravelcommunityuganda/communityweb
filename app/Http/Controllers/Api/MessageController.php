<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Get all conversations for the authenticated user.
     */
    public function conversations(Request $request): JsonResponse
    {
        $conversations = $request->user()->conversations()
            ->with(['participants.profile'])
            ->withLastMessage()
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Create or get a conversation with a user.
     */
    public function getConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = $request->user();
        $otherUser = \App\Models\User::findOrFail($validated['user_id']);

        // Check if blocked
        if ($user->hasBlocked($otherUser) || $otherUser->hasBlocked($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot start conversation with this user',
            ], 403);
        }

        // Find existing conversation
        $conversation = Conversation::whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereHas('participants', function ($q) use ($otherUser) {
            $q->where('user_id', $otherUser->id);
        })->where('type', 'private')->first();

        if (!$conversation) {
            // Create new conversation
            $conversation = Conversation::create(['type' => 'private']);
            $conversation->participants()->attach([$user->id, $otherUser->id]);
        }

        $conversation->load(['participants.profile']);

        return response()->json([
            'success' => true,
            'data' => $conversation,
        ]);
    }

    /**
     * Get messages for a conversation.
     */
    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        // Check if user is participant
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $messages = $conversation->messages()
            ->with('user.profile')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        // Mark messages as read
        $conversation->messages()
            ->where('user_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    /**
     * Send a message.
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conversation_id' => ['required_without:user_id', 'exists:conversations,id'],
            'user_id' => ['required_without:conversation_id', 'exists:users,id'],
            'content' => ['required_without:file', 'string', 'max:5000'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB max
        ]);

        $user = $request->user();

        // Get or create conversation
        if (isset($validated['conversation_id'])) {
            $conversation = Conversation::findOrFail($validated['conversation_id']);

            // Check if user is participant
            if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
        } else {
            $otherUser = \App\Models\User::findOrFail($validated['user_id']);

            // Check if blocked
            if ($user->hasBlocked($otherUser) || $otherUser->hasBlocked($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot send message to this user',
                ], 403);
            }

            // Find or create conversation
            $conversation = Conversation::whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereHas('participants', function ($q) use ($otherUser) {
                $q->where('user_id', $otherUser->id);
            })->where('type', 'private')->first();

            if (!$conversation) {
                $conversation = Conversation::create(['type' => 'private']);
                $conversation->participants()->attach([$user->id, $otherUser->id]);
            }
        }

        // Create message
        $message = new Message([
            'user_id' => $user->id,
            'content' => $validated['content'] ?? null,
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('messages', 'public');
            $message->file_path = $path;
            $message->file_name = $request->file('file')->getClientOriginalName();
            $message->file_size = $request->file('file')->getSize();
            $message->file_type = $request->file('file')->getMimeType();
        }

        $conversation->messages()->save($message);
        $conversation->touch(); // Update conversation timestamp

        $message->load('user.profile');

        // TODO: Broadcast to other participants

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message,
        ], 201);
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message): JsonResponse
    {
        $this->authorize('delete', $message);

        if ($message->file_path) {
            \Storage::disk('public')->delete($message->file_path);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
        ]);
    }

    /**
     * Mark conversation as read.
     */
    public function markAsRead(Request $request, Conversation $conversation): JsonResponse
    {
        // Check if user is participant
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $conversation->messages()
            ->where('user_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation marked as read',
        ]);
    }

    /**
     * Get unread messages count.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Message::whereHas('conversation.participants', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->where('user_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $count,
            ],
        ]);
    }

    /**
     * Create a group conversation.
     */
    public function createGroup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $conversation = Conversation::create([
            'type' => 'group',
            'name' => $validated['name'],
        ]);

        // Add creator as admin
        $conversation->participants()->attach($request->user()->id, ['is_admin' => true]);

        // Add other participants
        foreach ($validated['user_ids'] as $userId) {
            if ($userId != $request->user()->id) {
                $conversation->participants()->attach($userId, ['is_admin' => false]);
            }
        }

        $conversation->load(['participants.profile']);

        return response()->json([
            'success' => true,
            'message' => 'Group created successfully',
            'data' => $conversation,
        ], 201);
    }

    /**
     * Add participant to group.
     */
    public function addParticipant(Request $request, Conversation $conversation): JsonResponse
    {
        // Check if group
        if ($conversation->type !== 'group') {
            return response()->json([
                'success' => false,
                'message' => 'Can only add participants to group conversations',
            ], 400);
        }

        // Check if user is admin
        $participant = $conversation->participants()->where('user_id', $request->user()->id)->first();
        if (!$participant || !$participant->pivot->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only group admins can add participants',
            ], 403);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        // Check if already participant
        if ($conversation->participants()->where('user_id', $validated['user_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a participant',
            ], 400);
        }

        $conversation->participants()->attach($validated['user_id'], ['is_admin' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Participant added successfully',
        ]);
    }

    /**
     * Leave a group conversation.
     */
    public function leaveGroup(Request $request, Conversation $conversation): JsonResponse
    {
        // Check if group
        if ($conversation->type !== 'group') {
            return response()->json([
                'success' => false,
                'message' => 'Can only leave group conversations',
            ], 400);
        }

        $conversation->participants()->detach($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'You have left the group',
        ]);
    }
}