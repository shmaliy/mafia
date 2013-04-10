<?php 
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
?>
<div class="center_mod_title">Афиши</div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
<?php if (!isset($_GET['date'])): ?>
<?php echo $this->filter; ?>
<!-- CALENDAR -->
<div class="frontpage_center_mod_title">Календарь событий</div>
<?php
// calendar
$uTS = isset($_GET['month']) ? $_GET['month'] : mktime(0, 0, 0, date('m'), date('d'), date('Y'));
$cTS = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
$uDT = getdate($uTS);
$cDT = getdate($cTS);

// get used month days
$umDays = array(1 => 31, (!($uDT["year"] % 4) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

// processing prev & next month
$prevDT['mon'] = $uDT['mon'] == 1 ? 12 : $uDT['mon']-1;
$prevDT['year'] = $uDT['mon'] == 1 ? $uDT['year']-1 : $uDT['year'];
$prevTS = mktime(0, 0, 0, $prevDT['mon'], 1, $prevDT['year']);

$nextDT['mon'] = $uDT['mon'] == 12 ? 1 : $uDT['mon']+1;
$nextDT['year'] = $uDT['mon'] == 12 ? $uDT['year']+1 : $uDT['year'];
$nextTS = mktime(0, 0, 0, $nextDT['mon'], 1, $nextDT['year']);

// get first day of month
$first = getdate(mktime(0, 0, 0, $uDT['mon'], 1, $uDT['year']));

// parse weekday as sunday = 7;
$first["wday"] = $first["wday"] == 0 ? 7 : $first["wday"];

// count after empty items
$total = $first["wday"] + $umDays[$uDT['mon']] - 1;
if ($total < 35) {
    $after = 35 - $total;
} elseif ($total > 35 && $total < 42) {
    $after = 42 - $total;
} else {
    $after = 0;
}

$advLink = '';
if (isset($_GET['region'])) {
	$advLink .= '&region=' . $_GET['region'];
}
if (isset($_GET['city'])) {
	$advLink .= '&city=' . $_GET['city'];
}

$currentDate = $this->date;
$current = mktime(0, 0, 0, $currentDate["mon"]+1, 1, $currentDate["year"]);
//$current = time();
//$currentDate = getdate($current);
$firstDay = getdate(mktime(0, 0, 0, $currentDate["mon"], 1, $currentDate["year"]));
$monthDays = array(1 => 31, (!($uDT["year"] % 4) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
$monthNames = array(1 => "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
?>
<div class="calendar_my"><a href="/afishi?month=<?php echo $prevTS; ?><?php echo $advLink; ?>" class="prev">&larr;</a><a href="/afishi?month=<?php echo $nextTS; ?><?php echo $advLink; ?>" class="next">&rarr;</a><div class="month_name"><?php echo $this->mNames[$uDT["mon"]]; ?>, <?php echo $uDT["year"]; ?></div><div class="clr"></div></div>
<div class="calendar_items">
	<div class="calendar_i" style="height:20px;"><div class="day">ПН</div></div>
	<div class="calendar_i" style="height:20px;"><div class="day">ВТ</div></div>
	<div class="calendar_i" style="height:20px;"><div class="day">СР</div></div>
	<div class="calendar_i" style="height:20px;"><div class="day">ЧТ</div></div>
	<div class="calendar_i" style="height:20px;"><div class="day">ПТ</div></div>
	<div class="calendar_i" style="height:20px;"><div class="day">СБ</div></div>
	<div class="calendar_i" style="height:20px;"><div class="day">ВС</div></div>
	<?php for ($i = $first["wday"]; $i > 1; $i --): ?>
	    <div class="calendar_i invisible"><div class="day"><?php echo $umDays[$prevDT['mon']] - $i; ?></div></div>
	<?php endfor; ?>
    <?php for($day = 1; $day <= $umDays[$uDT['mon']]; $day ++): ?>
	<?php $dTS = mktime(0, 0, 0, $uDT["mon"], $day, $uDT["year"]); ?>
	<?php $dayInserted = false; ?>
    <?php if ($cTS == $dTS){ $advClass = ' calendar_ic'; } else { $advClass = '';} ?>
	<div class="calendar_i<?php echo $advClass; ?>">
        <div class="day"><?php echo $day; ?></div>
		<?php if (!empty($this->afishiAll)): ?>
    		<?php foreach ($this->afishiAll as $afisha): ?>
        		<?php if ($dayInserted == false && $afisha['publish_up'] > $dTS - 1 && $afisha['publish_up'] < $dTS + 24*60*60): ?>
        		<a style="margin:20px 0 0 10px; display:block;" href="/afishi?date=<?php echo $dTS; ?>"><img src="/image.php?i=<?php echo substr($afisha['image'], 1); ?>&t=png&m=fit&w=50&h=70&ca=cen&crop=true" /></a>
        		<?php $dayInserted = true; ?>
        		<?php endif; ?>
    		<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<?php endfor; ?>
    <?php for($p=1; $p<=$after; $p++): ?>
        <div class="calendar_i invisible"><div class="day"><?php echo $p; ?></div></div>
    <?php endfor; ?>
	<div class="clr"></div>
</div>
<?php else: ?>
    <div class="col_center_padding">
		<div class="submod_body">
			<div class="afishi">
				<div class="afishi_list_date"><?php echo (int) date('d', $_GET['date']); ?>&nbsp;<?php echo $_month[(int) date('m', $_GET['date'])]; ?>&nbsp;<?php echo date('Y', $_GET['date']); ?>г.</div>
				<?php if (!empty($this->afishiAll)): ?>
				<?php foreach ($this->afishiAll as  $item): ?>
				<div class="afishi_list_i">
					<a href="/club/club<?php echo $item['clubId']; ?>/afishi/afisha<?php echo $item['id']; ?>">
						<img src="/image.php?i=<?php echo substr($item['image'], 1); ?>&t=png&m=fit&w=120&h=175">
						<div class="photos_list_i_title">
							<div style="color: #593A24; font-weight: 600"><?php echo $item['clubTitle']; ?></div>
							<div><?php echo $item['title']; ?></div>
							<?php echo $item['city_title'] ? "<div>" . $item['city_title'] . "</div>" : ''; ?>
							<?php echo (int) date('d', $item['publish_up']); ?>&nbsp;<?php echo $_month[(int) date('m', $item['publish_up'])]; ?>&nbsp;<?php echo date('Y', $item['publish_up']); ?>г. <br />
							<?php echo (int) date('H', $item['publish_up']); ?><strong> : </strong><?php echo date('i', $item['publish_up']); ?>
						</div>
					</a>
				</div>
				<?php endforeach; ?>
				<div class="clr"></div>
				<?php if (isset($_GET['date'])): ?>
				<div class="i_more"><a href="/afishi" style="float:right; margin-left:5px;"><img src="/theme/default/img/calendar_icon.png"></a><a href="/afishi">вернуться к календарю</a></div>
				<?php endif; ?>
                <?php else: ?>
                <div class="empty">Нет Афиш</div>
        		<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>
</div>
