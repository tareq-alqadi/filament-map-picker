@php
    $config = $getMapConfig();
    $state = $getState();
    $recordKey = $getRecordKey();
@endphp

<div class="p-2 w-full"
    x-data="mapPicker({
            id: @js($recordKey),
            state: @js($state),
            config: @js($config),
            toolbarButtons: @js($getToolbarButtons()),
            hasMarkerCircle: @js($hasMarkerCircle()),
        })"
    x-ignore
    wire:ignore
    x-load="visible"
    x-load-css="[
            @js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-map-picker-css', \TareqAlqadi\FilamentMapPicker\FilamentMapPicker::getPackageName())),
        ]"
    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('map-picker-component', package: \TareqAlqadi\FilamentMapPicker\FilamentMapPicker::getPackageName()) }}">

    <div x-ref="map-{{ $recordKey }}"
        class="w-full border-2 border-gray-950/10 dark:border-gray-800 rounded-2xl"
        style="height: {{ $config['height'] }}px; z-index: 1 !important;">
    </div>

</div>