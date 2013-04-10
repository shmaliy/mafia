<?php if ($this->contList): ?>
<div class="frontpage_center_mod_title">Новости сайта</div>
<div class="center_mod_body" style="padding-bottom:40px;">
    <div class="news">
        <?php foreach ($this->contList as $item): ?>
        <div class="news_list_i">
            <?php $linkItem = '/news/' . $item['id']; ?>
            <?php if ($item['image'] != ''): ?>
            <?php $image = substr($item['image'], 1); ?>
            <div class="news_list_i_icon"><a href="<?php echo $linkItem; ?>"><img src="/image.php?i=<?php echo $image; ?>&t=png&m=fit&w=64&h=64&ca=cen&crop=true" /></a></div>
            <?php endif; ?>
            <div class="news_list_i_intro">
                <div class="news_list_i_title"><span class="news_date"><?php echo (int) date('d', $item['created']); ?> <?php echo $this->month[(int) date('m', $item['created'])]; ?> <?php echo date('Y', $item['created']); ?>г.</span>
                    <a href="<?php echo $linkItem; ?>"><?php echo $item['title']; ?></a>
                </div>
                <div><?php echo $item['introtext']; ?></div>
            </div>
            <div class="clr"></div>
        </div>
        <?php endforeach; ?>
        <div class="i_more"><a href="/news/">все новости</a></div>          
    </div>      
</div>
<?php endif; ?>
