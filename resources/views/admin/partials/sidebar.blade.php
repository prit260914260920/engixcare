@php
    $current = request()->path(); // e.g. "admin/products", "admin/ingredients"

    $navActive   = 'flex items-center gap-sm px-md py-xs bg-primary-container/10 text-primary border-l-4 border-primary font-bold transition-all duration-200';
    $navInactive = 'flex items-center gap-sm px-md py-xs text-on-surface-variant hover:bg-surface-variant/50 transition-colors group';

    $isActive = fn(string $segment): bool => str_starts_with($current, $segment);
@endphp

<aside class="fixed left-0 top-0 h-full w-[280px] bg-surface-container-low/80 backdrop-blur-xl border-r border-outline-variant/30 z-50 flex flex-col py-md overflow-hidden">
    <div class="px-md mb-xl">
        <h1 class="text-headline-md font-headline-md font-bold text-primary">EngixCare</h1>
        <p class="text-label-md font-label-md text-on-surface-variant opacity-70">Admin Dashboard</p>
    </div>

    <nav class="flex-1 overflow-y-auto custom-scrollbar px-sm space-y-1">

        <a class="{{ $isActive('admin/dashboard') ? $navActive : $navInactive }}" href="/admin/dashboard">
            <span class="material-symbols-outlined {{ $isActive('admin/dashboard') ? '' : 'group-hover:text-primary' }}"
                  @if($isActive('admin/dashboard')) style="font-variation-settings:'FILL' 1" @endif>dashboard</span>
            <span class="font-label-md text-label-md">Dashboard</span>
        </a>

        <a class="{{ $isActive('admin/products') ? $navActive : $navInactive }}" href="{{ route('admin.products.index') }}">
            <span class="material-symbols-outlined {{ $isActive('admin/products') ? '' : 'group-hover:text-primary' }}"
                  @if($isActive('admin/products')) style="font-variation-settings:'FILL' 1" @endif>inventory_2</span>
            <span class="font-label-md text-label-md">Products</span>
        </a>

        <a class="{{ $isActive('admin/ingredients') ? $navActive : $navInactive }}" href="{{ route('admin.ingredients.index') }}">
            <span class="material-symbols-outlined {{ $isActive('admin/ingredients') ? '' : 'group-hover:text-primary' }}"
                  @if($isActive('admin/ingredients')) style="font-variation-settings:'FILL' 1" @endif>science</span>
            <span class="font-label-md text-label-md">Ingredients</span>
        </a>

        <a class="{{ $isActive('admin/hero') ? $navActive : $navInactive }}" href="{{ route('admin.hero.index') }}">
            <span class="material-symbols-outlined {{ $isActive('admin/hero') ? '' : 'group-hover:text-primary' }}"
                  @if($isActive('admin/hero')) style="font-variation-settings:'FILL' 1" @endif>web_stories</span>
            <span class="font-label-md text-label-md">Hero Section</span>
        </a>

        <a class="{{ $isActive('admin/articles') ? $navActive : $navInactive }}" href="#">
            <span class="material-symbols-outlined group-hover:text-primary">article</span>
            <span class="font-label-md text-label-md">Articles / Blog</span>
        </a>

        <a class="{{ $isActive('admin/promotions') ? $navActive : $navInactive }}" href="{{ route('admin.promotions.index') }}">
            <span class="material-symbols-outlined group-hover:text-primary">campaign</span>
            <span class="font-label-md text-label-md">Offers &amp; Promotions</span>
        </a>

        <a class="{{ $isActive('admin/coupons') ? $navActive : $navInactive }}" href="{{ route('admin.coupons.index') }}">
            <span class="material-symbols-outlined {{ $isActive('admin/coupons') ? '' : 'group-hover:text-primary' }}"
                  @if($isActive('admin/coupons')) style="font-variation-settings:'FILL' 1" @endif>confirmation_number</span>
            <span class="font-label-md text-label-md">Coupons</span>
        </a>

        <a class="{{ $isActive('admin/orders') ? $navActive : $navInactive }}" href="{{ route('admin.orders.index') }}">
            <span class="material-symbols-outlined {{ $isActive('admin/orders') ? '' : 'group-hover:text-primary' }}"
                  @if($isActive('admin/orders')) style="font-variation-settings:'FILL' 1" @endif>shopping_bag</span>
            <span class="font-label-md text-label-md">Orders</span>
        </a>

        <a class="{{ $isActive('admin/payments') ? $navActive : $navInactive }}" href="{{ route('admin.payments.index') }}">
            <span class="material-symbols-outlined {{ $isActive('admin/payments') ? '' : 'group-hover:text-primary' }}"
                  @if($isActive('admin/payments')) style="font-variation-settings:'FILL' 1" @endif>payments</span>
            <span class="font-label-md text-label-md">Payments</span>
        </a>

        <a class="{{ $isActive('admin/users') ? $navActive : $navInactive }}" href="#">
            <span class="material-symbols-outlined group-hover:text-primary">group</span>
            <span class="font-label-md text-label-md">Users</span>
        </a>

        <a class="{{ $isActive('admin/reviews') ? $navActive : $navInactive }}" href="{{ route('admin.reviews.index') }}">
            <span class="material-symbols-outlined {{ $isActive('admin/reviews') ? '' : 'group-hover:text-primary' }}"
                  @if($isActive('admin/reviews')) style="font-variation-settings:'FILL' 1" @endif>star</span>
            <span class="font-label-md text-label-md">Reviews &amp; Ratings</span>
        </a>

        <a class="{{ $isActive('admin/inquiries') ? $navActive : $navInactive }}" href="{{ route('admin.inquiries.index') }}">
            <span class="material-symbols-outlined {{ $isActive('admin/inquiries') ? '' : 'group-hover:text-primary' }}"
                  @if($isActive('admin/inquiries')) style="font-variation-settings:'FILL' 1" @endif>contact_support</span>
            <span class="font-label-md text-label-md">Customer Inquiries</span>
        </a>

        <div class="pt-md mt-md border-t border-outline-variant/20">
            <a class="{{ $isActive('admin/settings') ? $navActive : $navInactive }}" href="#">
                <span class="material-symbols-outlined group-hover:text-primary">settings</span>
                <span class="font-label-md text-label-md">General Settings</span>
            </a>
            <a class="{{ $isActive('admin/profile') ? $navActive : $navInactive }}" href="#">
                <span class="material-symbols-outlined group-hover:text-primary">account_circle</span>
                <span class="font-label-md text-label-md">Profile</span>
            </a>
            <a class="flex items-center gap-sm px-md py-xs text-error hover:bg-error/5 transition-colors group" href="#">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-label-md text-label-md">Logout</span>
            </a>
        </div>

    </nav>
</aside>
