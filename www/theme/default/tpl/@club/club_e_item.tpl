<div class="center_mod_title"><?php echo $this->club['title']; ?></div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
	<div class="col_center_padding">
		<div style="height:20px; overflow:hidden;">
		<?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
			<a href="/club/club<?php echo $this->club['id']; ?>/edit" class="mod_title_btn">[редактировать]</a>
		<?php endif; ?>
		</div>
		<div class="clr"></div>
		<div class="club_avatar"><img src="/image.php?i=<?php echo substr($this->club['image'], 1); ?>&t=png&m=side&sm=width&s=150" /></div>
		<div class="club_info">
			<div class="club_info_title">Информация о клубе</div>
			<table class="club_info_user" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="col1">Администратор:</td>
                    <td class="col2"><?php echo $this->admin['param1'] . ' ' . $this->admin['param2'] . ' ' . $this->admin['param3']; ?></td>
                </tr>
				<tr>
					<td class="col1">Город:</td>
					<td class="col2"><?php echo $this->admin['param6']; ?></td>
				</tr>
				<tr>
					<td class="col1">Адрес:</td>
					<td class="col2"><?php echo $this->admin['param7']; ?></td>
				</tr>
				<tr>
					<td class="col1">Эл. почта:</td>
					<td class="col2"><?php echo $this->admin['email']; ?></td>
				</tr>
				<tr>
					<td class="col1">Телефон:</td>
					<td class="col2"><?php echo $this->admin['param4']; ?></td>
				</tr>
				<tr>
					<td class="col1">Сайт:</td>
					<td class="col2"><a href="<?php echo $this->admin['param8']; ?>" target="_blank"><?php echo $this->admin['param8']; ?></a></td>
				</tr>
			</table>
		</div>
		<div class="club_desc"><?php echo $this->club['description']; ?></div>
		<div class="clr"></div>
        <?php echo $this->news; ?>
        <?php echo $this->users; ?>
        <?php echo $this->afishi; ?>
        <?php echo $this->photos; ?>
        <?php echo $this->videos; ?>
    </div>
</div>
