<div class="right_mod_title">Организатор клуба</div>
<div class="right_mog_body">
	<div class="left_menu">
		<ul>
            <?php if (!empty($this->club)): ?>
	        <li><a href="/club/club<?php echo $this->club['id']; ?>">Клуб</a></li>
	        <li class="menu_separator">&nbsp;</li>
			<li><a href="/club/club<?php echo $this->club['id']; ?>/afishi">Афиши</a></li>
			<li class="menu_separator">&nbsp;</li>
			<li><a href="/club/club<?php echo $this->club['id']; ?>/photos">Фото</a></li>
			<li class="menu_separator">&nbsp;</li>
			<li><a href="/club/club<?php echo $this->club['id']; ?>/videos">Видео</a></li>
			<li class="menu_separator">&nbsp;</li>
			<li><a href="/club/club<?php echo $this->club['id']; ?>/users">Информация о участниках и рейтинг</a></li>
			<li class="menu_separator">&nbsp;</li>
			<li><a href="/club/club<?php echo $this->club['id']; ?>/news">Новости клуба</a></li>
            <?php else: ?>
	        <li><a href="/club/add">Создать клуб</a></li>
            <?php endif; ?>
			<li class="menu_separator">&nbsp;</li>
            <li><a href="/club/admin">Редактировать личные данные</a></li>
			<li class="exit"><input class="submit" type="submit" value="Выйти" onClick="logout();" /></li>
		</ul>
	</div>
</div>
<script>
	function logout()
	{
		new Ajax.Request('/index.php', {
			method: 'post',
			parameters: {'function':'club_admin->adminLogout','location':window.location.href},
			encoding: 'UTF-8',
			onCreate : (function(){}).bind(this),
			onComplete : (function(request){
				eval(request.responseText);
			}).bind(this)
		});		
	}
</script>
