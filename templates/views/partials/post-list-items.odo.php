#foreach ($posts as $post)
#php
$rowExcerpt = trim((string) $post->excerpt) !== ''
    ? $post->excerpt
    : mb_strimwidth(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $post->body)))), 0, 220, '…');
$rowDate = $post->published_at ? date('M j, Y', strtotime($post->published_at)) : date('M j, Y', strtotime($post->created_at));
$rowLiked = (bool) ($post->viewer_liked ?? false);
#endphp
<article class="dp-row">

    <div class="dp-row__no" aria-hidden="true"><small>№</small>[[ str_pad((string) $post->id, 3, '0', STR_PAD_LEFT) ]]</div>

    <div class="dp-row__main">
        <div class="flex flex-wrap items-center gap-2">
            #if ($post->category)
            <span class="dp-chip" style="--chip: [[ $post->accent_solid ]];"><i></i>[[ $post->category->name ]]</span>
            #endif
            #if ($post->is_featured)
            <span class="dp-row__feat">★ Featured</span>
            #endif
        </div>

        <h2 class="dp-row__title"><a href="/posts/[[ $post->slug ]]">[[ $post->title ]]</a></h2>

        #if ($rowExcerpt !== '')
        <p class="dp-row__excerpt">[[ $rowExcerpt ]]</p>
        #endif

        <div class="dp-row__meta">
            <span><b>[[ $post?->user?->name ?? 'Doppar Team' ]]</b></span>
            <time>[[ $rowDate ]]</time>
            <span>[[ number_format($post->comments_count ?? 0) ]] [[ ($post->comments_count ?? 0) === 1 ? 'comment' : 'comments' ]]</span>
            <span>[[ number_format($post->view_count ?? 0) ]] views</span>
            <span data-like-stat="[[ $post->slug ]]" class="[[ ($post->likes_count ?? 0) > 0 ? '' : 'invisible' ]]">♥ <span data-like-count>[[ number_format($post->likes_count ?? 0) ]]</span></span>
        </div>

        <div class="dp-row__foot">
            #if (auth()->check())
            <button type="button"
                    class="dp-like feed-like-btn [[ $rowLiked ? 'is-liked' : '' ]]"
                    data-slug="[[ $post->slug ]]"
                    data-liked="[[ $rowLiked ? '1' : '0' ]]"
                    aria-pressed="[[ $rowLiked ? 'true' : 'false' ]]">
                <svg class="feed-like-icon" fill="[[ $rowLiked ? 'currentColor' : 'none' ]]" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                <span class="feed-like-label">[[ $rowLiked ? 'Liked' : 'Like' ]]</span>
            </button>
            #else
            <a href="[[ route('login') . '?redirect_to=' . urlencode('/') ]]" class="dp-like" title="Sign in to like this story">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                Like
            </a>
            #endif
            <a href="/posts/[[ $post->slug ]]" class="dp-more">
                Read story
                <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
            </a>
        </div>
    </div>

    <div class="dp-row__aside" aria-hidden="true">
        <a href="/posts/[[ $post->slug ]]" tabindex="-1" class="dp-row__thumb">
            #if ($post->cover_image)
            <img src="[[ $post->cover_image ]]" alt="" loading="lazy">
            #else
            <span>[[ strtoupper(substr($post->title ?? 'D', 0, 1)) ]]</span>
            #endif
        </a>
    </div>

</article>
#endforeach
