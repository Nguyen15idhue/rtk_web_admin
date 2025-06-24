<?php
// filepath: private\layouts\pagination.php
// Basic Pagination Logic (Create this file if it doesn't exist)
$range = 1; // Number of links to show around the current page

if (!isset($total_pages) || $total_pages <= 1) {
    return; // Don't display pagination if there's only one page or less
}

if (!isset($current_page)) {
    $current_page = 1;
}

if (!isset($items_per_page)) {
    $items_per_page = DEFAULT_ITEMS_PER_PAGE; // Default items per page
}

if (!isset($pagination_base_url)) {
    $pagination_base_url = '?'; // Default base URL
}

// Add support for custom pagination parameter name
if (!isset($pagination_param)) {
    $pagination_param = 'page';
}

// Function to generate pagination link, preserving existing query parameters
if (!function_exists('generate_pagination_link')) {
    function generate_pagination_link($page, $base_url) {
        global $pagination_param;
        $parsed = parse_url($base_url);
        // Use only the path (no query string)
        $path = $parsed['path'] ?? $base_url;
        // Parse existing query params
        $query_params = [];
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $query_params);
        }
        // Set/update pagination parameter
        $query_params[$pagination_param] = $page;
        // Rebuild query string
        $query_string = http_build_query($query_params);
        return $path . ($query_string ? '?' . $query_string : '');
    }
}

?>
<div class="pagination-footer d-flex justify-content-between align-items-center mt-4">
    <div class="pagination-info text-muted">
        <?php if (isset($total_items) && $total_items > 0):
            $start_item = ($current_page - 1) * $items_per_page + 1;
            $end_item = min($start_item + $items_per_page - 1, $total_items);
        ?>
            Hiển thị <strong><?php echo $start_item; ?>-<?php echo $end_item; ?></strong> của <strong><?php echo $total_items; ?></strong> mục
        <?php else: ?>
            Không có mục nào
        <?php endif; ?>
    </div>
<nav aria-label="Page navigation">
    <div class="pagination-controls">
        <?php if ($current_page > 1): ?>
            <button onclick="window.location.href='<?php echo generate_pagination_link($current_page - 1, $pagination_base_url); ?>'" aria-label="Previous">
                &laquo;
            </button>
        <?php else: ?>
            <button disabled aria-label="Previous">&laquo;</button>
        <?php endif; ?>

        <?php
        // Determine the start and end page numbers for the links
        $start = max(1, $current_page - $range);
        $end = min($total_pages, $current_page + $range);

        // Show first page link if needed
        if ($start > 1): ?>
            <button onclick="window.location.href='<?php echo generate_pagination_link(1, $pagination_base_url); ?>'">1</button>
            <?php if ($start > 2): ?>
                <span>...</span>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <button class="<?php echo ($i == $current_page ? 'active' : ''); ?>"
                    onclick="window.location.href='<?php echo generate_pagination_link($i, $pagination_base_url); ?>'">
                <?php echo $i; ?>
            </button>
        <?php endfor; ?>

        <?php if ($end < $total_pages): ?>
            <?php if ($end < $total_pages - 1): ?>
                <span>...</span>
            <?php endif; ?>
            <button onclick="window.location.href='<?php echo generate_pagination_link($total_pages, $pagination_base_url); ?>'">
                <?php echo $total_pages; ?>
            </button>
        <?php endif; ?>

        <?php if ($current_page < $total_pages): ?>
            <button onclick="window.location.href='<?php echo generate_pagination_link($current_page + 1, $pagination_base_url); ?>'" aria-label="Next">
                &raquo;
            </button>
        <?php else: ?>
            <button disabled aria-label="Next">&raquo;</button>
        <?php endif; ?>
    </div>
</nav>
</div> <!-- End pagination-footer -->
