/**
 * Invoice Review Page JavaScript
 * Handles PDF modal functionality and invoice review actions
 */

// Global configuration
window.InvoiceReviewModal = {
    isOpen: false,
    currentInvoiceId: null,
    
    /**
     * Initialize modal event listeners
     */
    init() {
        this.bindEvents();
    },

    /**
     * Open PDF modal with enhanced features
     * @param {string} pdfUrl - URL of the PDF file
     * @param {string} invoiceId - Invoice ID for display
     */
    openPDF(pdfUrl, invoiceId) {
        const modal = document.getElementById('genericModal');
        const modalTitle = document.getElementById('genericModalTitle');
        const modalBody = document.getElementById('genericModalBody');
        const modalFooter = document.getElementById('genericModalFooter');
        
        if (!modal || !modalTitle || !modalBody || !modalFooter) {
            console.error('Modal elements not found');
            return;
        }

        this.currentInvoiceId = invoiceId;
        this.isOpen = true;

        // Set modal title
        modalTitle.innerHTML = `<i class="fas fa-file-pdf" style="color: #dc3545; margin-right: 8px;"></i>Xem PDF Hóa đơn #${invoiceId}`;
        
        // Show loading state
        this.showLoading(modalBody);
        
        // Hide footer for PDF viewing
        modalFooter.style.display = 'none';
        
        // Show modal with animation
        this.showModal(modal);
        
        // Load PDF after animation
        setTimeout(() => {
            this.loadPDF(modalBody, pdfUrl);
        }, 300);
    },

    /**
     * Show loading state in modal
     * @param {HTMLElement} modalBody 
     */
    showLoading(modalBody) {
        modalBody.innerHTML = `
            <div class="pdf-loading">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #007bff;"></i>
                <span style="margin-top: 10px; font-weight: 500;">Đang tải PDF...</span>
            </div>
        `;
    },

    /**
     * Load PDF into iframe
     * @param {HTMLElement} modalBody 
     * @param {string} pdfUrl 
     */
    loadPDF(modalBody, pdfUrl) {
        modalBody.innerHTML = `
            <iframe src="${pdfUrl}" 
                    width="100%" 
                    height="100%" 
                    style="border:none; display:block; flex-grow:1; border-radius: 0 0 12px 12px; opacity: 0; transition: opacity 0.3s ease;"
                    onload="this.style.opacity='1'"
                    onerror="InvoiceReviewModal.showPDFError('${pdfUrl}')">
            </iframe>
        `;
    },

    /**
     * Show PDF error state
     * @param {string} pdfUrl 
     */
    showPDFError(pdfUrl) {
        const modalBody = document.getElementById('genericModalBody');
        if (modalBody) {
            modalBody.innerHTML = `
                <div class="pdf-loading error">
                    <i class="fas fa-exclamation-triangle" style="color: #dc3545; font-size: 24px;"></i>
                    <span>Không thể hiển thị PDF. <a href="${pdfUrl}" target="_blank" style="color: #007bff; text-decoration: underline;">Tải xuống tệp</a></span>
                </div>
            `;
        }
    },

    /**
     * Show modal with animation
     * @param {HTMLElement} modal 
     */
    showModal(modal) {
        modal.classList.remove('hide');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        document.body.classList.add('modal-open');
        
        // Trigger animation
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    },

    /**
     * Close PDF modal
     */
    close() {
        const modal = document.getElementById('genericModal');
        const modalFooter = document.getElementById('genericModalFooter');
        
        if (!modal) return;

        // Reset state
        this.isOpen = false;
        this.currentInvoiceId = null;

        // Hide modal
        modal.classList.remove('show');
        modal.classList.add('hide');
        
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.body.classList.remove('modal-open');
            
            // Restore footer display
            if (modalFooter) {
                modalFooter.style.display = 'flex';
            }
        }, 300);
    },

    /**
     * Toggle fullscreen mode
     */
    toggleFullscreen() {
        const modal = document.getElementById('genericModal');
        const modalContent = modal?.querySelector('.modal-content');
        
        if (!modalContent) return;

        modalContent.classList.toggle('fullscreen');
    },

    /**
     * Handle keyboard shortcuts
     * @param {KeyboardEvent} event 
     */
    handleKeydown(event) {
        if (!this.isOpen) return;

        switch(event.key) {
            case 'Escape':
                this.close();
                break;
            case 'F11':
                event.preventDefault();
                this.toggleFullscreen();
                break;
        }
    },

    /**
     * Handle modal click events
     * @param {MouseEvent} event 
     */
    handleClick(event) {
        const modal = document.getElementById('genericModal');
        
        if (!this.isOpen || !modal) return;

        // Close when clicking outside modal content
        if (event.target === modal) {
            this.close();
        }
        
        // Close when clicking close button
        if (event.target.classList.contains('modal-close')) {
            this.close();
        }
    },

    /**
     * Handle window resize
     */
    handleResize() {
        if (!this.isOpen) return;

        const modal = document.getElementById('genericModal');
        if (modal && modal.style.display === 'block') {
            // Force reflow to apply new responsive styles
            modal.style.display = 'none';
            setTimeout(() => {
                modal.style.display = 'block';
            }, 10);
        }
    },

    /**
     * Bind all event listeners
     */
    bindEvents() {
        // Keyboard events
        document.addEventListener('keydown', (e) => this.handleKeydown(e));
        
        // Click events
        document.addEventListener('click', (e) => this.handleClick(e));
        
        // Window resize
        window.addEventListener('resize', () => this.handleResize());

        // Override helpers.closeModal if available
        if (typeof helpers !== 'undefined' && helpers.closeModal) {
            const originalCloseModal = helpers.closeModal;
            helpers.closeModal = (modalId) => {
                if (modalId === 'genericModal') {
                    this.close();
                } else {
                    originalCloseModal(modalId);
                }
            };
        }
    }
};

// Global functions for backward compatibility
function openPDFModal(pdfUrl, invoiceId) {
    InvoiceReviewModal.openPDF(pdfUrl, invoiceId);
}

function closePDFModal() {
    InvoiceReviewModal.close();
}

function toggleFullscreen() {
    InvoiceReviewModal.toggleFullscreen();
}

function showPDFError(pdfUrl) {
    InvoiceReviewModal.showPDFError(pdfUrl);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    InvoiceReviewModal.init();
});

// Also initialize immediately if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        InvoiceReviewModal.init();
    });
} else {
    InvoiceReviewModal.init();
}
