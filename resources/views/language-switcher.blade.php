@php
    $renderFlagSvg = function (?string $flagCode) {
        if (! $flagCode) {
            return null;
        }
        $flagCode = strtolower($flagCode);
        $prefixes = ['flags-', 'flags-1x1-', 'flags-4x3-', 'flag-country-', 'flag-1x1-', 'flag-4x3-', 'flag-'];
        foreach ($prefixes as $prefix) {
            try {
                return svg($prefix . $flagCode)->toHtml();
            } catch (\Throwable $e) {
                // Try next prefix
            }
        }
        return null;
    };
@endphp

<div @if($floating ?? false) style="position: fixed; top: 0.75rem; inset-inline-end: 1rem; z-index: 50;" @endif class="fi-dropdown-language-switcher flex items-center">
    <x-filament::dropdown placement="bottom-end" maxHeight="36rem" teleport>
        <x-slot name="trigger" style="display: flex; align-items: center; justify-content: center; padding: 0.25rem;">
            <button
                type="button"
                style="width: 2.15rem; height: 2.15rem; min-width: 2.15rem; max-width: 2.15rem; border-radius: 9999px; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; border: 1px solid rgba(0, 0, 0, 0.12); background-color: #ffffff; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06); transition: all 0.15s ease; padding: 0;"
                class="hover:scale-105 hover:ring-2 hover:ring-primary-500/40 focus:outline-none dark:border-white/20 dark:bg-gray-800"
                title="{{ $currentLanguage['name'] ?? 'Language' }}"
            >
                @php
                    $triggerSvg = ($showFlags ?? true) ? $renderFlagSvg($currentLanguage['flag'] ?? null) : null;
                @endphp
                @if ($triggerSvg)
                    <span class="fi-flag-trigger" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 9999px;">
                        {!! $triggerSvg !!}
                    </span>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.25rem; height: 1.25rem; color: #475569;" class="dark:text-gray-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.5m10.5-3.5A38.256 38.256 0 0 1 9 10.5" />
                    </svg>
                @endif
            </button>
        </x-slot>

        <div style="padding: 0.35rem; min-width: 11.5rem; display: flex; flex-direction: column; gap: 0.25rem;">
            @foreach ($otherLanguages as $language)
                @php
                    $isCurrent = isset($currentLanguage) && ($currentLanguage['code'] === $language['code']);
                    $itemSvg = ($showFlags ?? true) ? $renderFlagSvg($language['flag'] ?? null) : null;
                @endphp
                <a
                    href="{{ route('filament-language-switcher.switch', ['code' => $language['code']]) }}"
                    style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; text-decoration: none; transition: all 0.15s ease; {{ $isCurrent ? 'background-color: rgba(14, 165, 233, 0.1); color: #0284c7; font-weight: 600;' : 'color: #1e293b; font-weight: 500;' }}"
                    class="{{ $isCurrent ? 'dark:bg-primary-500/20 dark:text-primary-300' : 'hover:bg-slate-100 hover:text-slate-900 dark:text-gray-200 dark:hover:bg-white/10 dark:hover:text-white' }}"
                >
                    <div style="display: flex; align-items: center; min-width: 0; flex: 1;">
                        @if ($showFlags ?? true)
                            <div class="fi-flag-badge" style="width: 1.35rem; height: 1.35rem; min-width: 1.35rem; max-width: 1.35rem; border-radius: 9999px; overflow: hidden; margin-inline-end: 0.75rem; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.12);" class="dark:ring-white/20">
                                @if ($itemSvg)
                                    {!! $itemSvg !!}
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1rem; height: 1rem; color: #64748b;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.73a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                    </svg>
                                @endif
                            </div>
                        @endif
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $language['name'] }}</span>
                    </div>

                    @if ($isCurrent)
                        <svg class="fi-check-icon dark:text-primary-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 1.15rem; height: 1.15rem; min-width: 1.15rem; max-width: 1.15rem; min-height: 1.15rem; max-height: 1.15rem; flex-shrink: 0; color: #0284c7; margin-inline-start: 0.75rem;" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </x-filament::dropdown>
</div>
