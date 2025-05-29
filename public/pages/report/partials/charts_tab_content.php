<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\report\partials\charts_tab_content.php
?>
<div class="charts-section">
    <div class="charts-grid">
        <!-- Render chart sections using PHP loop -->
        <?php foreach ($chartSections as $chart): ?>
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title"><?php echo $chart['title']; ?></h3>
            </div>
            <div class="chart-body">
                <div class="chart-container">
                    <canvas id="<?php echo $chart['id']; ?>"></canvas>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
