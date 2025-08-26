<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class UserBlogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:user']);
    }
    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: Get all blogs
     * @return \Illuminate\Http\JsonResponse
     */
    public function userIndex()
    {
        $blogs = Blog::where('author_id', Auth::id())->get();

        return response()->json($blogs, 200);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: Get blog details
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function details($id)
    {
        $blog = Blog::where('id', $id)->where('author_id', Auth::id())->first();

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        return response()->json($blog, 200);
    }

}
