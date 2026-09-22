<header class="sticky top-0 z-40 flex justify-between items-center h-16 px-margin-desktop bg-surface/80 backdrop-blur-md border-b border-outline-variant/20 shadow-sm">
    <div class="flex items-center gap-md">
        <div class="relative group">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-outline text-[20px]">search</span>
            </span>
            <input class="bg-surface-container-low border-none rounded-xl pl-10 pr-md py-2 w-72 text-body-sm focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Search data, patients, orders..." type="text"/>
        </div>
    </div>
    <div class="flex items-center gap-md">
        <button class="bg-primary text-on-primary px-sm py-2 rounded-lg font-label-md text-label-md hover:bg-primary/90 transition-all shadow-sm flex items-center gap-xs">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Quick Add
        </button>
        <div class="flex items-center gap-sm border-l border-outline-variant/30 pl-md">
            <button class="text-on-surface-variant hover:text-primary transition-colors relative">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-0 right-0 w-2 h-2 bg-error rounded-full border-2 border-white"></span>
            </button>
            <button class="text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined">mail</span>
            </button>
            <button class="text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined">dark_mode</span>
            </button>
            <div class="flex items-center gap-xs ml-sm">
                <div class="w-8 h-8 rounded-full border border-primary/20 bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <span class="text-label-md font-bold text-on-surface">{{ Auth::user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="ml-sm">
                @csrf
                <button type="submit" class="flex items-center gap-xs text-on-surface-variant hover:text-error transition-colors text-sm" title="Logout">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                </button>
            </form>
        </div>
    </div>
</header>
