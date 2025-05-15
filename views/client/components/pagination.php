<?php
function renderPagination($totalPages, $currentPage, $baseUrl) {
    if ($totalPages <= 1) return;
    
    $params = $_GET;
    unset($params['page']); // Remove existing page from params
    $queryString = http_build_query($params);
    $baseUrl = $baseUrl . ($queryString ? "?{$queryString}&" : "?");
?>
<div class="flex items-center justify-between px-2 py-4">
    <div class="flex-1 text-sm text-muted-foreground">
        Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
    </div>
    <div class="flex items-center space-x-2">
        <a href="<?php echo $baseUrl . 'page=' . max(1, $currentPage - 1); ?>"
           class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0 <?php echo $currentPage <= 1 ? 'opacity-50 pointer-events-none' : ''; ?>"
           <?php echo $currentPage <= 1 ? 'aria-disabled="true"' : ''; ?>>
            <span class="sr-only">Previous page</span>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        
        <?php
        $range = 2;
        for ($i = max(1, $currentPage - $range); $i <= min($totalPages, $currentPage + $range); $i++):
        ?>
            <a href="<?php echo $baseUrl . 'page=' . $i; ?>"
               class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input hover:bg-accent hover:text-accent-foreground h-8 min-w-[2rem] <?php echo $i === $currentPage ? 'bg-primary text-primary-foreground hover:bg-primary/90 border-primary' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <a href="<?php echo $baseUrl . 'page=' . min($totalPages, $currentPage + 1); ?>"
           class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0 <?php echo $currentPage >= $totalPages ? 'opacity-50 pointer-events-none' : ''; ?>"
           <?php echo $currentPage >= $totalPages ? 'aria-disabled="true"' : ''; ?>>
            <span class="sr-only">Next page</span>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>
<?php
}
?> 