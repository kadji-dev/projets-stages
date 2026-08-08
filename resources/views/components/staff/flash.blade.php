@if (session('success'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 4000)"
        class="mb-6 flex items-center justify-between gap-4 px-5 py-4 rounded-xl bg-[#006444]/10 border border-[#006444]/20 text-[#006444] font-semibold text-sm"
    >
        <span class="flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            {{ session('success') }}
        </span>
        <button type="button" @click="show = false" class="text-[#006444]/60 hover:text-[#006444]">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 px-5 py-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm font-semibold space-y-1">
        @foreach ($errors->all() as $error)
            <p class="flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">error</span>
                {{ $error }}
            </p>
        @endforeach
    </div>
@endif
