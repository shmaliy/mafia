<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<?php if (!empty($this->frontpageCenterText)): ?>
<div class="col_center_padding">
<?php echo $this->frontpageCenterText['fulltext']; ?>
</div>
<?php endif; ?>
<?php echo $this->newsList; ?>

