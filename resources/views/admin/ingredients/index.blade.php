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
                <a class="hover:text-primary" href="{{ route('admin.products.index') }}">Products</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-semibold">Ingredients</span>
            </nav>
            <div class="flex flex-wrap items-end justify-between gap-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Ingredient Management</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Each product has one main ingredient with multiple sub-ingredients.</p>
                </div>
                <button type="button" onclick="openCreateForm()"
                    class="flex items-center gap-xs bg-primary text-white px-md py-sm rounded-xl font-label-md text-label-md shadow-lg shadow-primary/20 hover:bg-primary-fixed-dim hover:text-on-primary-fixed transition-all group">
                    <span class="material-symbols-outlined group-hover:rotate-90 transition-transform">add</span>
                    Add Ingredient
                </button>
            </div>
        </div>

        {{-- ── Filter Bar ───────────────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-sm shadow-sm flex flex-wrap items-center gap-sm">
            <div class="relative w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">science</span>
                <input id="filter-search"
                    class="w-full bg-surface border border-outline-variant/50 rounded-lg pl-10 py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary"
                    placeholder="Search by ingredient or product..." type="text" />
            </div>

            <select id="filter-product"
                class="bg-surface border border-outline-variant/50 rounded-lg px-md py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary min-w-[200px]">
                <option value="all">All Products</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>

            <button type="button" id="btn-search"
                class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-sm py-2 rounded-lg transition-colors font-label-sm">
                <span class="material-symbols-outlined text-[18px]">search</span> Search
            </button>

            <button type="button" id="btn-clear"
                class="hidden flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-sm py-2 rounded-lg transition-colors font-label-sm">
                <span class="material-symbols-outlined text-[18px]">close</span> Clear
            </button>
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
        <div id="ingredient-form" class="hidden bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm">
            <div class="flex items-center justify-between mb-md">
                <div>
                    <h3 id="form-title" class="font-headline-sm text-headline-sm text-on-surface">Add Ingredient</h3>
                    <p class="text-body-sm text-on-surface-variant">One main ingredient per product with unlimited sub-ingredients.</p>
                </div>
                <button type="button" onclick="closeForm()" class="text-on-surface-variant hover:text-primary">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="crud-form" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST" />

                {{-- ── Main Ingredient ── --}}
                <div class="border border-outline-variant/30 rounded-2xl p-md mb-md">
                    <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider mb-md">Main Ingredient</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-md">
                        <div class="lg:col-span-1">
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Product <span class="text-error">*</span></label>
                            <select name="product_id" id="f-product" required
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm">
                                <option value="">— Select Product —</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Ingredient Name <span class="text-error">*</span></label>
                            <input name="name" id="f-name" required placeholder="e.g. Glutathione"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Amount</label>
                            <input name="amount" id="f-amount" placeholder="e.g. 500mg"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm" />
                        </div>
                        <div class="lg:col-span-3">
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Description</label>
                            <textarea name="description" id="f-description" rows="2"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm"></textarea>
                        </div>
                    </div>
                </div>

                {{-- ── Sub-ingredients ── --}}
                <div class="border border-outline-variant/30 rounded-2xl p-md mb-md">
                    <div class="flex items-center justify-between mb-md">
                        <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Sub-Ingredients</p>
                        <button type="button" onclick="addSubRow()"
                            class="flex items-center gap-xs text-primary border border-primary/30 hover:bg-primary/5 px-sm py-1 rounded-lg text-label-sm font-label-sm transition-colors">
                            <span class="material-symbols-outlined text-[16px]">add</span> Add Row
                        </button>
                    </div>

                    {{-- Header --}}
                    <div class="hidden md:grid grid-cols-12 gap-sm px-sm mb-xs">
                        <div class="col-span-3 text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">Name</div>
                        <div class="col-span-2 text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">Amount</div>
                        <div class="col-span-2 text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">Unit</div>
                        <div class="col-span-4 text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">Description</div>
                        <div class="col-span-1"></div>
                    </div>

                    <div id="sub-rows" class="space-y-xs">
                        {{-- rows injected by JS --}}
                    </div>

                    <p id="sub-empty" class="text-label-sm text-on-surface-variant opacity-50 px-sm py-xs">
                        No sub-ingredients yet. Click "Add Row" to begin.
                    </p>
                </div>

                <div class="flex justify-end gap-sm">
                    <button type="button" onclick="closeForm()" class="rounded-lg border border-outline-variant px-md py-2 text-sm">Cancel</button>
                    <button type="submit" class="rounded-lg bg-primary px-md py-2 text-sm text-white">Save Ingredient</button>
                </div>
            </form>
        </div>

        {{-- ── Ingredients Table ────────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse" id="ingredients-table">
                    <thead>
                        <tr class="bg-surface-container-low/50 border-b border-outline-variant/10">
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Product</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Main Ingredient</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Sub-Ingredients</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider text-center">Count</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Description</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    @include('admin.ingredients._table', ['ingredients' => $ingredients])
                </table>
            </div>
        </div>

    </div>
</main>

{{-- ═══ SCRIPTS ════════════════════════════════════════════════════════════════ --}}
<script>
    const FILTER_URL = '{{ route('admin.ingredients.filter') }}';
    const CSRF       = '{{ csrf_token() }}';
    const STORE_URL  = '{{ route('admin.ingredients.store') }}';
    const UPDATE_URL = id => `/admin/ingredients/${id}`;

    // ── Sub-ingredient rows ───────────────────────────────────────────────────
    let subIndex = 0;

    function addSubRow(name = '', amount = '', unit = '', description = '') {
        document.getElementById('sub-empty').classList.add('hidden');
        const i   = subIndex++;
        const row = document.createElement('div');
        row.id    = `sub-row-${i}`;
        row.className = 'grid grid-cols-12 gap-sm items-start px-sm py-xs bg-surface-container/30 rounded-xl border border-outline-variant/20';
        row.innerHTML = `
            <div class="col-span-3">
                <input name="sub[${i}][name]" value="${escHtml(name)}" required placeholder="e.g. Vitamin C"
                    class="w-full rounded-lg border border-outline-variant bg-surface px-sm py-1.5 text-body-sm" />
            </div>
            <div class="col-span-2">
                <input name="sub[${i}][amount]" value="${escHtml(amount)}" placeholder="100"
                    class="w-full rounded-lg border border-outline-variant bg-surface px-sm py-1.5 text-body-sm" />
            </div>
            <div class="col-span-2">
                <input name="sub[${i}][unit]" value="${escHtml(unit)}" placeholder="mg / %DV"
                    class="w-full rounded-lg border border-outline-variant bg-surface px-sm py-1.5 text-body-sm" />
            </div>
            <div class="col-span-4">
                <textarea name="sub[${i}][description]" rows="1" placeholder="Short description…"
                    class="w-full rounded-lg border border-outline-variant bg-surface px-sm py-1.5 text-body-sm resize-none">${escHtml(description)}</textarea>
            </div>
            <div class="col-span-1 flex justify-center pt-1.5">
                <button type="button" onclick="removeSubRow(${i})"
                    class="p-1 text-on-surface-variant hover:text-error rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[18px]">remove_circle</span>
                </button>
            </div>`;
        document.getElementById('sub-rows').appendChild(row);
    }

    function removeSubRow(i) {
        const row = document.getElementById(`sub-row-${i}`);
        if (row) row.remove();
        if (!document.getElementById('sub-rows').children.length) {
            document.getElementById('sub-empty').classList.remove('hidden');
        }
    }

    function clearSubRows() {
        document.getElementById('sub-rows').innerHTML = '';
        subIndex = 0;
        document.getElementById('sub-empty').classList.remove('hidden');
    }

    function escHtml(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // ── Filter helpers ────────────────────────────────────────────────────────
    function getFilters(page = 1) {
        return {
            search:     document.getElementById('filter-search').value.trim(),
            product_id: document.getElementById('filter-product').value,
            page,
        };
    }

    function toggleClearBtn() {
        const f      = getFilters();
        const active = f.search || f.product_id !== 'all';
        document.getElementById('btn-clear').classList.toggle('hidden', !active);
    }

    function setLoading(on) {
        const tb = document.getElementById('ingredient-tbody');
        if (tb) tb.style.opacity = on ? '0.4' : '1';
    }

    // ── AJAX fetch ────────────────────────────────────────────────────────────
    async function fetchIngredients(page = 1) {
        setLoading(true);
        const body = new URLSearchParams({ ...getFilters(page), _token: CSRF });
        try {
            const res  = await fetch(FILTER_URL, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                body,
            });
            const data = await res.json();
            const tbl  = document.getElementById('ingredients-table');
            const tmp  = document.createElement('table');
            tmp.innerHTML = data.html;
            const newTbody = tmp.querySelector('#ingredient-tbody');
            const newTfoot = tmp.querySelector('#ingredient-tfoot');
            const oldTbody = document.getElementById('ingredient-tbody');
            const oldTfoot = document.getElementById('ingredient-tfoot');
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
                if (!isNaN(p)) fetchIngredients(p);
            })
        );
    }

    // ── Filter events ─────────────────────────────────────────────────────────
    document.getElementById('filter-product').addEventListener('change', () => fetchIngredients(1));
    document.getElementById('filter-search').addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); fetchIngredients(1); }
    });
    document.getElementById('btn-search').addEventListener('click', () => fetchIngredients(1));
    document.getElementById('btn-clear').addEventListener('click', () => {
        document.getElementById('filter-search').value  = '';
        document.getElementById('filter-product').value = 'all';
        fetchIngredients(1);
    });

    // ── CRUD Form ─────────────────────────────────────────────────────────────
    function openCreateForm() {
        document.getElementById('form-title').textContent = 'Add Ingredient';
        document.getElementById('crud-form').action       = STORE_URL;
        document.getElementById('form-method').value      = 'POST';
        document.getElementById('f-product').value    = '';
        document.getElementById('f-name').value       = '';
        document.getElementById('f-amount').value     = '';
        document.getElementById('f-description').value = '';
        clearSubRows();
        addSubRow(); // start with one empty row
        document.getElementById('ingredient-form').classList.remove('hidden');
        document.getElementById('ingredient-form').scrollIntoView({ behavior: 'smooth' });
    }

    function openEditForm(id) {
        fetch(`/admin/ingredients/${id}/edit-data`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('form-title').textContent  = 'Edit Ingredient';
            document.getElementById('crud-form').action        = UPDATE_URL(id);
            document.getElementById('form-method').value       = 'PUT';
            document.getElementById('f-product').value         = data.product_id ?? '';
            document.getElementById('f-name').value            = data.name        ?? '';
            document.getElementById('f-amount').value          = data.amount      ?? '';
            document.getElementById('f-description').value     = data.description ?? '';
            clearSubRows();
            (data.sub_ingredients ?? []).forEach(s => addSubRow(s.name, s.amount ?? '', s.unit ?? '', s.description ?? ''));
            if (!(data.sub_ingredients ?? []).length) addSubRow();
            document.getElementById('ingredient-form').classList.remove('hidden');
            document.getElementById('ingredient-form').scrollIntoView({ behavior: 'smooth' });
        });
    }

    function closeForm() {
        document.getElementById('ingredient-form').classList.add('hidden');
    }

    // ── Pre-select product from query string (when coming from product page) ──
    @if(request('product_id'))
        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('filter-product');
            sel.value = '{{ request('product_id') }}';
            fetchIngredients(1);
        });
    @endif

    // ── Init ──────────────────────────────────────────────────────────────────
    bindPageButtons();
    const flash = document.getElementById('flash-success');
    if (flash) setTimeout(() => flash.style.display = 'none', 4000);
</script>
@endsection
