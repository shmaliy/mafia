<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_afishi = {' . implode(',', $this->constants) . '}';
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
        <?php $linkAll = '/club/club' . $this->club['id'] . '/afishi'; ?>
        <div class="submod_title">
            <?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
            <a href="<?php echo $linkAll; ?>/add" class="submod_title_btn">[добавить]</a>
            <?php endif; ?>
			<div class="route"><a class="home" href="/">&nbsp;</a> &rarr; <a href="/club">Клубы</a> &rarr; <a href="/club/club<?php echo $this->club['id']; ?>"><?php echo $this->club['title']; ?></a></div>
        </div>
        <div class="submod_body">
            <div class="afishi">
                <?php if(!empty($this->list)): ?>
                <?php foreach($this->list as $item): ?>
                <?php $linkItem = '/club/club' . $this->club['id'] . '/afishi/afisha' . $item['id']; ?>
                <?php $image = substr($item['image'], 1); ?>
                <div class="afishi_list_i">
                    <?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
                    <div class="edit_actions">
                        <a onclick="if(confirm('Удалить афишу: <?php echo $item['title']; ?>')){ afishaDelItem('<?php echo $item['id']; ?>'); }"><img src="/theme/default/img/delete.png" /></a>
                        <div class="clr"></div>
                    </div>
                    <?php endif; ?>
                	<a href="<?php echo $linkItem; ?>" title="<?php echo $item['title']; ?>"><img src="/image.php?i=<?php echo $image; ?>&t=png&m=fit&w=120&h=175" /><div class="photos_list_i_title"><?php echo $item['title']; ?></div></a>
                </div>
                <?php endforeach; ?>
                <div class="clr"></div>
                <?php else: ?>
                <div class="empty">Нет афиш</div>
                <?php endif; ?>
            </div>
        </div>
        <div id="create_afishi_stat"></div>
    </div>
</div>
<script>
    function afishaDelItem(id)
    {
        if (!id) {
            return;
        }
        new Ajax.Request('/index.php', {
            method: 'post',
            parameters: {
                'function': 'club_afishi->afishiDeleteItem',
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