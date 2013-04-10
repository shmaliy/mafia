<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_photos = {' . implode(',', $this->constants) . '}';
}
?></script>
<div class="center_mod_title"><?php echo $this->item['title']; ?></div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
    <div class="col_center_padding">
		<?php $linkAll = '/club/club' . $this->club['id'] . '/photos'; ?>
		<div class="submod_title">
    		<?php $linkEdit = '/club/club' . $this->club['id'] . '/photos/photo' . $this->item['id'] . '/edit'; ?>
    		<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
    			<a href="<?php echo $linkEdit; ?>" class="mod_title_btn">[редактировать]</a>
    		<?php endif; ?>
			<div class="route"><a class="home" href="/">&nbsp;</a> &rarr; <a href="/club">Клубы</a> &rarr; <a href="/club/club<?php echo $this->club['id']; ?>"><?php echo $this->club['title']; ?></a> &rarr; <a href="<?php echo $linkAll; ?>"><?php echo $this->cat['title']; ?></a></div>
		</div>
        <div class="submod_body" style="padding-top:5px;">
            <div class="photos">
                <?php if($this->item['images'] != ''): ?>
                <?php foreach(explode('|', $this->item['images']) as $item): ?>
                <div class="photos_list_i"><a rel="lightbox[]" href="/image.php?i=<?php echo substr($item, 1); ?>&t=png&m=side&s=600&sm=long"><img src="/image.php?i=<?php echo substr($item, 1); ?>&t=png&m=fit&w=120&h=120&ca=cen&crop=true" /></a></div>
                <?php endforeach; ?>
                <div class="clr"></div>
                <?php else: ?>
                <div class="empty">Нет фотографий</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
