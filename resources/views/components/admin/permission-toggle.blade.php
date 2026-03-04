@props([
    'permission' => null, // Permission name string
    'label' => null, // Display label
    'checked' => false, // Current state (true/false)
    'disabled' => false, // Disabled state
    'name' => 'permissions[]', // Form input name
    'description' => null, // Optional description
])

@php
    $permissionId = 'permission-' . str()->slug($permission);
    $isChecked = is_bool($checked) ? $checked : old($name, false);
@endphp

<div class="permission-toggle flex items-start gap-3
            {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}
            p-3 rounded-lg
            {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50' }}
            transition-colors duration-200">
    {{-- Toggle Switch --}}
    <div class="relative flex-shrink-0 mt-0.5">
        <input
            type="checkbox"
            id="{{ $permissionId }}"
            name="{{ $name }}"
            value="{{ $permission }}"
            {{ $isChecked ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            class="peer sr-only"
        >

        {{-- Toggle Track --}}
        <label
            for="{{ $permissionId }}"
            class="block w-11 h-6
                   rounded-full
                   bg-gray-200
                   peer-checked:bg-primary
                   peer-focus:ring-4 peer-focus:ring-primary/20
                   transition-all duration-200 ease-in-out
                   cursor-pointer
                   relative
                   {{ $disabled ? 'cursor-not-allowed' : '' }}"
            @if($disabled) tabindex="-1" @endif
        >
            {{-- Toggle Knob --}}
            <span class="absolute
                          top-0.5
                          {{ $direction === 'rtl' ? 'right-0.5' : 'left-0.5' }}
                          w-5 h-5
                          bg-white
                          rounded-full
                          shadow-sm
                          transition-transform duration-200 ease-in-out
                          peer-checked:translate-x-5
                          {{ $direction === 'rtl' ? 'peer-checked:-translate-x-5' : '' }}">
            </span>
        </label>
    </div>

    {{-- Label & Description --}}
    <div class="flex-1 min-w-0 cursor-pointer {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
        <label
            for="{{ $permissionId }}"
            class="block text-sm font-medium text-gray-900
                   {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}"
            @if($disabled) tabindex="-1" @endif
        >
            {{ $label }}
        </label>

        @if($description)
            <p class="mt-0.5 text-xs text-gray-500">
                {{ $description }}
            </p>
        @endif
    </div>

    {{-- Hidden Helper for Accessibility --}}
    <span class="sr-only">
        @lang('admin.toggle_permission', ['permission' => $label])
    </span>
</div>

@pushOnce('scripts')
<script>
    // Keyboard navigation for toggles
    document.querySelectorAll('.permission-toggle input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.checked = !this.checked;
                // Trigger change event
                this.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
</script>
@endPushOnce
