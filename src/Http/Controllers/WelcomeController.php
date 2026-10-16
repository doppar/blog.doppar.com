<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPostFeed;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Phaseolies\Support\Facades\DB;
use Phaseolies\Http\Request;
use Phaseolies\Support\Router\Attributes\Route;
use Phaseolies\Database\Entity\Attributes\Model as RouteModel;

class WelcomeController extends Controller
{
    use BuildsPostFeed;

    #[Route(uri: '/', name: 'home')]
    public function welcome(Request $request)
    {
        $perPage = 5;
        $cursorColumn = 'id';
        $direction = 'desc';

        $activeCategory = $this->resolveCategory((string) $request->query('category', ''));
        $activeTag = $this->resolveTag((string) $request->query('tag', ''));
        $tagPostIds = $activeTag ? $this->postIdsForTag((int) $activeTag->id) : [];

        $query = Post::query()
            ->where('status', 'published')
            ->embed('category:name')
            ->if($request->tab === 'featured', fn($query) => $query->where('is_featured', true))
            ->if($activeCategory, fn($query) => $query->where('category_id', (int) $activeCategory->id))
            ->if($activeTag, fn($query) => $query->whereIn('id', $tagPostIds ?: [0]));

        $posts = $query->cursorPaginate(
            perPage: $perPage,
            cursorColumn: $cursorColumn,
            direction: $direction,
            cursor: $request->query('cursor')
        );

        $posts['data'] = $this->decoratePostsWithEngagement(
            $this->decoratePostsWithAccent($posts['data'] ?? [])
        );

        $tab = $request->input('tab', 'for-you');

        // Filters carried through the tabs, the filter links and "Show more".
        $filters = array_filter([
            'category' => $activeCategory?->slug,
            'tag' => $activeTag?->slug,
        ]);

        $loadMore = $this->resolveLoadMoreState(
            query: $query,
            paginated: $posts,
            perPage: $perPage,
            cursorColumn: $cursorColumn,
            direction: $direction,
            buildUrl: fn($cursor) => '/?' . http_build_query(['tab' => $tab] + $filters + ['cursor' => $cursor])
        );

        if ($request->isAjax()) {
            return response()->json([
                'html' => view('partials.post-list-items', ['posts' => $posts['data']])->render(),
                'next_cursor' => $loadMore['next_cursor'],
                'next_url' => $loadMore['next_url'],
                'has_more' => $loadMore['has_more'],
            ]);
        }

        $showEmptyState = empty($posts['data']) && !$loadMore['has_more'];

        $filterUrl = fn(array $changes = []) => $this->feedUrl(['tab' => $tab] + $filters, $changes);
        $categories = $this->categoriesWithCounts();
        $tags = $this->tagsWithCounts();
        $hasFilters = $filters !== [];

        return view('welcome', compact(
            'posts', 'tab', 'loadMore', 'showEmptyState',
            'activeCategory', 'activeTag', 'categories', 'tags', 'filterUrl', 'hasFilters'
        ));
    }

    /**
     * Build a feed URL from the current query, applying changes
     * (a null or empty value removes that parameter).
     */
    private function feedUrl(array $current, array $changes): string
    {
        $params = array_filter(
            array_merge($current, $changes),
            fn($value) => $value !== null && $value !== ''
        );

        // "for-you" is the default tab; keep URLs clean.
        if (($params['tab'] ?? '') === 'for-you') {
            unset($params['tab']);
        }

        return $params === [] ? '/' : '/?' . http_build_query($params);
    }

    private function resolveCategory(string $slug): ?Category
    {
        return $slug === '' ? null : Category::query()->active()->where('slug', $slug)->first();
    }

    private function resolveTag(string $slug): ?Tag
    {
        return $slug === '' ? null : Tag::query()->where('slug', $slug)->first();
    }

    /**
     * @return array<int, int>
     */
    private function postIdsForTag(int $tagId): array
    {
        return array_map(
            fn($row) => (int) $row['post_id'],
            DB::query('SELECT post_id FROM post_tag WHERE tag_id = ?', [$tagId])->all()
        );
    }

    /**
     * Active categories that have published posts, with their counts.
     */
    private function categoriesWithCounts(): array
    {
        $counts = [];

        foreach (Post::query()->select('category_id')->selectRaw('COUNT(*) AS total')->where('status', 'published')->groupBy('category_id')->get() as $row) {
            $counts[(int) $row->category_id] = (int) $row->total;
        }

        $categories = [];

        foreach (Category::query()->active()->orderBy('name')->get() as $category) {
            $category->posts_total = $counts[(int) $category->id] ?? 0;

            if ($category->posts_total > 0) {
                $categories[] = $category;
            }
        }

        return $categories;
    }

    /**
     * Tags used by published posts, most used first.
     */
    private function tagsWithCounts(): array
    {
        $counts = [];

        foreach (DB::query(
            'SELECT pt.tag_id, COUNT(*) AS total FROM post_tag pt JOIN posts p ON p.id = pt.post_id WHERE p.status = ? GROUP BY pt.tag_id',
            ['published']
        )->all() as $row) {
            $counts[(int) $row['tag_id']] = (int) $row['total'];
        }

        $tags = [];

        foreach (Tag::query()->orderBy('name')->get() as $tag) {
            $tag->posts_total = $counts[(int) $tag->id] ?? 0;

            if ($tag->posts_total > 0) {
                $tags[] = $tag;
            }
        }

        usort($tags, fn($a, $b) => $b->posts_total <=> $a->posts_total ?: strcmp($a->name, $b->name));

        return $tags;
    }

    #[Route(uri: '/posts/{post}', name: 'post.show')]
    public function show(#[RouteModel(exception: true)] Post $post)
    {
        $viewedPosts = session('viewed_posts', []);

        if (!in_array($post->id, $viewedPosts, true)) {
            $post->increment('view_count');
            $viewedPosts[] = $post->id;
            session()->put('viewed_posts', $viewedPosts);
        }

        return view('post', compact('post'));
    }
}
