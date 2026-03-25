<nav class="overflow-x-auto" aria-label="Breadcrumb">
    <ol class="flex min-w-max items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
        @foreach ($items as $item)
            <li class="flex items-center gap-2">
                @if (! $loop->first)
                    <span class="text-slate-300 dark:text-slate-600">/</span>
                @endif

                @if ($item['href'] && ! $item['current'])
                    <a href="{{ $item['href'] }}" wire:navigate class="font-medium text-slate-500 hover:text-sky-600 dark:text-slate-300 dark:hover:text-sky-300">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="{{ $item['current'] ? 'font-semibold text-slate-800 dark:text-slate-100' : '' }}">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
