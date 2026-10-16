<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Comment;
use App\Models\Like;

/**
 * Shared cursor-pagination + accent-decoration logic for controllers
 * that render a grid of posts via the `partials.post-list-items` view
 * (e.g. the homepage feed and the saved-posts list).
 */
trait BuildsPostFeed
{
    /**
     * Palette accents cycled per post for the list view.
     * Each entry is [solid CSS colour, soft tinted background].
     */
    private const ACCENT_PALETTE = [
        ['var(--c-primary)', 'rgba(109,108,243,.12)'],
        ['var(--c-cyan)',    'rgba(28,176,238,.12)'],
        ['var(--c-green)',   'rgba(54,202,132,.14)'],
        ['var(--c-amber)',   'rgba(243,177,26,.16)'],
        ['var(--c-pink)',    'rgba(214,104,135,.14)'],
        ['var(--c-gold)',    'rgba(148,107,1,.14)'],
    ];

    /**
     * Attach view-friendly presentation attributes to each post:
     *  - accent_solid : CSS colour for emphasis
     *  - accent_soft  : tinted background colour
     *  - author_initial : single uppercase letter for the avatar
     *
     * The accent is derived from the post id so it stays stable
     * across pagination and AJAX loads.
     */
    protected function decoratePostsWithAccent(array $posts): array
    {
        $palette = self::ACCENT_PALETTE;
        $size = count($palette);

        foreach ($posts as $post) {
            $bucket = ((int) ($post->id ?? 0)) % $size;
            [$solid, $soft] = $palette[$bucket];

            $post->accent_solid = $solid;
            $post->accent_soft  = $soft;
            $post->author_initial = strtoupper(substr((string) ($post->author_name ?? 'A'), 0, 1));
        }

        return $posts;
    }

    /**
     * Attach like/comment totals and whether the signed-in viewer has liked
     * each post, using one grouped query per metric for the whole page.
     *
     *  - likes_count    : total likes
     *  - comments_count : approved comments
     *  - viewer_liked   : true when the current user liked the post
     */
    protected function decoratePostsWithEngagement(array $posts): array
    {
        $ids = array_values(array_unique(array_map(fn($post) => (int) $post->id, $posts)));

        if ($ids === []) {
            return $posts;
        }

        $likeCounts = [];
        foreach (Like::query()->select('post_id')->selectRaw('COUNT(*) AS total')->whereIn('post_id', $ids)->groupBy('post_id')->get() as $row) {
            $likeCounts[(int) $row->post_id] = (int) $row->total;
        }

        $commentCounts = [];
        foreach (Comment::query()->select('post_id')->selectRaw('COUNT(*) AS total')->whereIn('post_id', $ids)->where('status', true)->groupBy('post_id')->get() as $row) {
            $commentCounts[(int) $row->post_id] = (int) $row->total;
        }

        $likedByViewer = [];
        if (auth()->check()) {
            foreach (Like::query()->where('user_id', auth()->id())->whereIn('post_id', $ids)->get() as $row) {
                $likedByViewer[(int) $row->post_id] = true;
            }
        }

        foreach ($posts as $post) {
            $id = (int) $post->id;
            $post->likes_count = $likeCounts[$id] ?? 0;
            $post->comments_count = $commentCounts[$id] ?? 0;
            $post->viewer_liked = isset($likedByViewer[$id]);
        }

        return $posts;
    }

    /**
     * Work out whether a "load more" affordance should be shown after a
     * cursor-paginated fetch, synthesizing a next cursor/url when needed.
     *
     * @param callable(string $cursor): string $buildUrl
     */
    protected function resolveLoadMoreState(
        object $query,
        array $paginated,
        int $perPage,
        string $cursorColumn,
        string $direction,
        callable $buildUrl
    ): array {
        $items = $paginated['data'] ?? [];
        $nextCursor = $paginated['next_cursor'] ?? null;
        $hasMore = (bool) ($paginated['has_more'] ?? false);

        if (!$hasMore && count($items) === $perPage && !empty($items)) {
            $lastItem = end($items);
            $lastValue = $lastItem->{$cursorColumn} ?? null;

            if ($lastValue !== null) {
                $lookAhead = clone $query;
                $operator = strtolower($direction) === 'desc' ? '<' : '>';

                $hasMore = $lookAhead
                    ->where($cursorColumn, $operator, $lastValue)
                    ->limit(1)
                    ->exists();

                if ($hasMore && empty($nextCursor)) {
                    $nextCursor = base64_encode(json_encode(['v' => $lastValue]));
                }
            }
        }

        $nextUrl = ($hasMore && !empty($nextCursor)) ? $buildUrl($nextCursor) : null;

        return [
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor,
            'next_url' => $nextUrl,
        ];
    }
}
