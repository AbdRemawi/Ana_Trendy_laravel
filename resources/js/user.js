/**
 * User Management Module
 *
 * Handles:
 * - Commission rate field toggle for affiliate role
 * - Delete user confirmation modal integration
 * - Form submissions for user management
 */

const UserManagement = {
    config: {
        affiliateRole: 'affiliate',
        commissionFieldId: 'commission-rate-field',
        roleSelectId: 'role',
        deleteModalId: 'delete-user-modal',
        deleteButtonClass: '.delete-user-btn',
    },

    init() {
        this.initCommissionFieldToggle();
        this.initDeleteButtons();
    },

    /**
     * Toggle commission rate field visibility based on selected role
     * Shows field only when 'affiliate' role is selected
     */
    initCommissionFieldToggle() {
        const roleSelect = document.getElementById(this.config.roleSelectId);
        const commissionField = document.getElementById(this.config.commissionFieldId);

        if (roleSelect && commissionField) {
            const toggleCommissionField = () => {
                const isAffiliate = roleSelect.value === this.config.affiliateRole;
                const isDisabled = roleSelect.disabled;

                if (isAffiliate && !isDisabled) {
                    commissionField.classList.remove('hidden');
                    commissionField.setAttribute('aria-hidden', 'false');
                } else {
                    commissionField.classList.add('hidden');
                    commissionField.setAttribute('aria-hidden', 'true');
                }
            };

            // Listen for role changes
            roleSelect.addEventListener('change', toggleCommissionField);

            // Initial state check
            toggleCommissionField();
        }
    },

    /**
     * Initialize delete button handlers with modal integration
     *
     * IMPORTANT: This only initializes if the delete-modal component is present on the page
     * to prevent double-handling with the modal's built-in event listeners.
     */
    initDeleteButtons() {
        // Check if the delete modal exists on this page
        const deleteModal = document.getElementById(this.config.deleteModalId);
        if (!deleteModal) {
            return; // No modal on this page, don't attach handlers
        }

        // Check if modal is already initialized (prevents double attachment)
        if (deleteModal.hasAttribute('data-user-js-initialized')) {
            return;
        }

        // Mark as initialized
        deleteModal.setAttribute('data-user-js-initialized', 'true');

        const deleteButtons = document.querySelectorAll(this.config.deleteButtonClass);

        deleteButtons.forEach(button => {
            // Remove any existing click handlers to prevent duplicates
            button.removeEventListener('click', this.handleDeleteClick);

            // Add our handler
            button.addEventListener('click', (e) => this.handleDeleteClick(e, button));
        });
    },

    /**
     * Handle delete button click
     */
    handleDeleteClick(e, button) {
        e.preventDefault();
        e.stopPropagation();

        const url = button.getAttribute('data-url');
        const userName = button.getAttribute('data-name') || '';
        const confirmMessage = button.getAttribute('data-confirm') || '';

        // Use the delete modal component
        if (typeof window.openDeleteModal === 'function') {
            window.openDeleteModal(this.config.deleteModalId, {
                title: __('admin.delete_user_button'),
                message: `${confirmMessage} ${userName}?`,
                onConfirm: () => this.submitDeleteForm(url)
            });
        } else {
            // Fallback to browser confirm if modal not available
            this.fallbackDelete(url, userName, confirmMessage);
        }
    },

    /**
     * Submit delete form via POST
     */
    submitDeleteForm(url) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);
        }

        // Add DELETE method
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    },

    /**
     * Fallback delete method using browser confirm
     * Only used if delete modal component is not available
     */
    fallbackDelete(url, userName, confirmMessage) {
        const safeUserName = this.sanitizeHtml(userName);
        const safeConfirmMessage = this.sanitizeHtml(confirmMessage);

        if (confirm(`${safeConfirmMessage} ${safeUserName}?`)) {
            this.submitDeleteForm(url);
        }
    },

    /**
     * Sanitize HTML to prevent XSS
     */
    sanitizeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
};

// Initialize on DOM content loaded
document.addEventListener('DOMContentLoaded', () => {
    UserManagement.init();
});
