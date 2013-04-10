<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_users = {' . implode(',', $this->constants) . '}';
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
        <?php $linkAll = '/club/club' . $this->club['id'] . '/users'; ?>
        <div class="submod_title">
            <?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
            <a href="<?php echo $linkAll; ?>/add" class="submod_title_btn">[добавить]</a>
            <?php endif; ?>
			<div class="route"><a class="home" href="/">&nbsp;</a> &rarr; <a href="/club">Клубы</a> &rarr; <a href="/club/club<?php echo $this->club['id']; ?>"><?php echo $this->club['title']; ?></a></div>
        </div>
        <div class="submod_body">
            <div class="users">
            	<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
               	<div class="users_rating_toggle">
               		<?php if($this->cat['param1'] == '1'): ?>
               		<a onclick="rate_toggle()">Скрыть рейтинг</a>
               		<?php else: ?>
               		<a onclick="rate_toggle()">Отобразить рейтинг</a>
               		<?php endif; ?>
               	</div>
               	<?php endif; ?>
                <?php if(!empty($this->list)): $i=0; ?>
                <?php foreach($this->list as $item): $i++; ?>
                <?php $linkItem = '/club/club' . $this->club['id'] . '/users/user' . $item['id']; ?>
				<div class="users_list_i">
                	<?php if ($i < 16): ?>
                	<div class="user_list_number"><strong><?php echo $i; ?></strong></div>
                    <?php else: ?>
                	<div class="user_list_number"><?php echo $i; ?></div>
                    <?php endif; ?>
					<div class="users_list_icon">
						<a href="<?php echo $linkItem; ?>"><img src="/image.php?i=<?php echo substr($item['image'], 1); ?>&t=png&m=fit&w=64&h=64&ca=top&crop=true" /></a>
					</div>
					<div class="users_list_text">
						<div class="user_title">
							<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
							<div class="edit_actions">
								<a title="Удалить" onclick="delItem('<?php echo $item['id']; ?>','<?php echo $item['title']; ?>');"><img src="/theme/default/img/delete.png" /></a>
								<div class="clr"></div>
							</div>
							<?php endif; ?>
							<strong style="font-size:14px">Имя: <a href="<?php echo $linkItem; ?>"><?php echo $item['title']; ?></a></strong>
						</div>
						<div class="user_title"><strong>Ник:</strong> <?php echo $item['param1']; ?></div>
						<div class="users_rating">
							<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
							<img src="/theme/default/img/rating_up.png" onclick="rate('<?php echo $item['id']; ?>','up', $('user_rate_<?php echo $item['id']; ?>').value)" />
							<img src="/theme/default/img/rating_down.png" onclick="rate('<?php echo $item['id']; ?>','down', $('user_rate_<?php echo $item['id']; ?>').value)" />
							<input id="user_rate_<?php echo $item['id']; ?>" type="text" />
							<div class="users_rating_label">введите баллы</div>
							<?php endif; ?>
        					<?php if($this->cat['param1'] == '1' || ($this->user && $this->user['email'] == $this->club['title_alias'])): ?>
        					<div class="users_rating_int"><strong>Рейтинг:</strong> <?php echo $item['param2']+0; ?> баллов</div>
        					<?php endif; ?>
							<div class="clr"></div>
						</div>
        				<div class="users_rating">
        					<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
        					<img src="/theme/default/img/rating_up.png" onclick="rateG('<?php echo $item['id']; ?>','up', $('user_rateG_<?php echo $item['id']; ?>').value)" />
        					<img src="/theme/default/img/rating_down.png" onclick="rateG('<?php echo $item['id']; ?>','down', $('user_rateG_<?php echo $item['id']; ?>').value)" />
        					<input id="user_rateG_<?php echo $item['id']; ?>" type="text" />
        					<div class="users_rating_label">введите количество</div>
        					<?php endif; ?>
        					<?php if($this->cat['param1'] == '1' || ($this->user && $this->user['email'] == $this->club['title_alias'])): ?>
        					<div class="users_rating_int"><strong>Рейтинговых игр:</strong> <?php echo $item['param3']+0; ?></div>
        					<?php endif; ?>
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
<script>
	function rate(id, mode, count)
	{
		if (/[+]?\d+/.test(count) == false || count <= 0){
			alert('Допускается только целое положительное число');
			return;
		}
		
		new Ajax.Request('/index.php', {
			method: 'post',
			parameters: {
				'function': 'club_users->usersRate',
				'mode': mode,
				'itemId': id,
				'count': count,
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

	function rateG(id, mode, count)
	{
		if (/[+]?\d+/.test(count) == false || count <= 0){
			alert('Допускается только целое положительное число');
			return;
		}
		
		new Ajax.Request('/index.php', {
			method: 'post',
			parameters: {
				'function': 'club_users->usersRateGames',
				'mode': mode,
				'itemId': id,
				'count': count,
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

	function rate_toggle()
	{
		new Ajax.Request('/index.php', {
			method: 'post',
			parameters: {
				'function': 'club_users->usersRateToggle',
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
    
	function delItem(id, title)
    {
        if (!id || !title) {
            return;
        }
        if (confirm('Удалть участника "' + title + '"?')) {
            new Ajax.Request('/index.php', {
                method: 'post',
                parameters: {
                    'function': 'club_users->usersDeleteItem',
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
    }
</script>