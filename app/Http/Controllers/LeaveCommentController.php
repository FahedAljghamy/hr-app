<?php

/**
 * Author: Eng.Fahed
 * Leave Comment Controller - HR System
 * تحكم في تعليقات الإجازات - نظام شات داخلي
 */

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LeaveCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant']);
    }

    /**
     * إضافة تعليق جديد
     */
    public function store(Request $request, Leave $leave): JsonResponse
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'comment_type' => 'nullable|in:' . implode(',', array_keys(LeaveComment::getCommentTypes())),
            'visibility' => 'nullable|in:' . implode(',', array_keys(LeaveComment::getVisibilityLevels())),
            'is_internal' => 'nullable|boolean',
        ]);

        try {
            $comment = $leave->comments()->create([
                'user_id' => auth()->id(),
                'tenant_id' => auth()->user()->tenant_id,
                'comment' => $request->get('comment'),
                'comment_type' => $request->get('comment_type', 'general'),
                'visibility' => $request->get('visibility', 'public'),
                'is_internal' => $request->boolean('is_internal', false),
                'notify_employee' => true,
                'notify_manager' => true,
            ]);

            $comment->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'user_name' => $comment->user->name,
                    'comment_type' => $comment->comment_type,
                    'created_at' => $comment->formatted_created_at,
                    'can_be_edited' => $comment->can_be_edited,
                    'can_be_deleted' => $comment->can_be_deleted,
                    'is_system_generated' => $comment->is_system_generated,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تعديل تعليق
     */
    public function update(Request $request, LeaveComment $comment): JsonResponse
    {
        if (!$comment->can_be_edited) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit this comment'
            ], 400);
        }

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        try {
            $comment->editComment($request->get('comment'));

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'is_edited' => $comment->is_edited,
                    'edited_at' => $comment->edited_at?->diffForHumans(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف تعليق
     */
    public function destroy(LeaveComment $comment): JsonResponse
    {
        if (!$comment->can_be_deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this comment'
            ], 400);
        }

        try {
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على تعليقات الإجازة (API)
     */
    public function getComments(Leave $leave): JsonResponse
    {
        try {
            $comments = $leave->comments()
                             ->with('user')
                             ->where(function ($query) {
                                 // عرض التعليقات العامة أو التعليقات الخاصة بالمستخدم
                                 $query->where('visibility', 'public')
                                       ->orWhere('user_id', auth()->id());
                                 
                                 // إذا كان مدير، يمكنه رؤية تعليقات المدراء
                                 if (auth()->user()->hasRole(['Admin', 'Manager'])) {
                                     $query->orWhere('visibility', 'managers_only');
                                 }
                             })
                             ->orderBy('created_at', 'asc')
                             ->get();

            // تحديد التعليقات كمقروءة
            foreach ($comments as $comment) {
                $comment->markAsRead(auth()->id());
            }

            return response()->json([
                'success' => true,
                'comments' => $comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'comment' => $comment->comment,
                        'user_name' => $comment->user->name,
                        'user_type' => $comment->user->user_type,
                        'comment_type' => $comment->comment_type,
                        'visibility' => $comment->visibility,
                        'created_at' => $comment->formatted_created_at,
                        'created_at_full' => $comment->created_at->format('Y-m-d H:i:s'),
                        'can_be_edited' => $comment->can_be_edited,
                        'can_be_deleted' => $comment->can_be_deleted,
                        'is_system_generated' => $comment->is_system_generated,
                        'is_edited' => $comment->is_edited,
                        'edited_at' => $comment->edited_at?->diffForHumans(),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading comments: ' . $e->getMessage()
            ], 500);
        }
    }
}