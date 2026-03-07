/**
 * Dashboard JavaScript Module
 */
window.Dashboard = {
    Sidebar: {
        elements: {
            sidebar: null,
            backdrop: null,
            closeButton: null,
            mobileMenuButton: null,
            html: null,
        },

        init() {
            this.elements.sidebar = document.getElementById("sidebar");
            this.elements.backdrop = document.getElementById("sidebar-backdrop");
            this.elements.closeButton = document.getElementById("close-sidebar");
            this.elements.mobileMenuButton = document.getElementById("mobile-menu-button");
            this.elements.html = document.documentElement;

            if (!this.elements.sidebar || !this.elements.backdrop) {
                return;
            }

            this.bindEvents();
            this.closeOnNavigation();
        },

        getClosedClass() {
            const isRTL = this.elements.html.getAttribute("dir") === "rtl";
            return isRTL ? "translate-x-full" : "-translate-x-full";
        },

        isClosed() {
            const closedClass = this.getClosedClass();
            return this.elements.sidebar.classList.contains(closedClass);
        },

        updateAriaExpanded(isOpen) {
            this.elements.mobileMenuButton?.setAttribute("aria-expanded", isOpen.toString());
        },

        open() {
            const closedClass = this.getClosedClass();
            this.elements.sidebar.classList.remove(closedClass);
            this.elements.backdrop.classList.remove("hidden");
            void this.elements.backdrop.offsetWidth;
            this.elements.backdrop.classList.remove("opacity-0");
            this.updateAriaExpanded(true);
            this.lockBodyScroll();
            window.dispatchEvent(new Event("sidebar-opened"));
            this.elements.closeButton?.focus();
        },

        close() {
            const closedClass = this.getClosedClass();
            this.elements.backdrop.classList.add("opacity-0");
            this.elements.sidebar.classList.add(closedClass);
            this.updateAriaExpanded(false);
            this.elements.mobileMenuButton?.focus();
            this.unlockBodyScroll();
            setTimeout(() => {
                if (this.isClosed()) {
                    this.elements.backdrop.classList.add("hidden");
                }
            }, 300);
        },

        lockBodyScroll() {
            if (window.innerWidth >= 1024) return;
            this.elements.html.style.overflow = "hidden";
            document.body.style.overflow = "hidden";
        },

        unlockBodyScroll() {
            this.elements.html.style.overflow = "";
            document.body.style.overflow = "";
        },

        toggle() {
            if (this.isClosed()) {
                this.open();
            } else {
                this.close();
            }
        },

        bindEvents() {
            this.elements.closeButton?.addEventListener("click", () => this.toggle());
            this.elements.mobileMenuButton?.addEventListener("click", () => this.toggle());
            this.elements.backdrop?.addEventListener("click", () => this.toggle());
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape" && !this.isClosed()) {
                    this.close();
                }
            });
            let resizeTimer;
            window.addEventListener("resize", () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => this.handleResize(), 100);
            });
        },

        handleResize() {
            if (window.innerWidth >= 1024 && !this.isClosed()) {
                this.unlockBodyScroll();
            }
        },

        closeOnNavigation() {
            document.addEventListener("click", (e) => {
                const link = e.target.closest("a");
                if (link && !this.isClosed()) {
                    const href = link.getAttribute("href");
                    if (href &&
                        !href.startsWith("#") &&
                        !href.startsWith("http") &&
                        !href.startsWith("javascript:") &&
                        !href.startsWith("mailto:")) {
                        this.close();
                    }
                }
            });
        },
    },

    Search: {
        elements: {
            button: null,
            overlay: null,
            closeButton: null,
        },

        init() {
            this.elements.button = document.getElementById("mobile-search-button");
            this.elements.overlay = document.getElementById("mobile-search-overlay");
            this.elements.closeButton = document.getElementById("close-mobile-search");
            if (!this.elements.button || !this.elements.overlay) {
                return;
            }
            this.bindEvents();
        },

        open() {
            this.elements.overlay.classList.remove("hidden");
            this.elements.button.setAttribute("aria-expanded", "true");
            const searchInput = this.elements.overlay.querySelector('input[type="search"]');
            if (searchInput) {
                setTimeout(() => searchInput.focus(), 100);
            }
        },

        close() {
            this.elements.overlay.classList.add("hidden");
            this.elements.button?.setAttribute("aria-expanded", "false");
            this.elements.button?.focus();
        },

        bindEvents() {
            this.elements.button?.addEventListener("click", () => this.open());
            this.elements.closeButton?.addEventListener("click", () => this.close());
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape" && !this.elements.overlay.classList.contains("hidden")) {
                    this.close();
                }
            });
        },
    },

    DeleteActions: {
        init() {
            // Handle all delete buttons - support multiple patterns for backward compatibility
            // Pattern 1 (new standard): .delete-btn with data-delete-url and data-confirm-message
            // Pattern 2 (legacy): .delete-{entity}-btn with data-url and data-modal-confirm
            const deleteButtonSelectors = [
                '.delete-btn',
                '.delete-city-btn',
                '.delete-courier-btn',
                '.delete-fee-btn',
                '.delete-transaction-btn',
                '.delete-permission-btn',
                '.delete-role-btn',
                '.delete-user-btn',
                '.delete-category-btn',
                '[data-delete-url]',
                '[data-url]'
            ].join(', ');

            document.addEventListener('click', (e) => {
                const deleteBtn = e.target.closest(deleteButtonSelectors);
                if (deleteBtn && (deleteBtn.hasAttribute('data-delete-url') || deleteBtn.hasAttribute('data-url'))) {
                    console.log('Delete button clicked', deleteBtn);
                    e.preventDefault();
                    e.stopPropagation();

                    // Support both attribute naming conventions
                    let url = deleteBtn.getAttribute('data-delete-url') || deleteBtn.getAttribute('data-url');
                    let message = deleteBtn.getAttribute('data-confirm-message') || deleteBtn.getAttribute('data-modal-confirm');

                    console.log('Delete button data:', { url, message, hasFunction: typeof window.showDeleteModal });

                    if (url && message) {
                        if (typeof window.showDeleteModal === 'function') {
                            window.showDeleteModal(message, url);
                        } else {
                            console.error('showDeleteModal function not found!');
                            // Fallback to native confirm
                            if (confirm(message)) {
                                this.submitDeleteForm(url);
                            }
                        }
                    } else {
                        console.error('Missing url or message:', { url, message });
                    }
                }
            });
        },

        submitDeleteForm(url) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        },
    },

    init() {
        this.Sidebar.init();
        this.Search.init();
        this.DeleteActions.init();
    }
};

document.addEventListener("DOMContentLoaded", () => {
    window.Dashboard.init();
});
