#extends('layouts.public')

#section('title')[[ 'Doppar News — Releases, Tutorials & Community Updates' ]]#endsection

#section('og_title')[[ 'Doppar News — Releases, Tutorials & Community Updates' ]]#endsection

#section('meta_description')[[ 'Release notes, RFCs, tutorials and package announcements from the Doppar core team and community, curated in one place.' ]]#endsection

#section('content')

#php
$totalStories = array_sum(array_map(fn($category) => (int) $category->posts_total, $categories));
$lead = $posts['data'][0] ?? null;
$rest = $lead ? array_slice($posts['data'], 1) : [];
$arrowSvg = '<svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>';
#endphp

<div class="min-h-screen flex flex-col">

    #include('partials.header')

    <main class="flex-1">

        <!-- ================= HERO ================= -->
        <section class="dp-hero">
            <div class="dp-wrap">
                <div class="dp-hero__grid">
                    <div>
                        <p class="dp-eyebrow">News from the Doppar PHP framework</p>
                        <h1>Releases, tutorials &amp; the people behind <span class="dp-mark">Doppar</span>.</h1>
                        <p class="dp-hero__lede">Release notes, RFCs, tutorials and package announcements from the core team and the community, in one place.</p>
                        <div class="dp-hero__stats">
                            <div class="dp-stat"><b>[[ $totalStories ]]</b><span>Stories</span></div>
                            <div class="dp-stat"><b>[[ count($categories) ]]</b><span>Topics</span></div>
                            <div class="dp-stat"><b>[[ count($tags) ]]</b><span>Tags</span></div>
                        </div>
                    </div>
                    <div class="dp-cluster" aria-hidden="true"><i class="c1"></i><i class="c2"></i><i class="c3"></i><i class="c4"></i><i class="c5"></i></div>
                </div>
            </div>
        </section>

        #if (count($categories) > 0)
        <!-- Topic strip -->
        <div class="dp-marquee" aria-hidden="true">
            <div class="dp-marquee__track">
                #foreach ([1, 2, 3, 4, 5, 6] as $repeat)
                #foreach ($categories as $category)
                <span class="dp-marquee__item">[[ $category->name ]]</span>
                #endforeach
                #endforeach
            </div>
        </div>
        #endif

        <!-- ================= TILES ================= -->
        #if ($lead)
        <div class="dp-wrap">
            <div class="dp-mosaic">

                <a href="/posts/[[ $lead->slug ]]" class="dp-lead">
                    #if ($lead->cover_image)
                    <img class="dp-lead__img" src="[[ $lead->cover_image ]]" alt="">
                    #else
                    <span class="dp-lead__letter" aria-hidden="true">[[ strtoupper(substr($lead->title ?? 'D', 0, 1)) ]]</span>
                    #endif
                    <span class="dp-lead__go">[[! $arrowSvg !]]</span>
                    <div class="dp-lead__body">
                        <div class="flex flex-wrap items-center gap-2">
                            #if ($lead->category)
                            <span class="dp-chip" style="--chip: var(--dp-volt);"><i></i>[[ $lead->category->name ]]</span>
                            #endif
                            #if ($lead->is_featured)
                            <span class="dp-row__feat">★ Featured</span>
                            #endif
                        </div>
                        <h2>[[ $lead->title ]]</h2>
                        <div class="dp-lead__meta">
                            <span>[[ $lead?->user?->name ?? 'Doppar Team' ]]</span>
                            <span>[[ $lead->published_at ? date('M j, Y', strtotime($lead->published_at)) : date('M j, Y', strtotime($lead->created_at)) ]]</span>
                            <span>[[ max(1, (int) ceil(str_word_count(strip_tags($lead->body ?? '')) / 200)) ]] min read</span>
                            <span>[[ number_format($lead->view_count ?? 0) ]] views</span>
                        </div>
                    </div>
                </a>

                <aside class="dp-tile dp-tile--ink">
                    <div>
                        <p class="dp-eyebrow">// Build with Doppar</p>
                        <h3>Expressive PHP, built for throughput.</h3>
                        <p>Read like prose, run like a machine.</p>
                    </div>
                    <div class="dp-tile__links"><a href="https://doppar.com">Read the docs →</a></div>
                </aside>

                <aside class="dp-tile dp-tile--volt">
                    <div>
                        <p class="dp-eyebrow">Community</p>
                        <h3>Come say hi.</h3>
                        <p>Ask questions and share what you are building.</p>
                    </div>
                    <div class="dp-tile__links">
                        <a href="https://discord.gg/Hz3tq294J" target="_blank" rel="noopener noreferrer">Join our Discord →</a>
                        <a href="https://github.com/doppar/framework/issues" target="_blank" rel="noopener noreferrer">Request a feature →</a>
                    </div>
                </aside>

            </div>
        </div>
        #endif

        <!-- ================= STORIES ================= -->
        <section class="dp-section" id="stories">
            <div class="dp-wrap">

                <div class="dp-section__head">
                    <div>
                        <p class="dp-eyebrow">The index</p>
                        <h2>[[ $tab === 'featured' ? 'Featured stories' : 'Latest stories' ]]<sup>[[ $totalStories ]]</sup></h2>
                    </div>
                    <div class="dp-switch" role="group" aria-label="Story order">
                        <a href="[[ $filterUrl(['tab' => 'for-you']) ]]" aria-current="[[ $tab === 'for-you' ? 'true' : 'false' ]]">Latest</a>
                        <a href="[[ $filterUrl(['tab' => 'featured']) ]]" aria-current="[[ $tab === 'featured' ? 'true' : 'false' ]]">Featured</a>
                    </div>
                </div>

                #if (count($categories) > 0)
                <nav class="dp-topics" aria-label="Topics">
                    <a class="dp-topic" href="[[ $filterUrl(['category' => null]) ]]" aria-current="[[ $activeCategory ? 'false' : 'true' ]]">All<sup>[[ $totalStories ]]</sup></a>
                    #foreach ($categories as $category)
                    #php
                    $categoryActive = $activeCategory && (int) $activeCategory->id === (int) $category->id;
                    #endphp
                    <a class="dp-topic" href="[[ $filterUrl(['category' => $categoryActive ? null : $category->slug]) ]]" style="--t: [[ $category->accent_color ]];" aria-current="[[ $categoryActive ? 'true' : 'false' ]]">
                        <i></i>[[ $category->name ]]<sup>[[ $category->posts_total ]]</sup>
                    </a>
                    #endforeach
                </nav>
                #endif

                #if ($activeTag)
                <div class="dp-active">
                    <a href="[[ $filterUrl(['tag' => null]) ]]" aria-label="Remove tag filter">&#35;[[ $activeTag->name ]] <span aria-hidden="true">×</span></a>
                </div>
                #endif

                <div id="post-list" class="dp-ledger">
                    #include('partials.post-list-items', ['posts' => $rest])
                </div>

                #if ($showEmptyState)
                <div class="dp-empty">
                    #if ($hasFilters)
                    <h3>No stories match these filters.</h3>
                    <p>Try a different topic or tag, or <a href="[[ $filterUrl(['category' => null, 'tag' => null]) ]]">clear the filters</a>.</p>
                    #else
                    <h3>No stories yet.</h3>
                    <p>Fresh writing is on the way. Check back soon.</p>
                    #endif
                </div>
                #endif

                #if ($loadMore['has_more'])
                <div class="dp-loadmore">
                    <button type="button" id="load-more-posts" data-next-url="[[ $loadMore['next_url'] ]]" class="dp-btn">
                        Show more stories
                        <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                </div>
                #endif

            </div>
        </section>

        #if (count($tags) > 0)
        <!-- ================= TAG INDEX ================= -->
        <section class="dp-section">
            <div class="dp-wrap">
                <div class="dp-section__head">
                    <div>
                        <p class="dp-eyebrow">Browse</p>
                        <h2>Tag index</h2>
                    </div>
                </div>
                <div class="dp-tags">
                    #foreach ($tags as $tag)
                    #php
                    $tagActive = $activeTag && (int) $activeTag->id === (int) $tag->id;
                    #endphp
                    <a href="[[ $filterUrl(['tag' => $tagActive ? null : $tag->slug]) ]]" class="dp-tag" aria-current="[[ $tagActive ? 'true' : 'false' ]]">[[ $tag->name ]]<sup>[[ $tag->posts_total ]]</sup></a>
                    #endforeach
                </div>
            </div>
        </section>
        #endif

    </main>

    #include('partials.footer')

</div>

<script>
    (() => {
        const button = document.getElementById('load-more-posts');
        const postList = document.getElementById('post-list');

        if (!button || !postList) {
            return;
        }

        let isLoading = false;
        const spinnerSvg = '<svg class="animate-spin" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>';

        button.addEventListener('click', async () => {
            const nextUrl = button.dataset.nextUrl;

            if (!nextUrl || isLoading) {
                return;
            }

            isLoading = true;
            const originalHTML = button.innerHTML;
            button.innerHTML = `${spinnerSvg}<span>Loading…</span>`;
            button.disabled = true;

            try {
                const response = await fetch(nextUrl, {
                    __skipAdminLoader: true,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Unable to load more posts.');
                }

                const payload = await response.json();

                if (payload.html) {
                    postList.insertAdjacentHTML('beforeend', payload.html);
                }

                if (payload.has_more && payload.next_url) {
                    button.dataset.nextUrl = payload.next_url;
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                } else {
                    button.remove();
                }
            } catch (error) {
                button.innerHTML = 'Try again';
                button.disabled = false;
            } finally {
                isLoading = false;
            }
        });
    })();
</script>

#endsection