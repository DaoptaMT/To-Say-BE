<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminBlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: Create a new blog
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBlog(Request $request)
    {
        if (!Gate::allows('create-blog')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $blog = Blog::create($request->all());
        return response()->json($blog, 201);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: Update an existing blog
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBlog(Request $request, $id)
    {
        if (!Gate::allows('update-blog')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $blog = Blog::find($id);
        $blog->update($request->all());
        return response()->json($blog);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-14
     * Description: Delete a blog
     * @param int $id
     * @return \Illuminate\Http\JsonResponsel
     */
    public function deleteBlog($id)
    {
        if (!Gate::allows('delete-blog')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $blog = Blog::find($id);
        $blog->delete();
        return response()->json(['message' => 'Blog deleted successfully'], 200);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-15
     * Description: Get all blogs
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $blogs = Blog::all();
        return response()->json($blogs);
    }

    /**
     * Auth: DaoPTA
     * CreateAt: 2025-08-15
     * Description: Get blog details
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        return response()->json($blog);
    }
}
