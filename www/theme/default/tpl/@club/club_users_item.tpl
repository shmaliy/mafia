<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_users = {' . implode(',', $this->constants) . '}';
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
        <?php $linkAll = '/club/club' . $this->club['id'] . '/users'; ?>
        <div class="submod_title">
            <?php $linkEdit = '/club/club' . $this->club['id'] . '/users/user' . $this->item['id'] . '/edit'; ?>
            <?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
                <a href="<?php echo $linkEdit; ?>" class="mod_title_btn">[редактировать]</a>
            <?php endif; ?>
			<div class="route"><a class="home" href="/">&nbsp;</a> &rarr; <a href="/club">Клубы</a> &rarr; <a href="/club/club<?php echo $this->club['id']; ?>"><?php echo $this->club['title']; ?></a> &rarr; <a href="<?php echo $linkAll; ?>"><?php echo $this->cat['title']; ?></a></div>
        </div>
		<div class="clr"></div>
		<div class="club_avatar">
			<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
            <div class="edit_actions">
                <a onclick="delItem('<?php echo $item['id']; ?>','<?php echo $item['title']; ?>');"><img src="/theme/default/img/delete.png" /></a>
                <div class="clr"></div>
            </div>
			<?php endif; ?>
	        <img src="/image.php?i=<?php echo substr($this->item['image'], 1); ?>&t=png&m=side&s=150&sm=short" />
			<div style="padding-top:5px; font-size:12px;">Мне нравится этот игрок.<br />Я голосую за него.</div>
			<div style="padding-top:5px;">
			<script type="text/javascript">
			<!--
			document.write(VK.Share.button(
				{
					url: 'http://mirmafii.com.ua<?php echo $linkAll . '/user' . $this->item['id']; ?>',
					title: 'Я люблю убивать Мафию с этим игроком. А ты?',
					description: 'Имя: <?php echo $this->item['title']; ?>\nНик: <?php echo $this->item['param1']; ?>',
					noparse: true
				},
				{text: 'Хороший игрок',type:'round'}
			));
			-->
			</script>
			</div>
        </div>
		<div class="club_info">
			<div class="club_info_title">Информация о участнике</div>
			<table class="club_info_user" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="col1" width="130">Имя:</td>
                    <td class="col2"><strong><?php echo $this->item['title']; ?></strong></td>
                </tr>
				<tr>
					<td class="col1">Ник:</td>
					<td class="col2"><?php echo $this->item['param1']; ?></td>
				</tr>
				<?php if($this->cat['param1'] == '1' || ($this->user && $this->user['email'] == $this->club['title_alias'])): ?>
				<tr>
					<td class="col1">Рейтинг:</td>
					<td class="col2">
						<div class="users_rating">
							<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
							<img src="/theme/default/img/rating_up.png" onclick="rate('<?php echo $this->item['id']; ?>','up', $('user_rate_<?php echo $item['id']; ?>').value)" />
							<img src="/theme/default/img/rating_down.png" onclick="rate('<?php echo $this->item['id']; ?>','down', $('user_rate_<?php echo $item['id']; ?>').value)" />
							<input id="user_rate_<?php echo $item['id']; ?>" type="text" />
							<?php endif; ?>
							<div class="users_rating_int"><?php echo $this->item['param2']+0; ?> баллов</div>
							<div class="clr"></div>
						</div>
					</td>
				</tr>
				<tr>
					<td class="col1">Рейтинговых игр:</td>
					<td class="col2">
						<div class="users_rating">
							<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
							<img src="/theme/default/img/rating_up.png" onclick="rateG('<?php echo $this->item['id']; ?>','up', $('user_rateG_<?php echo $item['id']; ?>').value)" />
							<img src="/theme/default/img/rating_down.png" onclick="rateG('<?php echo $this->item['id']; ?>','down', $('user_rateG_<?php echo $item['id']; ?>').value)" />
							<input id="user_rateG_<?php echo $item['id']; ?>" type="text" />
							<?php endif; ?>
							<div class="users_rating_int"><?php echo $this->item['param3']+0; ?></div>
							<div class="clr"></div>
						</div>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td colspan="2"><?php echo $this->item['introtext']; ?></td>
				</tr>
			</table>
		</div>
		<div class="clr"></div>
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