<div class="center_mod_title"><?php echo $this->item['title']; ?></div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
	<div class="col_center_padding">
        <?php echo $this->item['fulltext']; ?>
    </div>
</div>
