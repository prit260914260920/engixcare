{{-- ═══ TABLE BODY ═══════════════════════════════════════════════════════════ --}}
<tbody id="product-tbody" class="divide-y divide-outline-variant/10">
    @forelse($products as $product)
        <tr class="hover:bg-surface-container-low transition-colors group">

            {{-- Product Info --}}
            <td class="px-md py-md">
                <div class="flex items-center gap-md">
                    <div class="relative flex-shrink-0">
                        @php
                            $images     = $product->image ?? [];
                            $firstImage = is_array($images) && count($images)
                                ? \Illuminate\Support\Facades\Storage::disk('public')->url($images[0])
                                : 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=300&q=80';
                        @endphp
                        <img class="w-14 h-14 rounded-xl object-cover border border-outline-variant/20 shadow-sm"
                            src="{{ $firstImage }}"
                            alt="{{ $product->name }}" />
                        @if($product->badge_label)
                            @php
                                $bColor = match($product->badge_color) {
                                    'green'  => 'bg-emerald-500',
                                    'blue'   => 'bg-blue-500',
                                    'red'    => 'bg-red-500',
                                    'purple' => 'bg-purple-500',
                                    default  => 'bg-orange-500',
                                };
                            @endphp
                            <span class="absolute -top-2 -left-2 {{ $bColor }} text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full leading-tight whitespace-nowrap">
                                {{ $product->badge_label }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <div class="font-label-md text-label-md text-on-surface font-bold group-hover:text-primary transition-colors">{{ $product->name }}</div>
                        <div class="text-label-sm text-on-surface-variant opacity-70">{{ $product->subtitle }}</div>
                        {{-- Star rating --}}
                        @if($product->rating > 0)
                            <div class="flex items-center gap-xs mt-0.5">
                                <span class="text-yellow-400 text-[13px]">★</span>
                                <span class="text-label-sm text-on-surface-variant">{{ number_format($product->rating, 1) }}</span>
                                @if($product->review_count)
                                    <span class="text-label-sm text-on-surface-variant opacity-60">({{ $product->review_count }})</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </td>

            {{-- SKU --}}
            <td class="px-md py-md text-body-sm font-mono text-on-surface-variant">{{ $product->sku }}</td>

            {{-- Category --}}
            <td class="px-md py-md">
                <span class="bg-secondary-container/20 text-on-secondary-container px-sm py-1 rounded-full text-label-sm font-label-sm">{{ $product->category }}</span>
            </td>

            {{-- Price --}}
            <td class="px-md py-md text-body-sm font-semibold text-on-surface">
                @if($product->discounted_price)
                    <span class="text-primary">₹{{ number_format($product->discounted_price, 2) }}</span>
                    @if($product->original_price && $product->original_price > $product->discounted_price)
                        <br><s class="text-on-surface-variant opacity-60 font-normal text-[11px]">₹{{ number_format($product->original_price, 2) }}</s>
                    @endif
                @else
                    ₹{{ number_format($product->price, 2) }}
                @endif
            </td>

            {{-- Stock --}}
            <td class="px-md py-md">
                <div class="flex flex-col gap-0.5">
                    <div class="flex items-center gap-xs">
                        <span class="text-body-sm font-semibold {{ $product->stock < 20 ? 'text-error' : 'text-on-surface' }}">{{ $product->stock }}</span>
                        <span class="w-2 h-2 rounded-full {{ $product->stock < 20 ? 'bg-error animate-pulse' : 'bg-primary-container' }}"></span>
                    </div>
                    @if($product->stock_label)
                        <span class="text-[10px] text-error leading-tight">{{ $product->stock_label }}</span>
                    @endif
                </div>
            </td>

            {{-- Status --}}
            <td class="px-md py-md">
                <div class="flex items-center gap-xs font-semibold text-label-sm px-sm py-1 rounded-full w-fit
                    {{ $product->status === 'Active' ? 'text-primary bg-primary/10' : 'text-on-surface-variant bg-surface-variant/40' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $product->status === 'Active' ? 'bg-primary' : 'bg-outline' }}"></span>
                    {{ $product->status }}
                </div>
            </td>

            {{-- Featured --}}
            <td class="px-md py-md">
                @if($product->featured)
                    <span class="material-symbols-outlined text-tertiary-container text-[20px]" style="font-variation-settings: 'FILL' 1;">stars</span>
                @else
                    <span class="material-symbols-outlined text-outline-variant text-[20px]">stars</span>
                @endif
            </td>

            {{-- Visible --}}
            <td class="px-md py-md">
                @if($product->is_visible)
                    <span class="inline-flex items-center gap-1 text-label-sm font-semibold text-emerald-600 bg-emerald-50 px-sm py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Yes
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-label-sm font-semibold text-error bg-error-container/30 px-sm py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                        No
                    </span>
                @endif
            </td>

            {{-- Stock Line --}}
            <td class="px-md py-md">
                @if($product->show_stock)
                    <span class="inline-flex items-center gap-1 text-label-sm font-semibold text-emerald-600 bg-emerald-50 px-sm py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Shown
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-label-sm font-semibold text-error bg-error-container/30 px-sm py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                        Hidden
                    </span>
                @endif
            </td>

            {{-- Actions --}}
            <td class="px-md py-md text-right">
                <div class="flex items-center justify-end gap-xs">
                    <button onclick="openEditForm({{ $product->id }})"
                        class="p-2 text-on-surface-variant hover:text-primary transition-colors hover:bg-surface-variant rounded-lg"
                        title="Edit">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                    </button>
                    <a href="{{ route('admin.ingredients.index', ['product_id' => $product->id]) }}"
                        class="p-2 text-on-surface-variant hover:text-secondary transition-colors hover:bg-surface-variant rounded-lg"
                        title="Manage Ingredients">
                        <span class="material-symbols-outlined text-[20px]">science</span>
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                        onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')" class="inline">
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
            <td colspan="10" class="px-md py-xl text-center text-on-surface-variant text-body-sm">
                No products found.
            </td>
        </tr>
    @endforelse
</tbody>

{{-- ═══ PAGINATION ════════════════════════════════════════════════════════════ --}}
<tfoot id="product-tfoot">
    <tr>
        <td colspan="10" class="px-md py-md bg-surface-container-low/30 border-t border-outline-variant/10">
            <div class="flex items-center justify-between">
                <p class="text-body-sm text-on-surface-variant">
                    Showing <span class="font-bold text-on-surface">{{ $products->firstItem() ?? 0 }}&nbsp;&ndash;&nbsp;{{ $products->lastItem() ?? 0 }}</span>
                    of <span class="font-bold text-on-surface">{{ $products->total() }}</span> products
                </p>

                @if($products->hasPages())
                    <div class="flex items-center gap-base">
                        <button data-page="1" {{ $products->onFirstPage() ? 'disabled' : '' }}
                            class="page-btn p-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined">first_page</span>
                        </button>
                        <button data-page="{{ $products->currentPage() - 1 }}" {{ $products->onFirstPage() ? 'disabled' : '' }}
                            class="page-btn p-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>

                        <div class="flex items-center gap-1 mx-xs">
                            @php
                                $cur   = $products->currentPage();
                                $last  = $products->lastPage();
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

                        <button data-page="{{ $products->currentPage() + 1 }}" {{ !$products->hasMorePages() ? 'disabled' : '' }}
                            class="page-btn p-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                        <button data-page="{{ $products->lastPage() }}" {{ !$products->hasMorePages() ? 'disabled' : '' }}
                            class="page-btn p-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined">last_page</span>
                        </button>
                    </div>
                @endif
            </div>
        </td>
    </tr>
</tfoot>
