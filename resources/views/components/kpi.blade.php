@props(['label', 'value', 'hint' => null])
<div class="kpi">
    <p class="kpi-label">{{ $label }}</p>
    <p class="kpi-value">{{ $value }}</p>
    @if ($hint)
        <p class="kpi-hint">{{ $hint }}</p>
    @endif
</div>
