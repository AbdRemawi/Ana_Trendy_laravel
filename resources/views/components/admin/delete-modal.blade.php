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

{{-- Delete Confirmation Modal --}}
<div id="{{ $id }}" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    {{-- Backdrop --}}
    <div id="{{ $id }}-backdrop" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0"
         aria-hidden="true"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div id="{{ $id }}-panel" class="relative bg-white rounded-2xl shadow-2xl p-8 max-w-lg w-full transform transition-all scale-95 opacity-0">
                {{-- Warning Icon --}}
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                {{-- Title --}}
                <h3 id="{{ $id }}-title" class="text-xl font-semibold text-gray-900 text-center mb-2">
                    {{ __('admin.confirm_delete') }}
                </h3>

                {{-- Message --}}
                <p id="{{ $id }}-message" class="text-gray-600 text-center mb-8">
                    {{ $message ?? __('admin.confirm_delete_message') }}
                </p>

                {{-- Actions --}}
                <div class="flex justify-center gap-4">
                    <button type="button" id="{{ $id }}-cancel" class="px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium">
                        {{ $cancelText }}
                    </button>
                    <button type="button" id="{{ $id }}-confirm" class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium flex items-center gap-2">
                        <span>{{ $confirmText }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    (function() {
        const modalId = '{{ $id }}';
        let currentDeleteUrl = '';

        // Show modal function - defined globally for immediate access
        window.showDeleteModal = function(message, url) {
            console.log('showDeleteModal called with:', { message, url });
            const modal = document.getElementById(modalId);
            const backdrop = document.getElementById(modalId + '-backdrop');
            const panel = document.getElementById(modalId + '-panel');
            const messageEl = document.getElementById(modalId + '-message');

            if (messageEl && message) {
                messageEl.textContent = message;
            }
            currentDeleteUrl = url;

            console.log('currentDeleteUrl set to:', currentDeleteUrl);

            if (modal) {
                modal.classList.remove('hidden');
                // Trigger reflow
                void modal.offsetWidth;

                // Animate in
                if (backdrop) backdrop.classList.remove('opacity-0');
                if (panel) panel.classList.remove('scale-95', 'opacity-0');
            }
        };

        // Hide modal function - defined globally for immediate access
        window.hideDeleteModal = function() {
            const modal = document.getElementById(modalId);
            const backdrop = document.getElementById(modalId + '-backdrop');
            const panel = document.getElementById(modalId + '-panel');
            const confirmBtn = document.getElementById(modalId + '-confirm');

            if (backdrop) backdrop.classList.add('opacity-0');
            if (panel) panel.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                if (modal) modal.classList.add('hidden');
                // Reset button state
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<span>{{ $confirmText }}</span>';
                }
                currentDeleteUrl = '';
            }, 300);
        };

        // Submit delete request - defined globally for onclick handler
        window.submitDeleteModal = function() {
            console.log('submitDeleteModal called, currentDeleteUrl:', currentDeleteUrl);

            if (!currentDeleteUrl) {
                console.error('ERROR: No delete URL set!');
                return;
            }

            const confirmBtn = document.getElementById(modalId + '-confirm');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            console.log('Creating form to delete:', currentDeleteUrl);

            // Show loading state
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;
            }

            // Create a real form for proper Laravel method spoofing
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = currentDeleteUrl;
            form.style.display = 'none';

            // Add CSRF token
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);

            // Add DELETE method override
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            console.log('Form HTML:', form.outerHTML);
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);

            // Append to body and submit
            document.body.appendChild(form);

            console.log('About to submit form...');

            // Small timeout to ensure console logs are visible
            setTimeout(function() {
                console.log('Form.submit() called');
                form.submit();
            }, 100);
        };

        // Initialize modal event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const cancelBtn = document.getElementById(modalId + '-cancel');
            const confirmBtn = document.getElementById(modalId + '-confirm');
            const modal = document.getElementById(modalId);
            const backdrop = document.getElementById(modalId + '-backdrop');

            console.log('Delete modal initialization - confirmBtn found:', !!confirmBtn, 'cancelBtn found:', !!cancelBtn);

            // Event listeners
            if (cancelBtn) {
                cancelBtn.addEventListener('click', window.hideDeleteModal);
                console.log('Cancel button listener attached');
            }
            if (confirmBtn) {
                // Add both event listener and onclick for redundancy
                confirmBtn.addEventListener('click', function(e) {
                    console.log('Confirm button clicked via event listener');
                    e.preventDefault();
                    e.stopPropagation();
                    window.submitDeleteModal();
                });
                console.log('Confirm button listener attached');
            }

            // Close on backdrop click
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === backdrop) {
                        window.hideDeleteModal();
                    }
                });
            }

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    window.hideDeleteModal();
                }
            });

            console.log('Delete modal initialized');
        });
    })();
</script>
@endpush
@endonce