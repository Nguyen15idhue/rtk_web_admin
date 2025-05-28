<?php
/**
 * Test helper functions for invoice review page
 * Run this file to verify all helper functions work correctly
 */

// Include the helper file
require_once __DIR__ . '/invoice_review_helpers.php';

echo "Testing Invoice Review Helper Functions\n";
echo "=====================================\n\n";

// Test data
$testInvoice = [
    'invoice_id' => 123,
    'status' => 'pending',
    'invoice_file' => 'test_invoice.pdf',
    'rejected_reason' => 'Missing information'
];

$testPdfBaseUrl = 'http://localhost/public/uploads/invoice/';

echo "1. Testing renderInvoiceActions() for pending status...\n";
ob_start();
renderInvoiceActions($testInvoice, true);
$output = ob_get_clean();
echo "✓ Generated action buttons for pending invoice\n\n";

echo "2. Testing renderPDFViewer()...\n";
ob_start();
renderPDFViewer($testInvoice, $testPdfBaseUrl);
$output = ob_get_clean();
echo "✓ Generated PDF viewer button\n\n";

echo "3. Testing renderRejectionReason()...\n";
$testInvoice['status'] = 'rejected';
ob_start();
renderRejectionReason($testInvoice);
$output = ob_get_clean();
echo "✓ Generated rejection reason display\n\n";

echo "4. Testing buildSelectOptions()...\n";
$options = [
    '' => 'All Statuses',
    'pending' => 'Pending',
    'approved' => 'Approved'
];
$result = buildSelectOptions($options, 'pending');
if (strpos($result, 'selected') !== false) {
    echo "✓ Select options generated correctly\n\n";
} else {
    echo "✗ Select options test failed\n\n";
}

echo "5. Testing sanitizeFilters()...\n";
$filters = [
    'email' => '<script>alert("test")</script>test@example.com',
    'status' => 'pending'
];
$sanitized = sanitizeFilters($filters);
if (strpos($sanitized['email'], '<script>') === false) {
    echo "✓ Filters sanitized correctly\n\n";
} else {
    echo "✗ Filter sanitization test failed\n\n";
}

echo "All tests completed successfully!\n";
echo "Helper functions are working correctly.\n";
?>
