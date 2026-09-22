{{-- ═══ TABLE BODY ═══════════════════════════════════════════════════════════ --}}
<tbody id="ingredient-tbody" class="divide-y divide-outline-variant/10">
    @forelse($ingredients as $ingredient)
        <tr class="hover:bg-surface-container-low transition-colors group">

            {{-- Product --}}
            <td class="px-md py-md">
                <div class="flex items-center gap-sm">
                    @php
                        $productImage = $ingredient->product->image ?? null;
                        $imgSrc = is_array($productImage) && count($productImage)
                            ? \Illuminate\Support\Facades\Storage::disk('public')->url($productImage[0])
                            : 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=300&q=80';
                    @endphp
                    @php
                        $images     = $ingredient->product->image ?? [];
                        $firstImage = is_array($images) && count($images)
                            ? \Illuminate\Support\Facades\Storage::disk('public')->url($images[0])
                            : 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=300&q=80';
                    @endphp
                    <img class="w-14 h-14 rounded-xl object-cover border border-outline-variant/20 shadow-sm"
                        src="{{ $firstImage }}"
                        alt="{{ $ingredient->product->name ?? '' }}" />
                    <span class="font-label-md text-label-md text-on-surface font-bold group-hover:text-primary transition-colors">
                        {{ $ingredient->product->name ?? '—' }}
                    </span>
                </div>
            </td>

            {{-- Main Ingredient --}}
            <td class="px-md py-md">
                <div class="font-semibold text-body-sm text-on-surface">{{ $ingredient->name }}</div>
                @if($ingredient->amount)
                    <div class="text-label-sm text-on-surface-variant opacity-70">{{ $ingredient->amount }}</div>
                @endif
            </td>

            {{-- Sub-ingredients --}}
            <td class="px-md py-md">
                @if($ingredient->subIngredients->count())
                    <div class="flex flex-wrap gap-xs">
                        @foreach($ingredient->subIngredients as $sub)
                            <span class="bg-surface-container border border-outline-variant/30 rounded-full px-sm py-0.5 text-label-sm text-on-surface-variant">
                                {{ $sub->name }}@if($sub->amount) <span class="text-primary font-semibold">{{ $sub->amount }}{{ $sub->unit }}</span>@endif
                            </span>
                        @endforeach
                    </div>
                @else
                    <span class="text-label-sm text-on-surface-variant opacity-50">No sub-ingredients</span>
                @endif
            </td>

            {{-- Count --}}
            <td class="px-md py-md text-center">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary/10 text-primary font-bold text-label-sm">
                    {{ $ingredient->subIngredients->count() }}
                </span>
            </td>

            {{-- Description --}}
            <td class="px-md py-md text-body-sm text-on-surface-variant max-w-[220px] truncate">
                {{ $ingredient->description ?? '—' }}
            </td>

            {{-- Actions --}}
            <td class="px-md py-md text-right">
                <div class="flex items-center justify-end gap-xs">
                    <button onclick="openEditForm({{ $ingredient->id }})"
                        class="p-2 text-on-surface-variant hover:text-primary transition-colors hover:bg-surface-variant rounded-lg" title="Edit">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                    </button>
                    <form action="/admin/ingredients/{{ $ingredient->id }}" method="POST"
                        onsubmit="return confirm('Delete ingredient for {{ addslashes($ingredient->product->name ?? '') }}?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="p-2 text-on-surface-variant hover:text-error transition-colors hover:bg-error-container/20 rounded-lg" title="Delete">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="px-md py-xl text-center text-on-surface-variant text-body-sm">
                No ingredients found.
            </td>
        </tr>
    @endforelse
</tbody>

{{-- ═══ PAGINATION ════════════════════════════════════════════════════════════ --}}
<tfoot id="ingredient-tfoot">
    <tr>
        <td colspan="6" class="px-md py-md bg-surface-container-low/30 border-t border-outline-variant/10">
            <div class="flex items-center justify-between">
                <p class="text-body-sm text-on-surface-variant">
                    Showing <span class="font-bold text-on-surface">{{ $ingredients->firstItem() ?? 0 }}&nbsp;&ndash;&nbsp;{{ $ingredients->lastItem() ?? 0 }}</span>
                    of <span class="font-bold text-on-surface">{{ $ingredients->total() }}</span> ingredients
                </p>

                @if($ingredients->hasPages())
                    <div class="flex items-center gap-base">
                        <button data-page="1" {{ $ingredients->onFirstPage() ? 'disabled' : '' }}
                            class="page-btn p-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined">first_page</span>
                        </button>
                        <button data-page="{{ $ingredients->currentPage() - 1 }}" {{ $ingredients->onFirstPage() ? 'disabled' : '' }}
                            class="page-btn p-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>

                        <div class="flex items-center gap-1 mx-xs">
                            @php
                                $cur   = $ingredients->currentPage();
                                $last  = $ingredients->lastPage();
                                $start = max(1, $cur - 2);
                                $end   = min($last, $cur + 2);
                            @endphp
                            @if($start > 1)
                                <button data-page="1" class="page-btn w-8 h-8 rounded-lg hover:bg-surface-variant text-on-surface-variant font-medium text-label-md flex items-center justify-center">1</button>
                                @if($start > 2)<span class="px-1 text-on-surface-variant">...</span>@endif
                            @endif
                            @for($p = $start; $p <= $end; $p++)
                                <button data-page="{{ $p }}"
                                    class="page-btn w-8 h-8 rounded-lg font-medium text-label-md flex items-center justify-center
                                        {{ $p === $cur ? 'bg-primary text-white' : 'hover:bg-surface-variant text-on-surface-variant' }}">
                                    {{ $p }}
                                </button>
                            @endfor
                            @if($end < $last)
                                @if($end < $last - 1)<span class="px-1 text-on-surface-variant">...</span>@endif
                                <button data-page="{{ $last }}" class="page-btn w-8 h-8 rounded-lg hover:bg-surface-variant text-on-surface-variant font-medium text-label-md flex items-center justify-center">{{ $last }}</button>
                            @endif
                        </div>

                        <button data-page="{{ $ingredients->currentPage() + 1 }}" {{ !$ingredients->hasMorePages() ? 'disabled' : '' }}
                            class="page-btn p-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                        <button data-page="{{ $ingredients->lastPage() }}" {{ !$ingredients->hasMorePages() ? 'disabled' : '' }}
                            class="page-btn p-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined">last_page</span>
                        </button>
                    </div>
                @endif
            </div>
        </td>
    </tr>
</tfoot>
