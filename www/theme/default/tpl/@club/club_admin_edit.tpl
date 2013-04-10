<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_admin = {' . implode(',', $this->constants) . '}';
}
?></script>
<script><?php
            if (!empty($this->filter)){
                foreach ($this->filter as $k => $item) {
                    $level2 = array();
                    foreach ($item as $key => $value) {
                        if ($key != 'childs') {
                            $level2[] = "'$key'" . ": '$value'";
                        } else {
                            $level3 = array();
                            foreach ($value as $id => $title) {
                                $level3[] = $id . ": '$title'";
                            }
                            $level2[] = "'$key'" . ':{' . implode(',', $level3) . '}';
                        }
                    }
                    $level[] = $k . ':{' . implode(',', $level2) . '}';
                }
                echo 'var filter = {' . implode(',', $level) . '}';
            }
?></script>
<?php if (!empty($this->item)): ?>
<div class="center_mod_title">Редактирование личных данных</div>
<?php else: ?>
<div class="center_mod_title">Регистрация нового организатора клуба</div>
<?php endif; ?>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
	<div style="padding: 10px 10px 10px 25px;">Это страничка регистрации организатора клуба. Заполните, пожалуйста, все поля ниже.</div>
    <form name="create_admin_form" action="#" method="post" enctype="multipart/form-data" onsubmit="return create(this,event);">
    <input type="hidden" name="function" value="club_admin->adminSaveItem" />
    <table class="form" cellpadding="0" cellspacing="0">
        <?php if (empty($this->item)): ?>
        <tr>
            <td class="col1">Эл. почта</td>
            <td class="col2" colspan="2"><input type="text" name="email" value="<?php echo $this->item['email']; ?>" /></td>
        </tr>
        <tr>
            <td class="col1">Пароль</td>
            <td class="col2" colspan="2"><input type="password" name="password" /></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="col1">Фамилия</td>
            <td class="col2" colspan="2"><input type="text" name="param1" value="<?php echo $this->item['param1']; ?>" /></td>
        </tr>
        <tr>
            <td class="col1">Имя</td>
            <td class="col2" colspan="2"><input type="text" name="param2" value="<?php echo $this->item['param2']; ?>" /></td>
        </tr>
        <tr>
            <td class="col1">Телефон</td>
            <td class="col2" colspan="2"><input type="text" name="param4" value="<?php echo $this->item['param4']; ?>" /></td>
        </tr>
        <tr>
            <td>Область</td>
            <td class="col2" colspan="2" id="filterRegion"></td>
        </tr>
        <tr>
            <td>Город</td>
            <td class="col2" colspan="2" id="filterCity"></td>
        </tr>
        <tr>
            <td class="col1">Адрес заведения</td>
            <td class="col2" colspan="2"><input type="text" name="param7" value="<?php echo $this->item['param7']; ?>" /></td>
        </tr>
        <tr>
            <td class="col1">Сайт</td>
            <td class="col2" colspan="2"><input type="text" name="param8" value="<?php echo $this->item['param8']; ?>" /></td>
        </tr>
        <tr>
            <td>Дата рождения</td>
            <td class="col2" colspan="2">
                <select class="year" name="d_year">
                <?php $cTimestamp = !empty($this->item) ? mktime(0, 0, 0, substr($this->item['param9'], 6, 2), substr($this->item['param9'], 9, 2), substr($this->item['param9'], 0, 4)) : mktime(0, 0, 0, 1, 1, 1900); ?>
                <?php $cYear = date("Y", $cTimestamp); ?>
                <?php for ($y=1900; $y<=date("Y")-18; $y++): ?>
                    <?php if ($y == $cYear): ?>
                    <option value="<?php echo $y; ?>" selected="selected"><?php echo $y; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                    <?php endif; ?>
                <?php endfor; ?>
                </select><select class="month" name="d_month">
                <?php $cMonth = date("m", $cTimestamp); ?>
                <?php foreach($this->month as $month => $monthText): ?>
                    <?php if ($month == $cMonth): ?>
                    <option value="<?php echo $month; ?>" selected="selected"><?php echo $monthText ?></option>
                    <?php else: ?>
                    <option value="<?php echo $month; ?>"><?php echo $monthText ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
                </select><select class="day" name="d_day">
                <?php $cDay = date("d", $cTimestamp); ?>
                <?php for($d=1; $d<=31; $d++): ?>
                    <?php if ($d == $cDay): ?>
                    <option value="<?php echo $d; ?>" selected="selected"><?php echo $d; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $d; ?>"><?php echo $d; ?></option>
                    <?php endif; ?>
                <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="3"><a style="color:#4D321F" href="/user_agreement.stc" target="_blank">Пользовательское соглашение</a></td>
        </tr>
        <?php if (empty($this->item)): ?>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2" align="right"><input id="create_admin_form_submit" class="submit" style="width:200px;cursor:pointer;" type="submit" value="Зарегестрироваться" /></td>
        </tr>
        <?php else: ?>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2" align="right"><input id="create_admin_form_submit" class="submit" style="width:110px;cursor:pointer;" type="submit" value="Сохранить" /></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="3"><div class="stat" id="club_admin_stat"></div></td>
        </tr>
    </table>
    </form>
