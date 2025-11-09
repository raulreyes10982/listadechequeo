<div x-data="{ openGroup: null }" class="space-y-6">
    @foreach ($navigation as $group)
        <div class="space-y-1">
            <button
                @click="openGroup = openGroup === '{{ $group->label }}' ? null : '{{ $group->label }}'"
                type="button"
                class="flex items-center justify-between w-full px-3 py-2 font-semibold text-left text-gray-800 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800"
            >
                <span>{{ $group->label }}</span>
                <svg
                    class="w-4 h-4 transform transition-transform duration-200"
                    :class="{ 'rotate-90': openGroup === '{{ $group->label }}' }"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div x-show="openGroup === '{{ $group->label }}'" x-collapse>
                <div class="pl-4 space-y-1">
                    @foreach ($group->items as $item)
                        {{ $item }}
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
