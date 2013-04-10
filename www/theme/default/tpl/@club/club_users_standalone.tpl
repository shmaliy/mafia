<div class="center_mod_title">Рейтинг</div>
<div class="center_mod_body">
<?php echo $this->filter; ?>
    <div class="col_center_padding">
        <div class="submod_body">
            <div class="users">
                <?php if(!empty($this->list)): $i=0; ?>
                <?php foreach($this->list as $item): $i++; ?>
                <?php $linkItem = '/club/club' . $item['clubId'] . '/users/user' . $item['id']; ?>
                <div class="users_list_i">
                    <div class="users_list_icon">
                        <a href="<?php echo $linkItem; ?>"><img src="/image.php?i=<?php echo substr($item['image'], 1); ?>&t=png&m=fit&w=64&h=64&ca=top&crop=true" /></a>
                    </div>
                    <div class="users_list_text">
                        <div class="user_title">
                            <strong style="font-size:14px">Имя: <a href="<?php echo $linkItem; ?>"><?php echo $item['title']; ?></a></strong>
                        </div>
                        <div class="user_title"><strong>Ник:</strong> <?php echo $item['param1']; ?></div>
                        <div class="users_rating">
                            <div class="users_rating_int"><strong>Рейтинг:</strong> <?php echo $item['param2']+0; ?> баллов</div>
                            <div class="clr"></div>
                        </div>
                    </div>
                    <div class="clr"></div>
                </div>
                <?php if (count($this->list) > $i): ?>
                <div class="users_list_i_sep"></div>
                <?php endif; ?>
                <?php endforeach; ?>
                <div class="clr"></div>
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
                <?php else: ?>
                <div class="empty">Нет участников</div>
                <?php endif; ?>
            </div>
        </div>
        <div id="create_users_stat"></div>
    </div>
</div>
