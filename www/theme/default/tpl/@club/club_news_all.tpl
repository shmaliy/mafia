<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_news = {' . implode(',', $this->constants) . '}';
}
?></script>
<div class="center_mod_title"><?php echo $this->cat['title']; ?></div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
	<div class="col_center_padding">
		<?php $linkAll = '/club/club' . $this->club['id'] . '/news'; ?>
		<div class="submod_title">
			<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
			<a href="<?php echo $linkAll; ?>/add" class="submod_title_btn">[добавить]</a>
			<?php endif; ?>
			<div class="route"><a class="home" href="/">&nbsp;</a> &rarr; <a href="/club">Клубы</a> &rarr; <a href="/club/club<?php echo $this->club['id']; ?>"><?php echo $this->club['title']; ?></a></div>
		</div>
		<div class="submod_body">
			<div class="news">
				<?php if($this->list): ?>
				<?php foreach($this->list as $item): ?>
				<div class="news_list_i">
					<?php $linkItem = '/club/club' . $this->club['id'] . '/news/news' . $item['id']; ?>
                    <?php if ($item['image'] != ''): ?>
                    <?php $image = substr($item['image'], 1); ?>
                    <div class="news_list_i_icon"><a href="<?php echo $linkItem; ?>"><img src="/image.php?i=<?php echo $image; ?>&t=png&m=fit&w=64&h=64&ca=cen&crop=true" /></a></div>
                    <?php endif; ?>
					<div class="news_list_i_intro">
						<div class="news_list_i_title">
							<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
                        	<div class="news_list_i_delete">
								<img src="/theme/default/img/delete.png" title="Удалить" onclick="if(confirm('Удалить новость: <?php echo $item['title']; ?>')){ newsDelItem('<?php echo $item['id']; ?>'); }" />
							</div>
							<?php endif; ?>
							<a href="<?php echo $linkItem; ?>"><?php echo $item['title']; ?></a>
						</div>
						<div><?php echo $item['introtext']; ?></div>
					</div>
					<div class="clr"></div>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		<div id="create_news_stat"></div>
    </div>
</div>
<script>
    function newsDelItem(id)
    {
        if (!id) {
            return;
        }
        new Ajax.Request('/index.php', {
            method: 'post',
            parameters: {
                'function': 'club_news->newsDeleteItem',
                'itemId': id,
                'clubId': '<?php echo $this->club['id']; ?>',
                'catId': '<?php echo $this->cat['id']; ?>'
            },
            encoding: 'UTF-8',
            onCreate : (function(){}).bind(this),
            onComplete : (function(request){
                eval(request.responseText);
            }).bind(this)
        });     
    }
</script>