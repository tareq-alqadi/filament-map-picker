@php
    $id = $getId();
    $state = $getState();
    $config = $getMapConfig();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    @if (filled($state))
        <div x-data="mapPicker({
            id: @js($id),
            state: @js($state),
            config: @js($config),
            toolbarButtons: @js($getToolbarButtons()),
            hasMarkerCircle: @js($hasMarkerCircle()),
        })" x-ignore ax-load wire:ignore
            x-load-css="[
                @js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-map-picker-css', \TareqAlqadi\FilamentMapPicker\FilamentMapPicker::getPackageName())),
            ]"
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('map-picker-component', package: \TareqAlqadi\FilamentMapPicker\FilamentMapPicker::getPackageName()) }}">

            <div x-ref="map-{{ $id }}" class="w-full border-2 dark:border-gray-800 rounded-lg"
                style="height: {{ $config['height'] }}px; z-index: 1 !important;"></div>

        </div>
    @elseif (($placeholder = $getPlaceholder()) !== null)
        <x-filament-infolists::entries.placeholder>
            {{ $placeholder }}
        </x-filament-infolists::entries.placeholder>
    @endif

</x-dynamic-component>
