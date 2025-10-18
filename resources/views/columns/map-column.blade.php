@php
    $config = $getMapConfig();
    $state = $getState();
@endphp

@if (filled($state))
    <div class="p-2 w-full" x-data="mapPicker({
        id: @js($recordKey),
        state: @js($state),
        config: @js($config),
        toolbarButtons: @js($getToolbarButtons()),
        hasMarkerCircle: @js($hasMarkerCircle()),
    })" x-ignore ax-load wire:ignore
        x-load-css="[
        @js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-map-picker-css', \TareqAlqadi\FilamentMapPicker\FilamentMapPicker::getPackageName())),
        ]"
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('map-picker-component', package: \TareqAlqadi\FilamentMapPicker\FilamentMapPicker::getPackageName()) }}">

        <div x-ref="map-{{ $recordKey }}" class="w-full border-2 dark:border-gray-800 rounded-lg"
            style="height: {{ $config['height'] }}px; z-index: 1 !important;"></div>

    </div>
@elseif (($placeholder = $getPlaceholder()) !== null)
    <div class="fi-ta-placeholder text-sm leading-6 text-gray-400 dark:text-gray-500">
        {{ $placeholder }}
    </div>
@endif
