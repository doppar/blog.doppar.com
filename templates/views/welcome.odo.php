#extends('layouts.public')

#section('title')[[ 'Doppar News — Releases, Tutorials & Community Updates' ]]#endsection

#section('og_title')[[ 'Doppar News — Releases, Tutorials & Community Updates' ]]#endsection

#section('meta_description')[[ 'Release notes, RFCs, tutorials and package announcements from the Doppar core team and community, curated in one place.' ]]#endsection

#section('content')

<div class="min-h-screen flex flex-col">

    #include('partials.header')

    <!-- ================= MAIN ================= -->
    <main class="flex-1">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 lg:py-10 grid lg:grid-cols-[250px_minmax(0,1fr)_290px] gap-6">

            <!-- LEFT: profile card -->
            <aside class="hidden lg:block lg:sticky lg:top-24 lg:self-start space-y-4">
                <div class="feed-card text-center">
                    <div class="side-banner"></div>
                    <div class="-mt-8 px-5 pb-5">
                        <span class="mx-auto grid place-items-center w-16 h-16 rounded-full bg-white ring-4 ring-white shadow">
                            <img src="/logo-icon.png" alt="Doppar" class="h-9 w-auto">
                        </span>
                        <h1 class="mt-3 font-display text-lg font-bold text-ink leading-tight">Doppar News</h1>
                        <p class="mt-1 text-[13px] text-ink-soft leading-snug">Releases, tutorials and community updates from the Doppar PHP framework.</p>
                    </div>
                    <div class="border-t border-soft px-2 py-2 text-left text-sm font-medium">
                        <a href="[[ $filterUrl(['tab' => 'for-you']) ]]" class="flex items-center justify-between px-3 py-2 rounded-lg [[ $tab === 'for-you' ? 'bg-[rgba(109,108,243,.1)] text-primary' : 'text-ink-soft hover:bg-[rgba(26,26,26,.04)]' ]]">All stories</a>
                        <a href="[[ $filterUrl(['tab' => 'featured']) ]]" class="flex items-center justify-between px-3 py-2 rounded-lg [[ $tab === 'featured' ? 'bg-[rgba(109,108,243,.1)] text-primary' : 'text-ink-soft hover:bg-[rgba(26,26,26,.04)]' ]]">★ Featured</a>
                    </div>
                </div>

                #include('partials.feed-filters')
            </aside>

            <!-- CENTER: feed -->
            <div class="min-w-0">
                <!-- Mobile / tablet intro + tabs -->
                <div class="feed-card px-5 py-4 mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-bold tracking-tight text-ink">
                            [[ $tab === 'featured' ? 'Featured stories' : 'Latest stories' ]]
                        </h2>
                        <p class="text-[13px] text-ink-soft mt-0.5">From the Doppar team and community.</p>
                        #if ($hasFilters)
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            #if ($activeCategory)
                            <span class="active-filter">[[ $activeCategory->name ]]
                                <a href="[[ $filterUrl(['category' => null]) ]]" aria-label="Remove category filter">×</a>
                            </span>
                            #endif
                            #if ($activeTag)
                            <span class="active-filter">Tag: [[ $activeTag->name ]]
                                <a href="[[ $filterUrl(['tag' => null]) ]]" aria-label="Remove tag filter">×</a>
                            </span>
                            #endif
                        </div>
                        #endif
                    </div>
                    <div class="flex items-center gap-1 p-1 rounded-full bg-[#f1f2fa]">
                        <a href="[[ $filterUrl(['tab' => 'for-you']) ]]"
                            class="px-3.5 py-1.5 rounded-full text-sm font-medium whitespace-nowrap transition-colors [[ $tab === 'for-you' ? 'bg-ink text-[#fefefe]' : 'text-ink-soft hover:text-ink' ]]">
                            For you
                        </a>
                        <a href="[[ $filterUrl(['tab' => 'featured']) ]]"
                            class="px-3.5 py-1.5 rounded-full text-sm font-medium whitespace-nowrap transition-colors [[ $tab === 'featured' ? 'bg-ink text-[#fefefe]' : 'text-ink-soft hover:text-ink' ]]">
                            Featured
                        </a>
                    </div>
                </div>

                <!-- Filters on small screens (the sidebar is hidden there) -->
                <details class="lg:hidden mb-4 feed-card" [[ $hasFilters ? 'open' : '' ]]>
                    <summary class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink cursor-pointer">
                        Filter by category or tag
                        <span class="text-[12px] font-medium text-primary">[[ $hasFilters ? 'Active' : '' ]]</span>
                    </summary>
                    <div class="pb-2">
                        #include('partials.feed-filters')
                    </div>
                </details>

                <div id="post-list" class="flex flex-col gap-4">
                    #include('partials.post-list-items', ['posts' => $posts['data']])
                </div>

                #if ($showEmptyState)
                <div class="feed-card py-20 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-[#f1f2fa] grid place-items-center">
                        <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    #if ($hasFilters)
                    <p class="text-base font-medium text-ink">No stories match these filters</p>
                    <p class="text-sm text-ink-soft mt-1">Try a different category or tag, or <a href="[[ $filterUrl(['category' => null, 'tag' => null]) ]]" class="text-primary font-medium hover:underline">clear the filters</a>.</p>
                    #else
                    <p class="text-base font-medium text-ink">No stories published yet</p>
                    <p class="text-sm text-ink-soft mt-1">Check back soon — fresh writing is on the way.</p>
                    #endif
                </div>
                #endif

                #if ($loadMore['has_more'])
                <div class="py-8 flex items-center justify-center">
                    <button
                        type="button"
                        id="load-more-posts"
                        data-next-url="[[ $loadMore['next_url'] ]]"
                        class="group inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-white border border-soft shadow-sm text-sm font-semibold text-primary hover:bg-primary hover:text-[#fefefe] hover:border-transparent transition-colors">
                        Show more stories
                        <svg class="w-4 h-4 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                </div>
                #endif
            </div>

            <!-- RIGHT: sidebar -->
            <aside class="hidden lg:block space-y-4 lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-[14px] p-6 text-[#fefefe] relative overflow-hidden" style="background: linear-gradient(135deg, var(--c-ink) 0%, #2a2a2a 100%);">
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full" style="background: radial-gradient(circle, rgba(109,108,243,.5), transparent 70%);"></div>
                    <div class="relative">
                        <div class="font-mono text-[10px] tracking-widest text-cyan-a mb-2">// PSST</div>
                        <h3 class="font-display text-lg font-bold leading-snug mb-2">Build with Doppar.</h3>
                        <p class="text-sm text-[#babcbd] leading-relaxed mb-4">Expressive PHP, engineered for throughput. Read like prose, run like a machine.</p>
                        <a href="https://doppar.com" class="inline-flex items-center gap-1 text-sm font-medium text-amber-a link-anim">
                            Read the docs →
                        </a>
                    </div>
                </div>

                <div class="feed-card p-5">
                    <h3 class="text-[15px] font-semibold text-ink mb-3">Join the community</h3>
                    <ul class="space-y-2 text-sm text-ink-soft">
                        <li><a href="https://github.com/doppar/framework/issues" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors">Request a feature →</a></li>
                        <li><a href="https://www.linkedin.com/company/doppar/posts/?feedView=all" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors">Follow on LinkedIn →</a></li>
                        <li><a href="https://x.com/dopparframework" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors">Follow on X →</a></li>
                    </ul>
                </div>
            </aside>

        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="relative overflow-hidden border-t border-soft mt-12">
        <div class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-72 h-72 rounded-full blur-[100px] pointer-events-none" style="background:rgba(109,108,243,.10)"></div>
        <div class="absolute top-1/2 right-1/4 translate-x-1/2 -translate-y-1/2 w-72 h-72 rounded-full blur-[100px] pointer-events-none" style="background:rgba(28,176,238,.10)"></div>

        <div class="relative max-w-2xl mx-auto px-6 py-14">
            <!-- Brand -->
            <div class="text-center mb-8">
                <img src="/logo.png" alt="Doppar" class="h-8 mx-auto mb-3">
                <p class="text-xs text-ink-soft">© [[ date('Y') ]] Doppar. All Rights Reserved.</p>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-8 sm:gap-4 text-sm">
                <div>
                    <h4 class="text-primary font-semibold mb-2">Links</h4>
                    <ul class="space-y-1.5 text-ink-soft">
                        <li><a href="https://doppar.com/versions/4.x/credits" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Credits</a></li>
                        <li><a href="https://doppar.com/versions/4.x/installation" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Getting started</a></li>
                        <li><a href="https://doppar.com/versions/4.x/releases" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Release notes</a></li>
                        <li><a href="https://doppar.com/versions/4.x/contributions" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Contributions</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-primary font-semibold mb-2">Get Involved</h4>
                    <ul class="space-y-1.5 text-ink-soft">
                        <li><a href="https://join.slack.com/t/zuno-global/shared_invite/zt-3xg3sl8vq-giLmT9rxCdDzQ7yEK53NvA" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Join community</a></li>
                        <li><a href="https://github.com/doppar/framework/issues" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Request features</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-primary font-semibold mb-2">Ecosystem</h4>
                    <ul class="space-y-1.5 text-ink-soft">
                        <li><a href="https://doppar.com/versions/4.x/doppar-ai" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">AI</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-queue" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Queue</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-airbend" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Airbend</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-flarion" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Flarion</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-notifier" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Notifier</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-orion" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Orion</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-guard" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Guard</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-axios" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Axios</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-oauthic" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">OAuthic</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-bloom" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Bloom</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-insight" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Insight</a></li>
                        <li><a href="https://doppar.com/versions/4.x/doppar-twig-bridge" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Twig Bridge</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-primary font-semibold mb-2">Connect</h4>
                    <div class="flex flex-col items-start gap-1.5 text-ink-soft">
                        <a href="https://x.com/dopparframework" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">X (Twitter)</a>
                        <a href="https://www.linkedin.com/company/doppar/posts/?feedView=all" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">LinkedIn</a>
                        <a href="https://www.buymeacoffee.com/mahedisulaa" target="_blank" rel="noopener noreferrer" class="mt-1.5 inline-flex items-center gap-1.5 bg-amber-a hover:brightness-95 text-ink text-xs px-3 py-1.5 rounded-md font-semibold transition-all w-fit">
                            ☕ Buy me a coffee
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-center">
                <p class="text-sm font-medium text-ink-soft">Made with Doppar</p>
            </div>
        </div>
    </footer>

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