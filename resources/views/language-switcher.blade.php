<div @if($floating ?? false) style="position: fixed; top: 0.75rem; inset-inline-end: 1rem; z-index: 50;" @endif class="fi-dropdown-language-switcher flex items-center">
    <x-filament::dropdown placement="bottom-end" maxHeight="36rem" teleport>
        <x-slot name="trigger" style="display: flex; align-items: center; justify-content: center; padding: 0.25rem;">
            @if (isset($currentLanguage) && $showFlags)
                <button
                    type="button"
                    style="width: 2.15rem; height: 2.15rem; border-radius: 9999px; overflow: hidden; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 1px solid rgba(0, 0, 0, 0.1); background-color: #ffffff; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); transition: all 0.15s ease;"
                    class="hover:scale-105 hover:ring-2 hover:ring-primary-500/40 focus:outline-none dark:border-white/20 dark:bg-gray-800"
                    title="{{ $currentLanguage['name'] ?? 'Language' }}"
                >
                    @php
                        try {
                            echo svg('flag-1x1-'.$currentLanguage['flag'], '')->toHtml();
                            $flagFound = true;
                        } catch (Exception) {
                            $flagFound = false;
                        }
                    @endphp
                    @unless ($flagFound)
                        <x-filament::icon icon="heroicon-o-language" style="width: 1.25rem; height: 1.25rem; color: #475569;" class="dark:text-gray-300" />
                    @endunless
                </button>
            @else
                <x-filament::icon-button icon="heroicon-o-language" label="Language switcher" />
            @endif
        </x-slot>

        <div style="padding: 0.35rem; min-width: 11.5rem; display: flex; flex-direction: column; gap: 0.25rem;">
            @foreach ($otherLanguages as $language)
                @php
                    $isCurrent = isset($currentLanguage) && ($currentLanguage['code'] === $language['code']);
                @endphp
                <a
                    href="{{ route('filament-language-switcher.switch', ['code' => $language['code']]) }}"
                    style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; text-decoration: none; transition: all 0.15s ease; {{ $isCurrent ? 'background-color: rgba(14, 165, 233, 0.1); color: #0284c7; font-weight: 600;' : 'color: #1e293b; font-weight: 500;' }}"
                    class="{{ $isCurrent ? 'dark:bg-primary-500/20 dark:text-primary-300' : 'hover:bg-slate-100 hover:text-slate-900 dark:text-gray-200 dark:hover:bg-white/10 dark:hover:text-white' }}"
                >
                    <div style="display: flex; align-items: center; min-width: 0;">
                        @if ($showFlags)
                            <div style="width: 1.35rem; height: 1.35rem; min-width: 1.35rem; border-radius: 9999px; overflow: hidden; margin-inline-end: 0.75rem; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);" class="dark:ring-white/20">
                                @php
                                    try {
                                        echo svg('flag-1x1-'.$language['flag'], '')->toHtml();
                                        $itemFlagFound = true;
                                    } catch (Exception) {
                                        $itemFlagFound = false;
                                    }
                                @endphp
                                @unless ($itemFlagFound)
                                    <x-filament::icon icon="heroicon-o-flag" style="width: 1rem; height: 1rem;" />
                                @endunless
                            </div>
                        @endif
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $language['name'] }}</span>
                    </div>

                    @if ($isCurrent)
                        <x-filament::icon
                            icon="heroicon-m-check"
                            style="width: 1.1rem; height: 1.1rem; color: #0284c7; flex-shrink: 0; margin-inline-start: 0.75rem;"
                            class="dark:text-primary-300"
                        />
                    @endif
                </a>
            @endforeach
        </div>
    </x-filament::dropdown>
</div>
