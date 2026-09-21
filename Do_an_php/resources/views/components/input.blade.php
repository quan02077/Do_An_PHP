@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'icon' => null
])

@php
    $inputId = $id ?? $name;
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $inputId }}" class="form-label small fw-medium text-dark mb-1">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    @if($icon || isset($prefix))
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2">
                {{ $prefix ?? $icon }}
            </span>
            <input 
                type="{{ $type }}" 
                id="{{ $inputId }}" 
                name="{{ $name }}" 
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $attributes->merge(['class' => 'form-control border-start-0 ps-1 py-2 ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
            />
        </div>
    @else
        <input 
            type="{{ $type }}" 
            id="{{ $inputId }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'form-control py-2 ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
        />
    @endif

    @error($name)
        <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
    @enderror
</div>
