<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_news = {' . implode(',', $this->constants) . '}';
}
?></script>
<?php $linkAll = '/club/club' . $this->club['id'] . '/news'; ?>
<div class="submod_title">
	<span><?php echo $this->cat['title']; ?></span>
	<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
	<a href="<?php echo $linkAll; ?>/add" class="submod_title_btn">[добавить]</a>
	<?php endif; ?>
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
					<a href="<?php echo $linkItem; ?>"><?php echo $item['title']; ?></a>
				</div>
				<div><?php echo $item['introtext']; ?></div>
			</div>
            <div class="clr"></div>
        </div>
		<?php endforeach; ?>
        <div class="i_more"><a href="<?php echo $linkAll; ?>">все новости</a></div>
        <?php else: ?>
        <div class="empty">Нет новостей</div>
		<?php endif; ?>
    </div>
</div>
