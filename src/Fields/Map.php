<?php

declare(strict_types=1);

namespace TareqAlqadi\FilamentMapPicker\Fields;

use Closure;
use Filament\Forms\Components\Concerns\InteractsWithToolbarButtons;
use Filament\Forms\Components\Field;
use Filament\Forms\Concerns\HasStateBindingModifiers;
use TareqAlqadi\FilamentMapPicker\Contracts\MapOptions;

class Map extends Field
{
    use HasStateBindingModifiers;
    use InteractsWithToolbarButtons;

    /**
     * Field view
     */
    public string $view = 'map-picker::fields.map-picker';

    protected bool|Closure $hasMarkerCircle = false;

    protected float|Closure $markerCircleRadius = 100;

    /**
     * Main field config variables
     */
    private array $mapConfig = [
        'showMarker' => true,
        'tilesUrl' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        'attribution' => '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        'zoomOffset' => 0,
        'detectRetina' => false,
        'minZoom' => 2,
        'maxZoom' => 19,
        'zoom' => 15,
        'markerColor' => '#3b82f6',
        'markerCircleColor' => '#43ff64',
        'interactiveMarker' => true,
        'followMarker' => false,
        'draggableMarker' => true,
        'height' => 500,

    ];

    /**
     * Leaflet controls variables
     */
    private array $controls = [
        'scrollWheelZoom' => 'center',
        'doubleClickZoom' => false,
        'touchZoom' => 'center',
        'minZoom' => 2,
        'maxZoom' => 19,
        'zoom' => 15,
    ];

    /**
     * Extra leaflet controls variables
     */
    private array $extraControls = [];

    /**
     * Create json configuration array
     */
    public function getMapConfig(): array
    {
        return array_merge($this->mapConfig, [
            'controls' => array_merge($this->controls, $this->extraControls),
        ]);
    }

    /**
     * Get default toolbar buttons
     *
     * @return array
     */
    public function getDefaultToolbarButtons(): array
    {
        return [
            'zoomControl',
            'fullScreen',
            'search',
        ];
    }

    /**
     * Set default zoom
     *
     * @return MapOptions
     *
     * @note Default value 19
     */
    public function zoom(int $zoom): self
    {
        $this->controls['zoom'] = $zoom;

        return $this;
    }

    /**
     * Set default height
     *
     * @return MapOptions
     *
     * @note Default value 500
     */
    public function height(int $height = 500): self
    {
        $this->mapConfig['height'] = $height;

        return $this;
    }

    /**
     * Set max zoom
     *
     * @return $this
     *
     * @note Default value 20
     */
    public function maxZoom(int $maxZoom): self
    {
        $this->controls['maxZoom'] = $maxZoom;

        return $this;
    }

    /**
     * Set min zoom
     *
     * @param  int  $maxZoom
     * @return $this
     *
     * @note Default value 1
     */
    public function minZoom(int $minZoom): self
    {
        $this->controls['minZoom'] = $minZoom;

        return $this;
    }

    /**
     * Determine if marker is visible or not.
     *
     * @return $this
     *
     * @note Default value is false
     */
    public function showMarker(bool $show = true): self
    {
        $this->mapConfig['showMarker'] = $show;

        return $this;
    }

    /**
     * Set tiles url
     *
     * @return $this
     */
    public function tilesUrl(string $url): self
    {
        $this->mapConfig['tilesUrl'] = $url;

        return $this;
    }

    /**
     * Change the marker color.
     *
     * @return $this
     */
    public function markerColor(string $color): self
    {
        $this->mapConfig['markerColor'] = $color;

        return $this;
    }

    public function markerCircleColor(string $circleColor): self
    {
        $this->mapConfig['markerCircleColor'] = $circleColor;

        return $this;
    }

    /**
     * Enable or disable following marker view on the map.
     *
     * @return $this
     */
    public function followMarker(bool $followMarker = true): self
    {
        $this->mapConfig['followMarker'] = $followMarker;

        return $this;
    }

    /**
     * Enable or disable dragging marker on the map.
     *
     * @return $this
     */
    public function draggableMarker(bool $draggableMarker = true): self
    {
        $this->mapConfig['draggableMarker'] = $draggableMarker;

        return $this;
    }

    public function markerCircle(float|Closure $markerCircleRadius = 100): static
    {
        $this->hasMarkerCircle = true;

        $this->markerCircleRadius = $markerCircleRadius;

        return $this;
    }

    /**
     * Append extra controls to be passed to leaflet map object
     *
     * @return $this
     */
    public function extraControl(array $control): self
    {
        $this->extraControls = array_merge($this->extraControls, $control);

        return $this;
    }

    /**
     * Append extra controls to be passed to leaflet tileLayer object
     *
     * @return $this
     */
    public function extraTileControl(array $control): self
    {
        $this->mapConfig = array_merge($this->mapConfig, $control);

        return $this;
    }

    public function hasMarkerCircle(): bool
    {
        return (bool) $this->evaluate($this->hasMarkerCircle);
    }

    public function getMarkerCircleRadius(): float
    {
        return $this->evaluate($this->markerCircleRadius) ?? 100;
    }

    /**
     * Setup function
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->default(['lat' => 0, 'lng' => 0, 'radius' => $this->getMarkerCircleRadius()]);
    }
}
