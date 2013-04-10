<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_afishi = {' . implode(',', $this->constants) . '}';
}
$_month = array(
    1 => 'Января',
    'Февраля',
    'Марта',
    'Апреля',
    'Мая',
    'Июня',
    'Июля',
    'Августа',
    'Сентября',
    'Октября',
    'Ноября',
    'Декабря'
);
?></script>
<div class="center_mod_title"><?php echo $this->item['title']; ?></div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
	<div class="col_center_padding">
		<?php $linkAll = '/club/club' . $this->club['id'] . '/afishi'; ?>
		<div class="submod_title">
            <?php $linkEdit = '/club/club' . $this->club['id'] . '/afishi/afisha' . $this->item['id'] . '/edit'; ?>
            <?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
                <a href="<?php echo $linkEdit; ?>" class="mod_title_btn">[редактировать]</a>
            <?php endif; ?>
			<div class="route"><a class="home" href="/">&nbsp;</a> &rarr; <a href="/club">Клубы</a> &rarr; <a href="/club/club<?php echo $this->club['id']; ?>"><?php echo $this->club['title']; ?></a> &rarr; <a href="<?php echo $linkAll; ?>"><?php echo $this->cat['title']; ?></a></div>
		</div>
		<div class="submod_body">
		<div class="club_avatar">
			<a rel="lightbox" href="/image.php?i=<?php echo substr($this->item['image'], 1); ?>&t=png&m=fit&w=600&h=600">
				<img src="/image.php?i=<?php echo substr($this->item['image'], 1); ?>&t=png&m=side&sm=width&s=150" />
			</a>
		<?php if(!empty($this->vktext)): ?>
		<div>&nbsp;</div>
		<script type="text/javascript">
			//<!--
			document.write(VK.Share.button(
				{
					url: 'http://mirmafii.com.ua<?php echo $linkAll . '/afisha' . $this->item['id']; ?>',
					title: '<?php echo $this->vktext['introtext']; ?>',
					description: '<?php echo $this->vktext['fulltext']; ?> <?php echo $this->item['title']; ?>',
					noparse: true
				},
				{text: '<?php echo $this->vktext['title']; ?>',type:'round'}
			));
			//-->
		</script>
		<?php endif; ?>
		</div>
		<div class="club_info">
			<table class="club_info_user" cellpadding="0" cellspacing="0">
				<tr>
					<td class="col1">Город:</td>
					<td class="col2"><?php echo $this->city['title']; ?></td>
				</tr>
				<tr>
					<td class="col1">Дата:</td>
					<td class="col2"><?php echo (int) date('d', $this->item['publish_up']); ?>&nbsp;<?php echo $_month[(int) date('m', $this->item['publish_up'])]; ?>&nbsp;<?php echo date('Y', $this->item['publish_up']); ?>г.</td>
				</tr>
				<tr>
					<td class="col1">Время:</td>
					<td class="col2"><?php echo (int) date('H', $this->item['publish_up']); ?><strong> : </strong><?php echo date('i', $this->item['publish_up']); ?></td>
				</tr>
				<?php if (!empty($this->item['param1'])): ?>
				<tr>
					<td class="col1">Место<br />проведения:</td>
					<td class="col2"><?php echo $this->item['param1']; ?></td>
				</tr>
				<?php endif; ?>
			</table>
		</div>
			<div class="news">
				<div class="news_list_i">
					<div class="news_list_i_full"><?php echo $this->item['introtext']; ?></div>
				</div>
			</div>
		</div>
		<div class="clr"></div>
    </div>
</div>
<div class="center_mod_body">
    <div class="frontpage_center_mod_title" style="padding:0 25px; font-size:18px;">Записаться на игру</div>
    <form name="zakaz_stolov_form" action="#" method="post" enctype="multipart/form-data" onsubmit="return zakaz_item(this,event);">
    <input type="hidden" name="function" value="club_afishi->zakazItem" />
    <input type="hidden" name="id" value="<?php echo $this->item['id']; ?>" />
    <table class="form" cellpadding="0" cellspacing="0">
        <tr>
            <td class="col1">Ф.И.О.</td>
            <td class="col2" colspan="2"><input type="text" name="fio" /></td>
        </tr>
        <tr>
            <td class="col1">Эл. почта</td>
            <td class="col2" colspan="2"><input type="text" name="email" /></td>
        </tr>
        <tr>
            <td class="col1">Телефон</td>
            <td class="col2" colspan="2"><input type="text" name="phone" /></td>
        </tr>
        <tr>
            <td class="col1">Кол-во людей</td>
            <td class="col2" colspan="2">
            	<input class="radio" type="radio" name="count" value="1" checked="checked" />1
            	<input class="radio" type="radio" name="count" value="2" />2
            	<input class="radio" type="radio" name="count" value="3" />3
            </td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td class="col3"><input id="zakaz_stolov_form_submit" class="submit" style="width:110px;" type="submit" value="Заказать" /></td>
        </tr>
        <tr>
            <td colspan="3"><div class="stat" id="create_afishi_stat"></div></td>
        </tr>
    </table>
    </form>
</div>
<script>
    function zakaz_item(data, event)
    {
        event.stop();
        if (data.tagName.toUpperCase() == 'FORM'){
            var data = data.serialize(true);
        }

        data['clubId'] = '<?php echo $this->club['id']; ?>';
        data['string'] = '"<?php echo $this->item['title']; ?>" <?php echo $this->city['title']; ?> <?php echo (int) date('d', $this->item['publish_up']); ?> <?php echo $_month[(int) date('m', $this->item['publish_up'])]; ?> <?php echo date('Y', $this->item['publish_up']); ?>г. <?php echo (int) date('H', $this->item['publish_up']); ?> : <?php echo date('i', $this->item['publish_up']); ?>';

        new Ajax.Request('/index.php', {
            method: 'post',
            parameters: data,
            encoding: 'UTF-8',
            onCreate : (function(){
                document.zakaz_stolov_form.fio.setStyle({outline:"none"});
                document.zakaz_stolov_form.email.setStyle({outline:"none"});
                document.zakaz_stolov_form.phone.setStyle({outline:"none"});
                document.zakaz_stolov_form.disable();
            }).bind(this),
            onComplete : (function(request){
                eval(request.responseText);
            }).bind(this)
        });
        return false;
    }   
</script>