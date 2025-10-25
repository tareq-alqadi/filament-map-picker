@php
    $id = $getId();
    $isConcealed = $isConcealed();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $config = $getMapConfig();
@endphp
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :valid="!$errors->has($statePath)"
        class="fi-fo-map"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
            ->class(['overflow-hidden']
            )">
        <div
            x-data="mapPicker({
                    id: @js($id),
                    state: $wire.{{ $applyStateBindingModifiers("entangle('{$getStatePath()}')") }},
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

            <div x-ref="map-{{ $id }}" class="w-full border-2 border-gray-950/20 dark:border-gray-800 rounded-2xl"
                style="height: {{ $config['height'] }}px; z-index: 1 !important;"></div>

        </div>


    </x-filament::input.wrapper>
</x-dynamic-component>
