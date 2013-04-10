<div class="center_mod_title"><?php echo $this->cat['title']; ?></div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
	<div class="col_center_padding">
        <?php if (!empty($this->catList)): ?>
        <ul>
        <?php foreach ($this->catList as $item): ?>
            <li><?php echo $item['title']; ?></li>
        <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if (!empty($this->contList)): ?>
        <?php foreach ($this->contList as $item): ?>
        <div class="news_list_i">
            <?php $linkItem = '/news/' . $item['id']; ?>
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
		<?php if ($this->contListTotal > $this->rows): ?>
        <div class="paginator"><div class="text">Страница:</div>
	        <?php $pages = ceil($this->contListTotal / $this->rows); ?>
			<?php for ($p=1; $p<=$pages; $p++): ?>
				<?php if ($p == $this->page): ?>
					<a class="current"><?php echo $p; ?></a>
				<?php else: ?>
					<a href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
				<?php endif; ?>
			<?php endfor; ?>
        </div>
		<?php endif; ?>
        <?php endif; ?>
    </div>
</div>
