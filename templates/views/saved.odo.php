#extends('layouts.public')

#section('title')[[ 'Saved stories — Doppar News' ]]#endsection

#section('content')

<div class="min-h-screen flex flex-col">

    #include('partials.header')

    <section class="dp-hero" style="padding-bottom: clamp(20px, 3vw, 36px);">
        <div class="dp-wrap">
            <p class="dp-eyebrow">Reading list</p>
            <h1 style="max-width: 12ch;">Your <span class="dp-mark">saved</span> stories.</h1>
            <p class="dp-hero__lede">Posts you have bookmarked to read later, all in one place.</p>
        </div>
    </section>

    <main class="flex-1">
        <div class="dp-wrap" style="padding-top: 8px;">

            <div id="post-list" class="dp-ledger">
                #include('partials.post-list-items', ['posts' => $posts])
            </div>

            #if ($showEmptyState)
            <div class="dp-empty">
                <h3>No saved stories yet.</h3>
                <p>Tap the bookmark on any story to keep it here for later.</p>
                <p style="margin-top:1.4rem"><a href="/" class="dp-btn">Browse stories</a></p>
            </div>
            #endif

            #if ($loadMore['has_more'])
            <div class="dp-loadmore">
                <button type="button" id="load-more-posts" data-next-url="[[ $loadMore['next_url'] ]]" class="dp-btn">
                    Load more stories
                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                </button>
            </div>
            #endif
        </div>
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
