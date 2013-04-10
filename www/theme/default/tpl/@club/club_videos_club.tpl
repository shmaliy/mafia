<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_videos = {' . implode(',', $this->constants) . '}';
}
?></script>
<?php $linkAll = '/club/club' . $this->club['id'] . '/videos'; ?>
<div class="submod_title">
    <span><?php echo $this->cat['title']; ?></span>
    <?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
    <a href="<?php echo $linkAll; ?>/add" class="submod_title_btn">[добавить]</a>
    <?php endif; ?>
</div>
<div class="submod_body">
    <div class="videos">
        <?php if(!empty($this->list)): ?>
        <?php foreach($this->list as $item): ?>
        <?php $linkItem = '/club/club' . $this->club['id'] . '/videos/video' . $item['id']; ?>
        <?php $image = substr($item['image'], 1); ?>
        <div class="videos_list_i"><a href="<?php echo $linkItem; ?>"><img src="/image.php?i=<?php echo $image; ?>&t=png&m=fit&w=120&h=90&ca=cen&bg=000000" /><div class="photos_list_i_title"><?php echo $item['title']; ?></div></a></div>
        <?php endforeach; ?>
        <div class="clr"></div>
        <div class="i_more"><a href="<?php echo $linkAll; ?>">все видеозаписи</a></div>
        <?php else: ?>
        <div class="empty">Ќет видеозаписей</div>
        <?php endif; ?>
    </div>
</div>
