<div class="w-full overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
    <table {{ $attributes->merge(['class' => 'w-full text-start text-sm font-cairo']) }}>
        {{ $slot }}
    </table>
</div>