</div>
        <script>
            function genFilter(cityR)
            {
                if (typeof filter != 'undefined') {
                    var regionG = <?php echo (isset($this->item['param5']) && $this->item['param5'] != '') ? $this->item['param5'] : 0; ?>;
                    var cityG = <?php echo (isset($this->item['param6']) && $this->item['param5'] != '') ? $this->item['param6'] : 0; ?>;
                    var d = false;
                    if (!cityR) { var cityR = 0; }
                    else { d = true; }
                    if (!d) {
                        var o = '<option value="">Все</option>';
                        for (var k in filter) {
                            if (regionG != 0 && k == regionG || regionG == 0 && cityR == 0) {
                                cityR = k;
                            }
                            if (regionG != 0 && k == regionG) {
                                o += '<option value="' + k + '" selected="selected">' + filter[k]['title'] + '</option>';
                            } else {
                                o += '<option value="' + k + '">' + filter[k]['title'] + '</option>';
                            }                   
                        }
                        $("filterRegion").update('<select name="param5" onChange="genFilter(this.value);">' + o + '</select>');
                    }
                    var o = '<option value="">Все</option>';
                    for (var k in filter[cityR]['childs']) {
                        if (cityG != 0 && k == cityG) {
                            o += '<option value="' + k + '" selected="selected">' + filter[cityR]['childs'][k] + '</option>';
                        } else {
                            o += '<option value="' + k + '">' + filter[cityR]['childs'][k] + '</option>';
                        }                   
                    }
                    $("filterCity").update('<select name="param6">' + o + '</select>');
                }
            }
            genFilter();
        </script>
<script>
    function create(data, event)
    {
        if ($("introtext_parent")) {
        	tinyMCE.triggerSave();
        }
        event.stop();
        if (data.tagName.toUpperCase() == 'FORM'){
            var data = data.serialize(true);
        }
        data['userId'] = '<?php echo $this->item['id']; ?>';
        
        new Ajax.Request('/index.php', {
            method: 'post',
            parameters: data,
            encoding: 'UTF-8',
            onCreate : (function(){
                if (document.create_admin_form.email) {
                    document.create_admin_form.email.setStyle({outline:"none"});
                }
                if (document.create_admin_form.password) {
                    document.create_admin_form.password.setStyle({outline:"none"});
                }
                document.create_admin_form.param1.setStyle({outline:"none"});
                document.create_admin_form.param2.setStyle({outline:"none"});
                //document.create_admin_form.param3.setStyle({outline:"none"});
                document.create_admin_form.param4.setStyle({outline:"none"});
                document.create_admin_form.param5.setStyle({outline:"none"});
                document.create_admin_form.param6.setStyle({outline:"none"});
                document.create_admin_form.param7.setStyle({outline:"none"});
                document.create_admin_form.param8.setStyle({outline:"none"});
                document.create_admin_form.disable();
            }).bind(this),
            onComplete : (function(request){
                eval(request.responseText);
            }).bind(this)
        });
        return false;
    }   
</script>
