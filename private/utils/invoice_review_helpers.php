<?php
/**
 * Invoice Review Page Helper Functions
 * Contains utility functions for rendering invoice review interface elements
 */

/**
 * Render action buttons for invoice based on status and permissions
 * @param array $invoice Invoice data
 * @param bool $isEditAllowed Whether user has edit permission
 */
function renderInvoiceActions($invoice, $isEditAllowed) {
    $status = $invoice['status'];
    $invoiceId = $invoice['invoice_id'];
    
    if ($status === 'pending' && $isEditAllowed): ?>
        <div class="action-buttons">
            <a href="invoice_upload.php?invoice_id=<?php echo $invoiceId; ?>" 
               class="btn-icon btn-approve" title="Upload & Approve">
                <i class="fas fa-upload"></i>
            </a>
            <button class="btn-icon btn-reject" 
                    onclick="InvoiceReviewPageEvents.rejectInvoice(<?php echo $invoiceId; ?>)" 
                    title="Từ chối">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php elseif (($status === 'approved' || $status === 'rejected') && $isEditAllowed): ?>
        <button class="btn-icon btn-undo" 
                onclick="InvoiceReviewPageEvents.undoInvoice(<?php echo $invoiceId; ?>)" 
                title="Hoàn tác">
            <i class="fas fa-undo"></i>
        </button>
    <?php else: ?>
        <span class="no-actions">-</span>
    <?php endif;
}

/**
 * Render PDF viewer button
 * @param array $invoice Invoice data
 * @param string $pdfBaseUrl Base URL for PDF files
 */
function renderPDFViewer($invoice, $pdfBaseUrl) {
    if ($invoice['invoice_file']): ?>
        <button class="btn-link" 
                onclick="openPDFModal('<?php echo $pdfBaseUrl . $invoice['invoice_file']; ?>', '<?php echo $invoice['invoice_id']; ?>')">
            <i class="fas fa-eye"></i> Xem PDF
        </button>
    <?php else: ?>
        <span class="no-file">-</span>
    <?php endif;
}

/**
 * Render rejection reason with appropriate styling
 * @param array $invoice Invoice data
 */
function renderRejectionReason($invoice) {
    if ($invoice['rejected_reason'] && $invoice['status'] === 'rejected'): ?>
        <span class="rejection-reason rejected"><?php echo htmlspecialchars($invoice['rejected_reason']); ?></span>
    <?php else: ?>
        <span class="rejection-reason"><?php echo htmlspecialchars($invoice['rejected_reason'] ?? ''); ?></span>
    <?php endif;
}

/**
 * Build filter select options
 * @param array $options Available options
 * @param string $selectedValue Currently selected value
 * @return string HTML options
 */
function buildSelectOptions($options, $selectedValue) {
    $html = '';
    foreach ($options as $value => $label) {
        $selected = ($selectedValue === $value) ? 'selected' : '';
        $html .= sprintf(
            '<option value="%s" %s>%s</option>',
            htmlspecialchars($value),
            $selected,
            htmlspecialchars($label)
        );
    }
    return $html;
}
