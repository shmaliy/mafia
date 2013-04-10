<div class="center_mod_title"><?php echo $this->item['title']; ?></div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
	<div class="col_center_padding">
        <div class="news">
            <div class="news_list_i">
                <?php if ($this->item['image'] != ''): ?>
                <?php $image = substr($this->item['image'], 1); ?>
                <div class="news_list_i_icon"><img src="/image.php?i=<?php echo $image; ?>&t=png&m=fit&w=64&h=64&ca=cen&crop=true" /></div>
                <?php endif; ?>
                <div class="news_list_i_intro">
                    <div class="news_list_i_title"><span class="news_date"><?php echo (int) date('d', $this->item['created']); ?> <?php echo $this->month[(int) date('m', $this->item['created'])]; ?> <?php echo date('Y', $this->item['created']); ?>ã.</span>
                        <a><?php echo $this->item['title']; ?></a>
                    </div>
                    <div><?php echo $this->item['introtext']; ?></div>
                </div>
                <div class="clr"></div>
                <div><?php echo $this->item['fulltext']; ?></div>
            </div>
        </div>      
    </div>
</div>
