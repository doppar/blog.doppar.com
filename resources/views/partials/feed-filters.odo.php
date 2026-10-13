<div class="feed-card">
    <div class="flex items-center justify-between px-5 pt-4 pb-2">
        <h3 class="text-[15px] font-semibold text-ink">Browse</h3>
        #if ($hasFilters)
        <a href="[[ $filterUrl(['category' => null, 'tag' => null]) ]]" class="text-[12px] font-semibold text-primary hover:underline">Clear filters</a>
        #endif
    </div>

    #if (count($categories) > 0)
    <div class="px-3 pb-3">
        <p class="px-2 mb-1 text-[11px] font-semibold uppercase tracking-wider text-ink-soft">Categories</p>
        #foreach ($categories as $category)
        #php
        $categoryActive = $activeCategory && (int) $activeCategory->id === (int) $category->id;
        #endphp
        <a href="[[ $filterUrl(['category' => $categoryActive ? null : $category->slug]) ]]"
           class="filter-row [[ $categoryActive ? 'is-active' : '' ]]"
           aria-current="[[ $categoryActive ? 'true' : 'false' ]]">
            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: [[ $category->accent_color ]];"></span>
            <span class="flex-1 min-w-0 truncate">[[ $category->name ]]</span>
            <span class="filter-count">[[ $category->posts_total ]]</span>
        </a>
        #endforeach
    </div>
    #endif

    #if (count($tags) > 0)
    <div class="border-t border-soft px-5 py-4">
        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-ink-soft">Tags</p>
        <div class="flex flex-wrap gap-2">
            #foreach ($tags as $tag)
            #php
            $tagActive = $activeTag && (int) $activeTag->id === (int) $tag->id;
            #endphp
            <a href="[[ $filterUrl(['tag' => $tagActive ? null : $tag->slug]) ]]"
               class="filter-chip [[ $tagActive ? 'is-active' : '' ]]"
               aria-current="[[ $tagActive ? 'true' : 'false' ]]">
                [[ $tag->name ]]
                <span class="filter-chip__count">[[ $tag->posts_total ]]</span>
            </a>
            #endforeach
        </div>
    </div>
    #endif
</div>
