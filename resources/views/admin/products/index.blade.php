@extends('layouts.admin')

@section('content')
@include('admin.partials.sidebar')

<main class="ml-[280px] min-h-screen flex flex-col">
    @include('admin.partials.topbar')

    <div class="p-margin-desktop space-y-gutter">

        {{-- ── Header ──────────────────────────────────────────────────────── --}}
        <div class="flex flex-col gap-xs">
            <nav class="flex items-center gap-xs text-on-surface-variant text-label-sm font-label-sm">
                <a class="hover:text-primary" href="/admin/dashboard">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-semibold">Product Management</span>
            </nav>
            <div class="flex flex-wrap items-end justify-between gap-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Product Management</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Manage your product catalog, card fields, stock and visibility.</p>
                </div>
                <button type="button" onclick="openCreateForm()"
                    class="flex items-center gap-xs bg-primary text-white px-md py-sm rounded-xl font-label-md text-label-md shadow-lg shadow-primary/20 hover:bg-primary-fixed-dim hover:text-on-primary-fixed transition-all group">
                    <span class="material-symbols-outlined group-hover:rotate-90 transition-transform">add</span>
                    Add New Product
                </button>
            </div>
        </div>

        {{-- ── Filter Bar ───────────────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-sm shadow-sm flex flex-wrap items-center justify-between gap-md">
            <div class="flex flex-wrap items-center gap-sm">
                <div class="relative w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">inventory</span>
                    <input id="filter-search"
                        class="w-full bg-surface border border-outline-variant/50 rounded-lg pl-10 py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary"
                        placeholder="Filter by Name or SKU..." type="text" />
                </div>

                <select id="filter-category"
                    class="bg-surface border border-outline-variant/50 rounded-lg px-md py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary min-w-[160px]">
                    <option value="all">All Categories</option>
                    @foreach(['Supplements','Equipment','Personal Care','Diagnostics'] as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>

                <div class="flex bg-surface-container border border-outline-variant/50 rounded-lg p-1" id="status-tabs">
                    @foreach(['all' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive'] as $val => $label)
                        <button type="button" data-status="{{ $val }}"
                            class="status-tab px-sm py-1 rounded-md text-label-sm font-label-sm transition-colors
                                {{ $val === 'all' ? 'bg-surface-container-lowest shadow-sm text-primary' : 'text-on-surface-variant hover:text-primary' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <label class="flex items-center gap-xs cursor-pointer select-none">
                    <div class="relative inline-block w-10 h-5">
                        <input type="checkbox" id="filter-featured" class="opacity-0 w-0 h-0 peer" />
                        <span class="absolute inset-0 bg-surface-variant/50 rounded-full transition-colors peer-checked:bg-primary-container"></span>
                        <span class="absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition-transform peer-checked:translate-x-5"></span>
                    </div>
                    <span class="text-label-sm font-label-sm text-on-surface-variant">Featured Only</span>
                </label>

                <button type="button" id="btn-search"
                    class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-sm py-2 rounded-lg transition-colors font-label-sm">
                    <span class="material-symbols-outlined text-[18px]">search</span> Search
                </button>

                <button type="button" id="btn-clear"
                    class="hidden flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-sm py-2 rounded-lg transition-colors font-label-sm">
                    <span class="material-symbols-outlined text-[18px]">close</span> Clear
                </button>
            </div>
        </div>

        {{-- ── Flash / Errors ───────────────────────────────────────────────── --}}
        @if($errors->any())
            <div class="rounded-2xl border border-error/20 bg-error-container/20 p-md text-sm text-error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div id="flash-success" class="rounded-2xl border border-primary/20 bg-primary/10 p-md text-sm text-primary">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Create / Edit Form ───────────────────────────────────────────── --}}
        <div id="product-form" class="hidden bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm">
            <div class="flex items-center justify-between mb-md">
                <div>
                    <h3 id="form-title" class="font-headline-sm text-headline-sm text-on-surface">Create Product</h3>
                    <p class="text-body-sm text-on-surface-variant">Fill in the product details below.</p>
                </div>
                <button type="button" onclick="closeForm()" class="text-sm text-on-surface-variant hover:text-primary">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="crud-form" method="POST" class="space-y-md">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST" />

                {{-- Section: Basic Info --}}
                <div class="border border-outline-variant/30 rounded-2xl p-md">
                    <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider mb-md">Basic Info</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-md">
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Name <span class="text-error">*</span></label>
                            <input name="name" id="f-name" required class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Subtitle</label>
                            <input name="subtitle" id="f-subtitle" class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">SKU <span class="text-error">*</span></label>
                            <input name="sku" id="f-sku" required class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Category</label>
                            <select name="category" id="f-category" class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm">
                                @foreach(['Supplements','Equipment','Personal Care','Diagnostics'] as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Price <span class="text-error">*</span></label>
                            <input type="number" step="0.01" name="price" id="f-price" value="0.00" required class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Original Price (MRP)
                                <span class="text-on-surface-variant opacity-50 font-normal text-[10px] ml-1">(shown strikethrough)</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="original_price" id="f-original-price"
                                placeholder="0.00"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Discounted Price
                                <span class="text-on-surface-variant opacity-50 font-normal text-[10px] ml-1">(final sale price)</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="discounted_price" id="f-discounted-price"
                                placeholder="0.00"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Stock <span class="text-error">*</span></label>
                            <input type="number" name="stock" id="f-stock" value="0" required class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Status</label>
                            <select name="status" id="f-status" class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        {{-- AJAX Multi-image uploader --}}
                        <div class="md:col-span-2 lg:col-span-3">
                            <label class="text-label-sm font-label-sm text-on-surface-variant">
                                Product Images
                                <span class="text-on-surface-variant opacity-60 font-normal">(select multiple, drag &amp; drop supported)</span>
                            </label>

                            {{-- Drop zone --}}
                            <div id="image-drop-zone"
                                class="mt-1 relative border-2 border-dashed border-outline-variant/60 rounded-xl bg-surface hover:border-primary hover:bg-primary/5 transition-colors cursor-pointer"
                                onclick="document.getElementById('f-images').click()"
                                ondragover="event.preventDefault(); this.classList.add('border-primary','bg-primary/10')"
                                ondragleave="this.classList.remove('border-primary','bg-primary/10')"
                                ondrop="handleImageDrop(event)">
                                <div id="drop-hint" class="flex flex-col items-center justify-center py-6 gap-2">
                                    <span id="upload-spinner" class="hidden material-symbols-outlined text-[40px] text-primary animate-spin">progress_activity</span>
                                    <span id="upload-icon" class="material-symbols-outlined text-[40px] text-on-surface-variant/50">add_photo_alternate</span>
                                    <span class="text-body-sm text-on-surface-variant">Drop images here or <span class="text-primary font-semibold">click to browse</span></span>
                                    <span class="text-label-sm text-on-surface-variant/60">PNG, JPG, WEBP, GIF — max 20 MB each</span>
                                </div>
                                <input type="file" id="f-images" multiple accept="image/*" class="hidden" onchange="handleImageSelect(this.files)" />
                            </div>

                            {{-- Hidden: JSON array of stored paths sent with the form --}}
                            <input type="hidden" id="f-image-paths" name="image_paths" value="[]" />

                            {{-- Preview grid --}}
                            <div id="image-preview-grid" class="mt-sm grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-sm hidden"></div>
                        </div>
                        <div class="md:col-span-2 lg:col-span-3 mt-md">
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Image Padding (top, right, bottom, left)</label>
                            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-sm">
                                <div>
                                    <label class="text-label-xs text-on-surface-variant">Top</label>
                                    <input type="number" min="-200" max="200" name="image_padding_top" id="f-image-padding-top" value="0" class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                                </div>
                                <div>
                                    <label class="text-label-xs text-on-surface-variant">Right</label>
                                    <input type="number" min="-200" max="200" name="image_padding_right" id="f-image-padding-right" value="0" class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                                </div>
                                <div>
                                    <label class="text-label-xs text-on-surface-variant">Bottom</label>
                                    <input type="number" min="-200" max="200" name="image_padding_bottom" id="f-image-padding-bottom" value="0" class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                                </div>
                                <div>
                                    <label class="text-label-xs text-on-surface-variant">Left</label>
                                    <input type="number" min="-200" max="200" name="image_padding_left" id="f-image-padding-left" value="0" class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                                </div>
                            </div>
                        </div>

                        {{-- AJAX Video uploader --}}
                        <div class="md:col-span-2 lg:col-span-3 mt-md">
                            <label class="text-label-sm font-label-sm text-on-surface-variant">
                                Product Videos
                                <span class="text-on-surface-variant opacity-60 font-normal">(MP4, WEBM, MOV — max 100 MB each)</span>
                            </label>

                            {{-- Video Drop zone --}}
                            <div id="video-drop-zone"
                                class="mt-1 relative border-2 border-dashed border-outline-variant/60 rounded-xl bg-surface hover:border-tertiary hover:bg-tertiary/5 transition-colors cursor-pointer"
                                onclick="document.getElementById('f-videos').click()"
                                ondragover="event.preventDefault(); this.classList.add('border-tertiary','bg-tertiary/10')"
                                ondragleave="this.classList.remove('border-tertiary','bg-tertiary/10')"
                                ondrop="handleVideoDrop(event)">
                                <div id="video-drop-hint" class="flex flex-col items-center justify-center py-6 gap-2">
                                    <span id="video-upload-spinner" class="hidden material-symbols-outlined text-[40px] text-tertiary animate-spin">progress_activity</span>
                                    <span id="video-upload-icon" class="material-symbols-outlined text-[40px] text-on-surface-variant/50">video_file</span>
                                    <span class="text-body-sm text-on-surface-variant">Drop videos here or <span class="text-tertiary font-semibold">click to browse</span></span>
                                    <span class="text-label-sm text-on-surface-variant/60">MP4, WEBM, MOV — max 100 MB each</span>
                                </div>
                                <input type="file" id="f-videos" multiple accept="video/*" class="hidden" onchange="handleVideoSelect(this.files)" />
                            </div>

                            {{-- Hidden: JSON array of stored video paths sent with the form --}}
                            <input type="hidden" id="f-video-paths" name="video_paths" value="[]" />

                            {{-- Video preview list --}}
                            <div id="video-preview-list" class="mt-sm space-y-2 hidden"></div>
                        </div>

                        <div class="flex flex-col gap-1 pt-6">
                            <span class="text-label-sm font-label-sm text-on-surface-variant">Featured Product</span>
                            <label class="flex items-center gap-xs cursor-pointer select-none w-fit">
                                <div class="relative inline-block w-11 h-6">
                                    <input type="checkbox" name="featured" id="f-featured" value="1" class="opacity-0 w-0 h-0 peer" />
                                    <span class="absolute inset-0 bg-surface-variant/60 border border-outline-variant/40 rounded-full transition-all duration-200 peer-checked:bg-primary peer-checked:border-primary"></span>
                                    <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow transition-all duration-200 peer-checked:translate-x-5"></span>
                                </div>
                                <span id="f-featured-label" class="text-label-sm text-on-surface-variant">No</span>
                            </label>
                        </div>

                        <div class="flex flex-col gap-1 pt-6">
                            <span class="text-label-sm font-label-sm text-on-surface-variant">Visible on Site</span>
                            <label class="flex items-center gap-xs cursor-pointer select-none w-fit">
                                <div class="relative inline-block w-11 h-6">
                                    <input type="checkbox" name="is_visible" id="f-is-visible" value="1" class="opacity-0 w-0 h-0 peer" checked />
                                    <span class="absolute inset-0 bg-surface-variant/60 border border-outline-variant/40 rounded-full transition-all duration-200 peer-checked:bg-tertiary peer-checked:border-tertiary"></span>
                                    <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow transition-all duration-200 peer-checked:translate-x-5"></span>
                                </div>
                                <span id="f-is-visible-label" class="text-label-sm text-on-surface-variant">Yes</span>
                            </label>
                        </div>

                        <div class="flex flex-col gap-1 pt-6">
                            <span class="text-label-sm font-label-sm text-on-surface-variant">Show Stock Line</span>
                            <label class="flex items-center gap-xs cursor-pointer select-none w-fit">
                                <div class="relative inline-block w-11 h-6">
                                    <input type="checkbox" name="show_stock" id="f-show-stock" value="1" class="opacity-0 w-0 h-0 peer" checked />
                                    <span class="absolute inset-0 bg-surface-variant/60 border border-outline-variant/40 rounded-full transition-all duration-200 peer-checked:bg-tertiary peer-checked:border-tertiary"></span>
                                    <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow transition-all duration-200 peer-checked:translate-x-5"></span>
                                </div>
                                <span id="f-show-stock-label" class="text-label-sm text-on-surface-variant">Yes</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-md">
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Description</label>
                        <textarea name="description" id="f-description" rows="2" class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm"></textarea>
                    </div>
                </div>

                {{-- Section: Card / Display Fields --}}
                <div class="border border-outline-variant/30 rounded-2xl p-md">
                    <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider mb-md">Card Display Fields</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-md">
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Badge Label</label>
                            <input name="badge_label" id="f-badge-label" placeholder="#1 BESTSELLER"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Badge Color</label>
                            <select name="badge_color" id="f-badge-color" class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm">
                                <option value="orange">Orange</option>
                                <option value="green">Green</option>
                                <option value="blue">Blue</option>
                                <option value="red">Red</option>
                                <option value="purple">Purple</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Rating (0–5)</label>
                            <input type="number" step="0.1" min="0" max="5" name="rating" id="f-rating" value="0"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Review Count</label>
                            <input type="number" min="0" name="review_count" id="f-review-count" value="0"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Warning Text</label>
                            <input name="warning_text" id="f-warning-text" placeholder="Contains sucralose (non-caloric sweetener)"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Unit Quantity
                                <span class="text-on-surface-variant opacity-50 font-normal text-[10px] ml-1">(e.g. 15, 100)</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="unit_value" id="f-unit-value"
                                placeholder="15"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Unit Label
                                <span class="text-on-surface-variant opacity-50 font-normal text-[10px] ml-1">(e.g. Tablets, gm, ml)</span>
                            </label>
                            <input name="unit_label" id="f-unit-label"
                                placeholder="Tablets"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Stock Label
                                <span class="text-on-surface-variant opacity-50 font-normal text-[10px] ml-1">(auto-filled from stock)</span>
                            </label>
                            <input name="stock_label" id="f-stock-label" placeholder="Only 8 left at this price!"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                    </div>
                    <div class="mt-md">
                        <label class="text-label-sm font-label-sm text-on-surface-variant">
                            Bullet Points <span class="text-on-surface-variant opacity-60 font-normal">(one per line, shown as ✓ checkmarks)</span>
                        </label>
                        <textarea name="bullet_points" id="f-bullet-points" rows="3"
                            placeholder="Drop in 200 ml water &amp; dissolve&#10;2 tablets daily or as recommended&#10;Tastes best with chilled water"
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm font-mono"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-sm">
                    <button type="button" onclick="closeForm()" class="rounded-lg border border-outline-variant px-md py-2 text-sm">Cancel</button>
                    <button type="submit" class="rounded-lg bg-primary px-md py-2 text-sm text-white">Save Product</button>
                </div>
            </form>
        </div>

        {{-- ── Products Table ───────────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse" id="products-table">
                    <thead>
                        <tr class="bg-surface-container-low/50 border-b border-outline-variant/10">
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Product Info</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">SKU</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Category</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Price</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Stock</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Status</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Featured</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Visible</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Stock Line</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    @include('admin.products._table', ['products' => $products])
                </table>
            </div>
        </div>

    </div>
</main>

{{-- ═══ SCRIPTS ════════════════════════════════════════════════════════════════ --}}
<script>
    const FILTER_URL = '/admin/products/filter';
    const CSRF       = '{{ csrf_token() }}';
    const STORE_URL  = '/admin/products';
    const UPDATE_URL = id => `/admin/products/${id}`;

    let activeStatus = 'all';

    // ── Filter helpers ────────────────────────────────────────────────────────
    function getFilters(page = 1) {
        return {
            search:   document.getElementById('filter-search').value.trim(),
            category: document.getElementById('filter-category').value,
            status:   activeStatus,
            featured: document.getElementById('filter-featured').checked ? '1' : '',
            page,
        };
    }

    function toggleClearBtn() {
        const f      = getFilters();
        const active = f.search || f.category !== 'all' || f.status !== 'all' || f.featured;
        document.getElementById('btn-clear').classList.toggle('hidden', !active);
    }

    function setLoading(on) {
        const tb = document.getElementById('product-tbody');
        if (tb) tb.style.opacity = on ? '0.4' : '1';
    }

    // ── AJAX fetch ────────────────────────────────────────────────────────────
    async function fetchProducts(page = 1) {
        setLoading(true);
        const body = new URLSearchParams({ ...getFilters(page), _token: CSRF });
        try {
            const res  = await fetch(FILTER_URL, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                body,
            });
            const data = await res.json();
            const tbl  = document.getElementById('products-table');
            const tmp  = document.createElement('table');
            tmp.innerHTML = data.html;
            const newTbody = tmp.querySelector('#product-tbody');
            const newTfoot = tmp.querySelector('#product-tfoot');
            const oldTbody = document.getElementById('product-tbody');
            const oldTfoot = document.getElementById('product-tfoot');
            if (oldTbody && newTbody) tbl.replaceChild(newTbody, oldTbody);
            if (oldTfoot && newTfoot) tbl.replaceChild(newTfoot, oldTfoot);
            else if (newTfoot)        tbl.appendChild(newTfoot);
            bindPageButtons();
            toggleClearBtn();
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    }

    function bindPageButtons() {
        document.querySelectorAll('.page-btn').forEach(btn =>
            btn.addEventListener('click', () => {
                const p = parseInt(btn.dataset.page, 10);
                if (!isNaN(p)) fetchProducts(p);
            })
        );
    }

    // ── Filter events ─────────────────────────────────────────────────────────
    document.getElementById('status-tabs').addEventListener('click', e => {
        const btn = e.target.closest('.status-tab');
        if (!btn) return;
        activeStatus = btn.dataset.status;
        document.querySelectorAll('.status-tab').forEach(b => {
            const on = b === btn;
            b.classList.toggle('bg-surface-container-lowest', on);
            b.classList.toggle('shadow-sm', on);
            b.classList.toggle('text-primary', on);
            b.classList.toggle('text-on-surface-variant', !on);
        });
        fetchProducts(1);
    });

    document.getElementById('filter-category').addEventListener('change', () => fetchProducts(1));
    document.getElementById('filter-featured').addEventListener('change', () => fetchProducts(1));
    document.getElementById('filter-search').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); fetchProducts(1); } });
    document.getElementById('btn-search').addEventListener('click', () => fetchProducts(1));
    document.getElementById('btn-clear').addEventListener('click', () => {
        document.getElementById('filter-search').value     = '';
        document.getElementById('filter-category').value  = 'all';
        document.getElementById('filter-featured').checked = false;
        activeStatus = 'all';
        document.querySelectorAll('.status-tab').forEach(b => {
            const on = b.dataset.status === 'all';
            b.classList.toggle('bg-surface-container-lowest', on);
            b.classList.toggle('shadow-sm', on);
            b.classList.toggle('text-primary', on);
            b.classList.toggle('text-on-surface-variant', !on);
        });
        fetchProducts(1);
    });

    // ── Featured toggle label ─────────────────────────────────────────────────
    function syncFeaturedLabel() {
        const chk   = document.getElementById('f-featured');
        const label = document.getElementById('f-featured-label');
        if (!chk || !label) return;
        label.textContent  = chk.checked ? 'Yes' : 'No';
        label.className    = chk.checked
            ? 'text-label-sm text-primary font-semibold'
            : 'text-label-sm text-on-surface-variant';
    }

    // ── Visible toggle label ──────────────────────────────────────────────────
    function syncVisibleLabel() {
        const chk   = document.getElementById('f-is-visible');
        const label = document.getElementById('f-is-visible-label');
        if (!chk || !label) return;
        label.textContent = chk.checked ? 'Yes' : 'No';
        label.className   = chk.checked
            ? 'text-label-sm text-tertiary font-semibold'
            : 'text-label-sm text-error font-semibold';
    }

    // ── Show Stock toggle label ───────────────────────────────────────────────
    function syncShowStockLabel() {
        const chk   = document.getElementById('f-show-stock');
        const label = document.getElementById('f-show-stock-label');
        if (!chk || !label) return;
        label.textContent = chk.checked ? 'Yes' : 'No';
        label.className   = chk.checked
            ? 'text-label-sm text-tertiary font-semibold'
            : 'text-label-sm text-error font-semibold';
    }

    // ── Stock → Stock Label auto-fill ─────────────────────────────────────────
    let stockLabelManuallyEdited = false;

    function autoStockLabel(stock) {
        if (stockLabelManuallyEdited) return;
        const n   = parseInt(stock, 10);
        const lbl = document.getElementById('f-stock-label');
        if (!lbl) return;
        if (isNaN(n) || n <= 0) {
            lbl.value = '';
        } else if (n <= 5) {
            lbl.value = `Only ${n} left at this price!`;
        } else if (n <= 10) {
            lbl.value = `Only ${n} left — selling fast!`;
        } else if (n <= 20) {
            lbl.value = `Low stock — only ${n} remaining`;
        } else if (n <= 50) {
            lbl.value = `${n} in stock`;
        } else {
            lbl.value = '';
        }
    }

    document.getElementById('f-stock').addEventListener('input', function () {
        autoStockLabel(this.value);
    });

    document.getElementById('f-stock-label').addEventListener('input', function () {
        // If the user clears the field entirely, re-enable auto-fill
        stockLabelManuallyEdited = this.value.trim() !== '';
    });

    // ── CRUD Form ─────────────────────────────────────────────────────────────
    function openCreateForm() {
        document.getElementById('form-title').textContent  = 'Create Product';
        document.getElementById('crud-form').action        = STORE_URL;
        document.getElementById('form-method').value       = 'POST';
        // clear all fields
        ['f-name','f-subtitle','f-sku','f-description',
         'f-badge-label','f-warning-text','f-stock-label','f-bullet-points','f-unit-label',
         'f-image-padding-top','f-image-padding-right','f-image-padding-bottom','f-image-padding-left'].forEach(id =>
            document.getElementById(id).value = ''
        );
        document.getElementById('f-price').value            = '0.00';
        document.getElementById('f-original-price').value    = '';
        document.getElementById('f-discounted-price').value  = '';
        document.getElementById('f-stock').value             = '0';
        document.getElementById('f-rating').value       = '0';
        document.getElementById('f-review-count').value = '0';
        document.getElementById('f-unit-value').value   = '';
        document.getElementById('f-status').value       = 'Active';
        document.getElementById('f-category').value     = 'Supplements';
        document.getElementById('f-badge-color').value  = 'orange';
        document.getElementById('f-featured').checked   = false;
        syncFeaturedLabel();
        document.getElementById('f-is-visible').checked  = true;
        syncVisibleLabel();
        document.getElementById('f-show-stock').checked   = true;
        syncShowStockLabel();
        stockLabelManuallyEdited = false;
        autoStockLabel(0);
        resetImageUploader();
        resetVideoUploader();
        document.getElementById('product-form').classList.remove('hidden');
        document.getElementById('product-form').scrollIntoView({ behavior: 'smooth' });
    }

    function openEditForm(id) {
        fetch(`/admin/products/${id}/edit-data`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }
        })
        .then(r => r.json())
        .then(p => {
            document.getElementById('form-title').textContent       = 'Edit Product';
            document.getElementById('crud-form').action             = UPDATE_URL(id);
            document.getElementById('form-method').value            = 'PUT';
            document.getElementById('f-name').value                 = p.name          ?? '';
            document.getElementById('f-subtitle').value             = p.subtitle      ?? '';
            document.getElementById('f-sku').value                  = p.sku           ?? '';
            document.getElementById('f-category').value             = p.category      ?? 'Supplements';
            document.getElementById('f-price').value                = p.price             ?? '0.00';
            document.getElementById('f-original-price').value        = p.original_price    ?? '';
            document.getElementById('f-discounted-price').value      = p.discounted_price  ?? '';
            document.getElementById('f-stock').value                 = p.stock             ?? '0';
            document.getElementById('f-status').value               = p.status        ?? 'Active';
            document.getElementById('f-featured').checked           = !!p.featured;
            syncFeaturedLabel();
            document.getElementById('f-is-visible').checked          = p.is_visible !== false && p.is_visible !== 0;
            syncVisibleLabel();
            document.getElementById('f-show-stock').checked           = p.show_stock !== false && p.show_stock !== 0;
            syncShowStockLabel();
            document.getElementById('f-description').value          = p.description   ?? '';
            document.getElementById('f-badge-label').value          = p.badge_label   ?? '';
            document.getElementById('f-badge-color').value          = p.badge_color   ?? 'orange';
            document.getElementById('f-rating').value               = p.rating        ?? '0';
            document.getElementById('f-review-count').value         = p.review_count  ?? '0';
            document.getElementById('f-warning-text').value         = p.warning_text  ?? '';
            document.getElementById('f-stock-label').value          = p.stock_label   ?? '';
            document.getElementById('f-unit-value').value           = p.unit_value    ?? '';
            document.getElementById('f-image-padding-top').value    = p.image_padding_top ?? '0';
            document.getElementById('f-image-padding-right').value  = p.image_padding_right ?? '0';
            document.getElementById('f-image-padding-bottom').value = p.image_padding_bottom ?? '0';
            document.getElementById('f-image-padding-left').value   = p.image_padding_left ?? '0';
            document.getElementById('f-unit-label').value           = p.unit_label    ?? '';
            document.getElementById('f-bullet-points').value        = p.bullet_points ?? '';
            // If product already has a stock label saved, treat it as manually set
            stockLabelManuallyEdited = !!(p.stock_label && p.stock_label.trim());
            // Load existing images into the uploader
            resetImageUploader();
            if (p.image_urls && p.image_urls.length) {
                p.image_urls.forEach((url, i) => {
                    addExistingPreview(url, p.image_paths[i]);
                });
            }
            // Load existing videos into the uploader
            resetVideoUploader();
            if (p.video_urls && p.video_urls.length) {
                p.video_urls.forEach((url, i) => {
                    addExistingVideoPreview(url, p.video_paths[i]);
                });
            }
            document.getElementById('product-form').classList.remove('hidden');
            document.getElementById('product-form').scrollIntoView({ behavior: 'smooth' });
        });
    }

    function closeForm() {
        document.getElementById('product-form').classList.add('hidden');
        resetImageUploader();
        resetVideoUploader();
    }

    // ── Image uploader (AJAX — uploads immediately, no multipart form) ─────────
    const UPLOAD_URL = '/admin/products/upload-image';
    const DELETE_URL = '/admin/products/delete-image';

    // currentPaths: array of storage paths that will be sent with the form
    let currentPaths = [];

    function resetImageUploader() {
        currentPaths = [];
        syncPathsInput();
        document.getElementById('f-images').value = '';
        const grid = document.getElementById('image-preview-grid');
        grid.innerHTML = '';
        grid.classList.add('hidden');
        setDropHint(false);
    }

    function syncPathsInput() {
        document.getElementById('f-image-paths').value = JSON.stringify(currentPaths);
    }

    function setDropHint(uploading) {
        const grid    = document.getElementById('image-preview-grid');
        const hint    = document.getElementById('drop-hint');
        const icon    = document.getElementById('upload-icon');
        const spinner = document.getElementById('upload-spinner');
        const hasItems = grid.querySelectorAll('.img-thumb').length > 0;

        hint.classList.toggle('hidden', hasItems && !uploading);
        icon.classList.toggle('hidden', uploading);
        spinner.classList.toggle('hidden', !uploading);
        grid.classList.toggle('hidden', !hasItems);
    }

    function handleImageSelect(files) {
        // Upload sequentially to avoid PHP built-in server concurrency issues
        const arr = Array.from(files);
        document.getElementById('f-images').value = '';
        arr.reduce((chain, file) => chain.then(() => uploadOne(file)), Promise.resolve());
    }

    function handleImageDrop(e) {
        e.preventDefault();
        document.getElementById('image-drop-zone').classList.remove('border-primary', 'bg-primary/10');
        handleImageSelect(e.dataTransfer.files);
    }

    async function uploadOne(file) {
        // Show spinner while uploading
        setDropHint(true);

        // Optimistic preview with loading overlay
        const thumb = buildThumb(URL.createObjectURL(file), true, null);
        document.getElementById('image-preview-grid').classList.remove('hidden');
        document.getElementById('image-preview-grid').appendChild(thumb);

        const fd = new FormData();
        fd.append('image', file);
        fd.append('_token', CSRF);

        try {
            const res  = await fetch(UPLOAD_URL, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });
            const text = await res.text();

            let data = {};
            try { data = JSON.parse(text); } catch (_) {
                // Server returned non-JSON (e.g. HTML error page)
                thumb.remove();
                console.error('Server response:', text);
                alert('Upload failed (HTTP ' + res.status + '). See console for details.');
                setDropHint(false);
                return;
            }

            if (res.ok && data.path) {
                // Update thumb to real URL and wire up remove
                thumb.querySelector('img').src = data.url;
                const overlay = thumb.querySelector('.loading-overlay');
                if (overlay) overlay.remove();
                thumb.dataset.path = data.path;
                thumb.querySelector('button').addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeImage(data.path, thumb);
                });
                currentPaths.push(data.path);
                syncPathsInput();
            } else {
                thumb.remove();
                const msg = data.message
                    ?? (data.errors ? Object.values(data.errors).flat().join(', ') : 'HTTP ' + res.status);
                console.error('Upload failed:', res.status, data);
                alert('Upload failed (' + res.status + '): ' + msg);
            }
        } catch (err) {
            thumb.remove();
            console.error('Upload fetch error:', err);
            alert('Upload failed: ' + err.message);
        }

        setDropHint(false);
    }

    async function removeImage(path, thumb) {
        thumb.style.opacity = '0.4';
        try {
            await fetch(DELETE_URL, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ path }),
            });
        } catch (e) { /* silent — file removal is best-effort */ }

        currentPaths = currentPaths.filter(p => p !== path);
        syncPathsInput();
        thumb.remove();
        setDropHint(false);
    }

    function addExistingPreview(url, storagePath) {
        const thumb = buildThumb(url, false, null);
        thumb.dataset.path = storagePath;
        thumb.querySelector('button').addEventListener('click', (e) => {
            e.stopPropagation();
            removeImage(storagePath, thumb);
        });
        document.getElementById('image-preview-grid').appendChild(thumb);
        currentPaths.push(storagePath);
        syncPathsInput();
        setDropHint(false);
    }

    function buildThumb(src, loading, _unused) {
        const wrap = document.createElement('div');
        wrap.className = 'img-thumb relative group rounded-xl overflow-hidden border border-outline-variant/30 aspect-square bg-surface-container';
        wrap.innerHTML = `
            <img src="${src}" class="w-full h-full object-cover" />
            ${loading ? `<div class="loading-overlay absolute inset-0 bg-black/40 flex items-center justify-center">
                <span class="text-white text-xs">Uploading…</span></div>` : ''}
            <button type="button"
                class="absolute top-1 right-1 w-5 h-5 rounded-full bg-error text-white flex items-center justify-center
                       opacity-0 group-hover:opacity-100 transition-opacity shadow-md text-[11px] leading-none"
                title="Remove">✕</button>
        `;
        return wrap;
    }

    // ── Video uploader (AJAX — same pattern as image uploader) ───────────────
    const VIDEO_UPLOAD_URL = '/admin/products/upload-video';
    const VIDEO_DELETE_URL = '/admin/products/delete-video';

    let currentVideoPaths = [];

    function resetVideoUploader() {
        currentVideoPaths = [];
        syncVideoPathsInput();
        document.getElementById('f-videos').value = '';
        const list = document.getElementById('video-preview-list');
        list.innerHTML = '';
        list.classList.add('hidden');
        setVideoDropHint(false);
    }

    function syncVideoPathsInput() {
        document.getElementById('f-video-paths').value = JSON.stringify(currentVideoPaths);
    }

    function setVideoDropHint(uploading) {
        const hint    = document.getElementById('video-drop-hint');
        const icon    = document.getElementById('video-upload-icon');
        const spinner = document.getElementById('video-upload-spinner');
        const list    = document.getElementById('video-preview-list');
        const hasItems = list.querySelectorAll('.vid-item').length > 0;

        hint.classList.toggle('hidden', hasItems && !uploading);
        icon.classList.toggle('hidden', uploading);
        spinner.classList.toggle('hidden', !uploading);
        list.classList.toggle('hidden', !hasItems);
    }

    function handleVideoSelect(files) {
        const arr = Array.from(files);
        document.getElementById('f-videos').value = '';
        arr.reduce((chain, file) => chain.then(() => uploadOneVideo(file)), Promise.resolve());
    }

    function handleVideoDrop(e) {
        e.preventDefault();
        document.getElementById('video-drop-zone').classList.remove('border-tertiary', 'bg-tertiary/10');
        handleVideoSelect(e.dataTransfer.files);
    }

    async function uploadOneVideo(file) {
        setVideoDropHint(true);

        // Optimistic list item with loading state
        const item = buildVideoItem(file.name, true, null);
        document.getElementById('video-preview-list').classList.remove('hidden');
        document.getElementById('video-preview-list').appendChild(item);

        const fd = new FormData();
        fd.append('video', file);
        fd.append('_token', CSRF);

        try {
            const res  = await fetch(VIDEO_UPLOAD_URL, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });
            const text = await res.text();
            let data = {};
            try { data = JSON.parse(text); } catch (_) {
                item.remove();
                alert('Upload failed (HTTP ' + res.status + '). See console.');
                setVideoDropHint(false);
                return;
            }

            if (res.ok && data.path) {
                // Update item to real state and wire up remove
                const loadingEl = item.querySelector('.vid-loading');
                if (loadingEl) loadingEl.remove();
                const nameEl = item.querySelector('.vid-name');
                if (nameEl) nameEl.textContent = file.name;
                item.dataset.path = data.path;
                item.querySelector('button').addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeVideo(data.path, item);
                });
                currentVideoPaths.push(data.path);
                syncVideoPathsInput();
            } else {
                item.remove();
                const msg = data.message ?? (data.errors ? Object.values(data.errors).flat().join(', ') : 'HTTP ' + res.status);
                alert('Upload failed (' + res.status + '): ' + msg);
            }
        } catch (err) {
            item.remove();
            alert('Upload failed: ' + err.message);
        }

        setVideoDropHint(false);
    }

    async function removeVideo(path, item) {
        item.style.opacity = '0.4';
        try {
            await fetch(VIDEO_DELETE_URL, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ path }),
            });
        } catch (e) { /* silent */ }

        currentVideoPaths = currentVideoPaths.filter(p => p !== path);
        syncVideoPathsInput();
        item.remove();
        setVideoDropHint(false);
    }

    function addExistingVideoPreview(url, storagePath) {
        const fileName = storagePath.split('/').pop();
        const item = buildVideoItem(fileName, false, url);
        item.dataset.path = storagePath;
        item.querySelector('button').addEventListener('click', (e) => {
            e.stopPropagation();
            removeVideo(storagePath, item);
        });
        document.getElementById('video-preview-list').appendChild(item);
        currentVideoPaths.push(storagePath);
        syncVideoPathsInput();
        setVideoDropHint(false);
    }

    function buildVideoItem(name, loading, url) {
        const wrap = document.createElement('div');
        wrap.className = 'vid-item flex items-center gap-sm rounded-xl border border-outline-variant/30 bg-surface-container px-sm py-2';
        wrap.innerHTML = `
            <span class="material-symbols-outlined text-[28px] text-tertiary shrink-0">videocam</span>
            <div class="flex-1 min-w-0">
                <p class="vid-name text-body-sm truncate">${name}</p>
                ${loading ? `<span class="vid-loading text-label-xs text-on-surface-variant/60">Uploading…</span>` : ''}
                ${url ? `<a href="${url}" target="_blank" class="text-label-xs text-tertiary hover:underline">Preview</a>` : ''}
            </div>
            <button type="button"
                class="w-7 h-7 rounded-full bg-error/10 text-error hover:bg-error hover:text-white flex items-center justify-center transition-colors shrink-0 text-[13px]"
                title="Remove">✕</button>
        `;
        return wrap;
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    bindPageButtons();
    const flash = document.getElementById('flash-success');
    if (flash) setTimeout(() => flash.style.display = 'none', 4000);

    document.getElementById('f-featured').addEventListener('change', syncFeaturedLabel);
    document.getElementById('f-is-visible').addEventListener('change', syncVisibleLabel);
    syncVisibleLabel(); // init label on page load
    document.getElementById('f-show-stock').addEventListener('change', syncShowStockLabel);
    syncShowStockLabel(); // init label on page load

    // Auto-open edit form after save (redirect returns ?edit=ID)
    @if(isset($editingProduct) && $editingProduct)
        openEditForm({{ $editingProduct->id }});
    @endif
</script>
@endsection
