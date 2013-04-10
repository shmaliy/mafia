<?php
$linkClub = $linkAfisha = '/club/register';
if (!empty($this->user)) {
    if (empty($this->club)) {
        $linkClub = $linkAfisha = '/club/add';
    } else {
        $linkAfisha = "/club/club" . $this->club['id'] . "/afishi/add";
    }
}
?>
<div class="filter_shadow">
    <?php if ($this->state === true): ?>
	<div class="filter_bg">
	<?php else: ?>
	<div class="filter_bg2">
	<?php endif; ?>
		<div class="filter_title">
			<?php if (empty($this->club)): ?>
			<a href="<?php echo $linkClub; ?>">добавить клуб</a>
			<?php endif; ?>
			<a class="filter_add_button" href="<?php echo $linkAfisha; ?>">добавить мероприятие</a>
			<span>Фильтр клубов и событий</span>
		</div>
		<form name="filter" method="get">
		<?php if (isset($_GET['page'])): ?>
		<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
		<?php endif; ?>
        <?php if (isset($_GET['date'])): ?>
        <input type="hidden" name="date" value="<?php echo $_GET['date']; ?>" />
        <?php endif; ?>
        <?php if (isset($_GET['month'])): ?>
        <input type="hidden" name="month" value="<?php echo $_GET['month']; ?>" />
        <?php endif; ?>
		<table class="filter_form" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="stitle">Область</td>
				<td class="select" id="filterRegion">
                	<select name="region" onChange="submitFilter(document.filter, true);">
                        <option value="0">Все</option>
    				    <?php if (!empty($this->filter)): ?>
                        <?php foreach ($this->filter as $k => $item): ?>
                        <?php if (isset($_GET['region']) && $_GET['region'] != 0 && $_GET['region'] == $k): ?>
                        <?php if (!empty($item['childs'])){ $childs = $item['childs']; } ?>
                        <option value="<?php echo $k; ?>" selected="selected"><?php echo $item['title']; ?></option>
                        <?php else: ?>
                        <option value="<?php echo $k; ?>"><?php echo $item['title']; ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </td>
				<td class="sep">&nbsp;</td>
				<td class="stitle2">Город</td>
				<td class="select" id="filterCity">
                    <select name="city" onChange="submitFilter(document.filter);">
                        <option value="0">Все</option>
                        <?php if (!empty($this->filter) && !empty($childs)): ?>
                        <?php foreach ($childs as $k => $item): ?>
                        <?php if (isset($_GET['city']) && $_GET['city'] != 0 && $_GET['city'] == $k): ?>
                        <option value="<?php echo $k; ?>" selected="selected"><?php echo $item; ?></option>
                        <?php else: ?>
                        <option value="<?php echo $k; ?>"><?php echo $item; ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
				</td>
			</tr>
		</table>
		</form>
		<script>
			function submitFilter(e, reset)
			{
			    if (e.region.selectedIndex == '0' || reset == true){ e.city.selectedIndex = '0'; }
			    e.submit();
    			return false;
			}
		</script>
	</div>
</div>
