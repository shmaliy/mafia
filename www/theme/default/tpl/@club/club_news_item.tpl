<div class="center_mod_title"><?php echo $this->item['title']; ?></div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
	<div class="col_center_padding">
		<?php $linkAll = '/club/club' . $this->club['id'] . '/news'; ?>
		<div class="submod_title">
    		<?php $linkEdit = '/club/club' . $this->club['id'] . '/news/news' . $this->item['id'] . '/edit'; ?>
    		<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
    			<a href="<?php echo $linkEdit; ?>" class="mod_title_btn">[редактировать]</a>
    		<?php endif; ?>
			<div class="route"><a class="home" href="/">&nbsp;</a> &rarr; <a href="/club">Клубы</a> &rarr; <a href="/club/club<?php echo $this->club['id']; ?>"><?php echo $this->club['title']; ?></a> &rarr; <a href="<?php echo $linkAll; ?>"><?php echo $this->cat['title']; ?></a></div>
		</div>
		<div class="submod_body">
			<div class="news">
                <?php if ($this->item['image'] != ''): ?>
                <?php $image = substr($this->item['image'], 1); ?>
                <div class="news_list_i_icon"><img src="/image.php?i=<?php echo $image; ?>&t=png&m=fit&w=64&h=64&ca=cen&crop=true" /></div>
                <?php endif; ?>
                <div class="news_list_i_intro">
                    <div class="news_list_i_title"><span class="news_date"><?php echo (int) date('d', $this->item['created']); ?> <?php echo $this->month[(int) date('m', $this->item['created'])]; ?> <?php echo date('Y', $this->item['created']); ?>г.</span>
                        <a><?php echo $this->item['title']; ?></a>
                    </div>
                    <div><?php echo $this->item['introtext']; ?></div>
                </div>
                <div class="clr"></div>
				<div class="news_list_i">
					<div class="news_list_i_full"><?php echo $this->item['fulltext']; ?></div>
				</div>
			</div>
		</div>
    </div>
</div>
