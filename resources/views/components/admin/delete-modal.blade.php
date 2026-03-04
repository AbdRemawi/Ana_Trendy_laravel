@props([
    'id' => 'delete-modal',
    'title' => null,
    'message' => null,
    'confirmText' => 'Delete',
    'cancelText' => 'Cancel',
    'type' => 'danger',
])

@php
    $typeClasses = [
        'danger' => [
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
    ];

    $classes = $typeClasses[$type] ?? $typeClasses['danger'];
@endphp

{{-- Premium Delete Confirmation Modal --}}
<div id="{{ $id }}" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" aria-describedby="{{ $id }}-description">
    {{-- Animated Backdrop with blur effect --}}
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm modal-backdrop transition-opacity duration-300 ease-out opacity-0"
         aria-hidden="true"
         data-modal-backdrop></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl p-8 max-w-lg w-full transform transition-all">
                <h2 id="{{ $id }}-title" class="text-xl font-bold text-gray-900 mb-4"></h2>
                <p id="{{ $id }}-description" class="text-gray-600 mb-6">{{ $message }}</p>
                <div class="flex justify-end gap-3">
                    <button type="button" data-modal-cancel class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                        {{ $cancelText }}
                    </button>
                    <button type="button" data-modal-confirm class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        {{ $confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </div>

@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set up cancel button
        document.querySelectorAll('#delete-modal [data-modal-cancel], #delete-modal [data-modal-cancel]').forEach(function(button) {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal-container') || document.getElementById('delete-modal');
                if (modal) {
                    window.closeDeleteModal('delete-modal');
                }
            });
        });

        // Set up confirm button
        document.querySelectorAll('[data-modal-confirm]').forEach(function(button) {
            button.addEventListener('click', function() {
                this.disabled = true;
                this.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            });
        });
    });
</script>
@endpush
@endonce