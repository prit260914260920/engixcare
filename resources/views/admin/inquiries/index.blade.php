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
                <span class="text-primary font-semibold">Customer Inquiries</span>
            </nav>
            <div class="flex flex-wrap items-end justify-between gap-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Customer Inquiries</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">All messages submitted through the contact form are stored here.</p>
                </div>
                <div class="flex flex-wrap items-center gap-sm">
                    <button type="button" onclick="window.location.reload()"
                        class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-sm py-2 rounded-lg transition-colors font-label-sm">
                        <span class="material-symbols-outlined text-[18px]">refresh</span> Refresh
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Filter Bar ───────────────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-sm shadow-sm flex flex-wrap items-center justify-between gap-md">
            <div class="flex flex-wrap items-center gap-sm">
                <div class="relative w-80">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">contact_support</span>
                    <input id="filter-search"
                        class="w-full bg-surface border border-outline-variant/50 rounded-lg pl-10 py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary"
                        placeholder="Search by name, email, or message..." type="text" />
                </div>

                <div class="flex bg-surface-container border border-outline-variant/50 rounded-lg p-1" id="status-tabs">
                    @foreach(['all' => 'All', 'unread' => 'Unread', 'read' => 'Read'] as $val => $label)
                        <button type="button" data-status="{{ $val }}"
                            class="status-tab px-sm py-1 rounded-md text-label-sm font-label-sm transition-colors
                                {{ $val === 'all' ? 'bg-surface-container-lowest shadow-sm text-primary' : 'text-on-surface-variant hover:text-primary' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

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

        {{-- ── Inquiries Table ─────────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse" id="inquiries-table">
                    <thead>
                        <tr class="bg-surface-container-low/50 border-b border-outline-variant/10">
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Date</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Name</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Email</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Phone</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Message</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                    @forelse($messages as $message)
                        <tr class="hover:bg-surface-container-low transition-colors group" data-name="{{ strtolower($message->name) }}" data-email="{{ strtolower($message->email) }}" data-message="{{ strtolower($message->message) }}">
                            <td class="px-md py-md text-body-sm text-on-surface-variant whitespace-nowrap">
                                {{ $message->created_at->format('d M Y') }}
                                <div class="text-label-sm text-on-surface-variant/60">{{ $message->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-md py-md">
                                <div class="flex items-center gap-sm">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                        <span class="text-label-md font-bold text-primary">{{ strtoupper(substr($message->name, 0, 1)) }}</span>
                                    </div>
                                    <span class="font-label-md text-label-md text-on-surface font-semibold group-hover:text-primary transition-colors">{{ $message->name }}</span>
                                </div>
                            </td>
                            <td class="px-md py-md">
                                <a href="mailto:{{ $message->email }}" class="text-body-sm text-primary hover:underline">{{ $message->email }}</a>
                            </td>
                            <td class="px-md py-md text-body-sm text-on-surface-variant">
                                @if($message->phone)
                                    <a href="tel:{{ $message->phone }}" class="hover:text-primary transition-colors">{{ $message->phone }}</a>
                                @else
                                    <span class="text-on-surface-variant/40">—</span>
                                @endif
                            </td>
                            <td class="px-md py-md text-body-sm text-on-surface break-words max-w-sm">
                                <div class="line-clamp-2">{{ $message->message }}</div>
                            </td>
                            <td class="px-md py-md text-right">
                                <div class="flex items-center justify-end gap-xs">
                                    {{-- View / Expand --}}
                                    <button type="button"
                                        onclick="openInquiryModal({{ $message->id }}, '{{ addslashes($message->name) }}', '{{ addslashes($message->email) }}', '{{ $message->phone ?? '' }}', '{{ addslashes($message->message) }}', '{{ $message->created_at->format('d M Y H:i') }}')"
                                        class="p-2 text-on-surface-variant hover:text-primary transition-colors hover:bg-surface-variant rounded-lg"
                                        title="View Full Message">
                                        <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                                    </button>
                                    {{-- Delete --}}
                                    <form action="{{ route('admin.inquiries.destroy', $message) }}" method="POST"
                                        onsubmit="return confirm('Delete this inquiry from {{ addslashes($message->name) }}?')" class="inline">
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
                                No inquiries have been submitted yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                    {{-- ── Pagination (inside table footer, like products) ─────── --}}
                    @if($messages->hasPages())
                    <tfoot>
                        <tr>
                            <td colspan="6" class="px-md py-md bg-surface-container-low/30 border-t border-outline-variant/10">
                                <div class="flex items-center justify-between">
                                    <p class="text-body-sm text-on-surface-variant">
                                        Showing <span class="font-bold text-on-surface">{{ $messages->firstItem() ?? 0 }}&nbsp;&ndash;&nbsp;{{ $messages->lastItem() ?? 0 }}</span>
                                        of <span class="font-bold text-on-surface">{{ $messages->total() }}</span> inquiries
                                    </p>
                                    <div class="flex items-center gap-base">
                                        {{ $messages->links() }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                    @else
                    <tfoot>
                        <tr>
                            <td colspan="6" class="px-md py-md bg-surface-container-low/30 border-t border-outline-variant/10">
                                <p class="text-body-sm text-on-surface-variant">
                                    Total: <span class="font-bold text-on-surface">{{ $messages->total() }}</span> inquiries
                                </p>
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</main>

{{-- ── View Full Message Modal ─────────────────────────────────────────────── --}}
<div id="inquiry-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-md">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeInquiryModal()"></div>

    {{-- Modal Card --}}
    <div class="relative bg-surface rounded-3xl shadow-2xl w-full max-w-lg p-xl space-y-md z-10">
        <div class="flex items-start justify-between gap-md">
            <div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface" id="modal-name">—</h3>
                <p class="text-body-sm text-on-surface-variant" id="modal-date">—</p>
            </div>
            <button onclick="closeInquiryModal()" class="text-on-surface-variant hover:text-primary transition-colors flex-shrink-0">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="space-y-sm">
            <div class="flex items-center gap-sm">
                <span class="material-symbols-outlined text-[18px] text-primary">mail</span>
                <a id="modal-email" href="#" class="text-body-sm text-primary hover:underline">—</a>
            </div>
            <div class="flex items-center gap-sm" id="modal-phone-row">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">phone</span>
                <a id="modal-phone" href="#" class="text-body-sm text-on-surface hover:text-primary transition-colors">—</a>
            </div>
        </div>

        <div class="bg-surface-container-low rounded-2xl p-md">
            <p class="text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider mb-sm">Message</p>
            <p id="modal-message" class="text-body-md text-on-surface leading-relaxed">—</p>
        </div>

        <div class="flex justify-end gap-sm pt-sm">
            <a id="modal-mailto" href="#"
                class="flex items-center gap-xs bg-primary text-white px-md py-sm rounded-xl font-label-md text-label-md shadow-lg shadow-primary/20 hover:bg-primary-fixed-dim transition-all">
                <span class="material-symbols-outlined text-[18px]">reply</span> Reply via Email
            </a>
            <button onclick="closeInquiryModal()"
                class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-md py-sm rounded-xl font-label-md transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ── Client-side search ────────────────────────────────────────────────────
    const searchInput = document.getElementById('filter-search');
    const btnSearch   = document.getElementById('btn-search');
    const btnClear    = document.getElementById('btn-clear');
    const tbody       = document.querySelector('#inquiries-table tbody');

    function filterRows() {
        const q = searchInput.value.trim().toLowerCase();
        tbody.querySelectorAll('tr[data-name]').forEach(row => {
            const matches = !q
                || row.dataset.name.includes(q)
                || row.dataset.email.includes(q)
                || row.dataset.message.includes(q);
            row.style.display = matches ? '' : 'none';
        });
        btnClear.classList.toggle('hidden', !q);
    }

    btnSearch.addEventListener('click', filterRows);
    searchInput.addEventListener('input', filterRows);
    searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') filterRows(); });

    btnClear.addEventListener('click', () => {
        searchInput.value = '';
        filterRows();
    });

    // ── Status tabs (visual only — full filter requires server-side) ──────────
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.status-tab').forEach(t => {
                t.classList.remove('bg-surface-container-lowest', 'shadow-sm', 'text-primary');
                t.classList.add('text-on-surface-variant');
            });
            tab.classList.add('bg-surface-container-lowest', 'shadow-sm', 'text-primary');
            tab.classList.remove('text-on-surface-variant');
        });
    });

    // ── Modal ─────────────────────────────────────────────────────────────────
    function openInquiryModal(id, name, email, phone, message, date) {
        document.getElementById('modal-name').textContent    = name;
        document.getElementById('modal-date').textContent    = date;
        document.getElementById('modal-email').textContent   = email;
        document.getElementById('modal-email').href          = 'mailto:' + email;
        document.getElementById('modal-mailto').href         = 'mailto:' + email;
        document.getElementById('modal-message').textContent = message;

        const phoneRow = document.getElementById('modal-phone-row');
        if (phone) {
            document.getElementById('modal-phone').textContent = phone;
            document.getElementById('modal-phone').href        = 'tel:' + phone;
            phoneRow.classList.remove('hidden');
        } else {
            phoneRow.classList.add('hidden');
        }

        const modal = document.getElementById('inquiry-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeInquiryModal() {
        const modal = document.getElementById('inquiry-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Auto-dismiss flash
    const flash = document.getElementById('flash-success');
    if (flash) setTimeout(() => flash.style.display = 'none', 4000);
</script>
@endpush

@endsection
