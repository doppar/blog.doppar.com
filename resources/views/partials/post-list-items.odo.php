#foreach ($posts as $post)
<article class="feed-card group">

    <!-- Author row -->
    <div class="flex items-start gap-3 px-5 pt-4">
        #if (!empty($post?->user?->image))
        <img src="[[ $post->user->image ]]" alt="[[ $post->user->name ]]" class="feed-avatar object-cover" loading="lazy">
        #else
        <span class="feed-avatar" style="background: [[ $post->accent_solid ]];">
            [[ strtoupper(substr((string) ($post?->user?->name ?? 'D'), 0, 1)) ]]
        </span>
        #endif

        <div class="min-w-0 flex-1 leading-tight">
            <div class="flex items-center gap-2">
                <span class="text-[15px] font-semibold text-ink truncate">[[ $post?->user?->name ?? 'Doppar Team' ]]</span>
                #if ($post->is_featured)
                <span class="feed-badge">★ Featured</span>
                #endif
            </div>
            <div class="mt-0.5 flex items-center gap-1.5 text-[12px] text-ink-soft">
                #if ($post->category)
                <span class="font-medium" style="color: [[ $post->accent_solid ]];">[[ $post->category->name ]]</span>
                <span aria-hidden="true">•</span>
                #endif
                <time>[[ $post->published_at ? date('M j, Y', strtotime($post->published_at)) : date('M j, Y', strtotime($post->created_at)) ]]</time>
            </div>
        </div>
    </div>

    <!-- Text -->
    <div class="px-5 pt-3 pb-3">
        <a href="/posts/[[ $post->slug ]]" class="block">
            <h2 class="font-display text-[22px] font-bold leading-snug tracking-tight text-ink transition-colors group-hover:text-primary">
                [[ $post->title ]]
            </h2>
        </a>
        #php
        $cardExcerpt = trim((string) $post->excerpt) !== ''
            ? $post->excerpt
            : mb_strimwidth(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $post->body)))), 0, 220, '…');
        #endphp
        #if ($cardExcerpt !== '')
        <p class="mt-2 text-[14.5px] text-ink-soft leading-relaxed line-clamp-3">
            [[ $cardExcerpt ]]
            <a href="/posts/[[ $post->slug ]]" class="font-medium text-ink hover:text-primary hover:underline whitespace-nowrap">…see more</a>
        </p>
        #endif
    </div>

    <!-- Media -->
    <a href="/posts/[[ $post->slug ]]" class="block relative aspect-[1.91/1] overflow-hidden" style="background: [[ $post->accent_soft ]];">
        #if ($post->cover_image)
        <img
            src="[[ $post->cover_image ]]"
            alt="[[ $post->title ]]"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
            loading="lazy"
        >
        #else
        <div class="absolute inset-0 grid place-items-center">
            <div class="font-display text-8xl font-bold opacity-30" style="color: [[ $post->accent_solid ]];">
                [[ strtoupper(substr($post->title ?? 'D', 0, 1)) ]]
            </div>
            <div class="absolute inset-0" style="background: radial-gradient(120% 120% at 0% 0%, [[ $post->accent_soft ]] 0%, transparent 60%);"></div>
        </div>
        #endif
    </a>

    <!-- Stats -->
    <div class="flex items-center justify-between px-5 py-2.5 text-[12.5px] text-ink-soft">
        <span class="inline-flex items-center gap-1.5 [[ ($post->likes_count ?? 0) > 0 ? '' : 'invisible' ]]" data-like-stat="[[ $post->slug ]]">
            <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
            <span data-like-count>[[ number_format($post->likes_count ?? 0) ]]</span>
        </span>
        <span class="inline-flex items-center gap-1.5">
            [[ number_format($post->comments_count ?? 0) ]] [[ ($post->comments_count ?? 0) === 1 ? 'comment' : 'comments' ]]
            <span aria-hidden="true">•</span>
            [[ number_format($post->view_count ?? 0) ]] views
        </span>
    </div>

    <!-- Actions -->
    <div class="feed-actions">
        #if (auth()->check())
        <button type="button"
                class="feed-action feed-like-btn [[ ($post->viewer_liked ?? false) ? 'is-liked' : '' ]]"
                data-slug="[[ $post->slug ]]"
                data-liked="[[ ($post->viewer_liked ?? false) ? '1' : '0' ]]"
                aria-pressed="[[ ($post->viewer_liked ?? false) ? 'true' : 'false' ]]">
            <svg class="w-[18px] h-[18px] feed-like-icon" fill="[[ ($post->viewer_liked ?? false) ? 'currentColor' : 'none' ]]" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
            <span class="feed-like-label">[[ ($post->viewer_liked ?? false) ? 'Liked' : 'Like' ]]</span>
        </button>
        #else
        <a href="[[ route('login') . '?redirect_to=' . urlencode('/') ]]" class="feed-action" title="Sign in to like this story">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
            Like
        </a>
        #endif
        <a href="/posts/[[ $post->slug ]]&#35;comments-heading" class="feed-action">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
            Comment
        </a>
        <a href="/posts/[[ $post->slug ]]" class="feed-action feed-action--primary">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
            Read story
        </a>
    </div>

</article>
#endforeach
