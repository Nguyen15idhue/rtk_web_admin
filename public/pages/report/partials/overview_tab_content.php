<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\report\partials\overview_tab_content.php
?>
<!-- Reports cards: using stats-grid/stat-card -->
<div class="stats-grid">
    <!-- Render stat cards using PHP loop -->
    <?php foreach ($statSections as $section): ?>
    <div class="stat-card">
        <div class="icon <?php echo $section['icon_bg']; ?> <?php echo $section['icon_text']; ?>"><i class="<?php echo $section['icon_class']; ?>"></i></div>
        <div>
            <h3><?php echo $section['title']; ?></h3>
            <div class="space-y-2 text-sm">
                <?php foreach ($section['items'] as $item): ?>
                <div class="flex justify-between"><span><?php echo $item['label']; ?></span> <strong><?php echo $item['value']; ?></strong></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
