<div class="center_mod_title">Клубы</div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
<?php echo $this->filter; ?>
	<div class="col_center_padding">
		<!-- filter place -->
        <?php if(!empty($this->list)): ?>
            <div class="afishi">
                <?php foreach($this->list as $item): ?>
                <?php $linkItem = '/club/club' . $item['id']; ?>
                <?php $image = substr($item['image'], 1); ?>
                <div class="afishi_list_i"><a href="<?php echo $linkItem; ?>" title="<?php echo $item['title']; ?>"><img src="/image.php?i=<?php echo $image; ?>&t=png&m=fit&w=120&h=175&ca=cen&bg=000000" /><div class="photos_list_i_title"><?php echo $item['title']; ?><?php echo $item['city']['title'] ? "<div>(" . $item['city']['title'] . ")</div>" : ''; ?></div></a>
                </div>
                <?php endforeach; ?>
                <div class="clr"></div>
            </div>
        <?php endif; ?>
    </div>
</div>
