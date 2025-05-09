<?php
// filepath: private\layouts\pagination.php
// Basic Pagination Logic (Create this file if it doesn't exist)

if (!isset($total_pages) || $total_pages <= 1) {
    return; // Don't display pagination if there's only one page or less
}

if (!isset($current_page)) {
    $current_page = 1;
}

if (!isset($pagination_base_url)) {
    $pagination_base_url = '?'; // Default base URL
}

// Function to generate pagination link, preserving existing query parameters
function generate_pagination_link($page, $base_url) {
    $query_params = $_GET; // Get current query parameters
    $query_params['page'] = $page; // Set/update the page parameter
    // Ensure base_url ends with '?' or '&' appropriately
    $separator = (strpos($base_url, '?') === false) ? '?' : '&';
    // Remove existing 'page' param from base_url if present to avoid duplicates
    $base_url = preg_replace('/([?&])page=[^&]*&?/', '$1', $base_url);
    // Ensure the base URL doesn't end with '?' or '&' before adding query string
     if (substr($base_url, -1) === '?' || substr($base_url, -1) === '&') {
         $base_url = rtrim($base_url, '?&');
     }
     $query_string = http_build_query($query_params);

     // Reconstruct the final URL
     return $base_url . (empty($query_string) ? '' : $separator . $query_string);


}

$range = 2; // Number of links to show around the current page

?>
<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php if ($current_page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo generate_pagination_link($current_page - 1, $pagination_base_url); ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link" aria-hidden="true">&laquo;</span>
            </li>
        <?php endif; ?>

        <?php
        // Determine the start and end page numbers for the links
        $start = max(1, $current_page - $range);
        $end = min($total_pages, $current_page + $range);

        // Show first page link if needed
        if ($start > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . generate_pagination_link(1, $pagination_base_url) . '">1</a></li>';
            if ($start > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Generate links within the range
        for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo generate_pagination_link($i, $pagination_base_url); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php
        // Show last page link if needed
        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="' . generate_pagination_link($total_pages, $pagination_base_url) . '">' . $total_pages . '</a></li>';
        }
        ?>

        <?php if ($current_page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo generate_pagination_link($current_page + 1, $pagination_base_url); ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link" aria-hidden="true">&raquo;</span>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<style>
/* Basic Pagination Styles (Add to your CSS or here) */
.pagination {
    display: flex;
    padding-left: 0;
    list-style: none;
    border-radius: .25rem;
    justify-content: center; /* Center pagination */
    margin-top: 1rem;
}

.page-item {
    margin: 0 2px;
}

.page-link {
    position: relative;
    display: block;
    padding: .5rem .75rem;
    margin-left: -1px;
    line-height: 1.25;
    color: #0d6efd; /* Updated Bootstrap 5 link color */
    background-color: #fff; /* Background */
    border: 1px solid #dee2e6; /* Border */
    text-decoration: none;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out;
}

@media (prefers-reduced-motion: reduce) {
  .page-link {
    transition: none;
  }
}


.page-link:hover {
    z-index: 2;
    color: #0a58ca; /* Updated hover color */
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    z-index: 3; /* Ensure active is above hover */
    color: #fff;
    background-color: #0d6efd; /* Active background */
    border-color: #0d6efd;
}

.page-item.disabled .page-link {
    color: #6c757d; /* Disabled color */
    pointer-events: none;
    /* cursor: auto; */ /* Removed as pointer-events: none is sufficient */
    background-color: #fff;
    border-color: #dee2e6;
}
</style>
