<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\SeoMeta;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => BlogPost::with(['category', 'tags'])->orderBy('published_at', 'desc')->get(),
        ]);
    }

    public function publicIndex(Request $request): JsonResponse
    {
        $query = BlogPost::published()->with(['category', 'tags', 'author:id,name'])->latest('published_at');
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate($request->integer('per_page', 9));

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'last_page' => $posts->lastPage(),
            ],
        ]);
    }

    public function publicShow(string $slug): JsonResponse
    {
        $post = BlogPost::published()->where('slug', $slug)->with(['category', 'tags', 'author:id,name', 'seoMeta'])->firstOrFail();
        $post->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:250',
            'category_id' => 'required|exists:blog_categories,id',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|string|in:published,draft,archived',
            'read_time_minutes' => 'nullable|integer',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('blogs', 'public');
            $data['featured_image'] = '/storage/' . $path;
        }

        $data['author_id'] = User::first()->id ?? 1;
        $data['slug'] = Str::slug($data['title']);
        $data['published_at'] = now();
        $data['view_count'] = 1;

        $post = BlogPost::create($data);

        // Sync SEO
        SeoMeta::updateOrCreate(
            ['path' => '/blog/' . $post->slug],
            [
                'meta_title' => $post->title . ' | Lumique Journal',
                'meta_description' => $post->excerpt,
                'canonical_url' => url('/blog/' . $post->slug),
                'og_title' => $post->title,
                'og_description' => $post->excerpt,
                'og_image' => $post->featured_image,
                'robots' => 'index, follow',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Article published successfully to database',
            'data' => $post,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $blog = ($id instanceof BlogPost) ? $id : BlogPost::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:250',
            'category_id' => 'required|exists:blog_categories,id',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|string|in:published,draft,archived',
            'read_time_minutes' => 'nullable|integer',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('blogs', 'public');
            $data['featured_image'] = '/storage/' . $path;
        }

        $data['slug'] = Str::slug($data['title']);
        $blog->update($data);

        // Sync SEO
        SeoMeta::updateOrCreate(
            ['path' => '/blog/' . $blog->slug],
            [
                'meta_title' => $blog->title . ' | Lumique Journal',
                'meta_description' => $blog->excerpt,
                'canonical_url' => url('/blog/' . $blog->slug),
                'og_title' => $blog->title,
                'og_description' => $blog->excerpt,
                'og_image' => $blog->featured_image,
                'robots' => 'index, follow',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully in database',
            'data' => $blog,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $blog = ($id instanceof BlogPost) ? $id : BlogPost::findOrFail($id);
        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully from database',
        ]);
    }
}
