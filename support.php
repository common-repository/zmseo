<?php
if ( ! defined( 'ABSPATH' ) ) exit; #выход при прямом доступе

if ( is_user_logged_in() ) {
	
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$name_bd=$wpdb->prefix.'zmseo_support';
$my_data = $wpdb->get_results("SELECT * FROM ".$name_bd, ARRAY_A);

if (sizeof($my_data))
foreach ($my_data as $i => $zn)
{
if (!isset($tuning[$zn['name']])) $tuning[$zn['name']]=$zn['val_d'];
}

if (!$_POST)
{
$api_go['z']=1;
require( dirname( __FILE__ ) . '/sup_pages/api.php' );
}

### Наш токен
if (isset($_POST['save_token_1']) and wp_verify_nonce( $_POST['nonce'], $_SERVER['REQUEST_URI'])) 
{

$tuning['token_1'] = sanitize_post($_POST['token_1']);

$wpdb->update($name_bd, // указываем таблицу
	array('val_d' => $tuning['token_1']), // поменяем имя 
	array('id' => 2), // где 
	array( '%s' ), // формат для данных  %d-число, %s-строка
	array( '%d' )  // формат для где
);

$api_go['z']=2;
require( dirname( __FILE__ ) . '/sup_pages/api.php' );

if ($arr_api['data']['id_site'])
	{
		$wpdb->update($name_bd, // указываем таблицу
	array('val_d' => $arr_api['data']['id_site']), // поменяем имя 
	array('id' => 5), // где 
	array( '%d' ), // формат для данных  %d-число, %s-строка
	array( '%d' )  // формат для где
);
	}
}
###

### Ваш токен
if (isset($_POST['save_token_2']) and wp_verify_nonce( $_POST['nonce'], $_SERVER['REQUEST_URI'])) 
{
echo 'Сохранили токен нашего приложения';

$tuning['token_2'] = sanitize_post($_POST['token_2']);

$wpdb->update($name_bd, // указываем таблицу
	array('val_d' => $tuning['token_2']), // поменяем имя 
	array('id' => 3), // где 
	array( '%s' ), // формат для данных  %d-число, %s-строка
	array( '%d' )  // формат для где
);


$metrika_go['z']=1;
require( dirname( __FILE__ ) . '/sup_pages/metrika.php' );
}
###

### id_app
if (isset($_POST['save_id_app']) and $_POST['id_app'] and wp_verify_nonce( $_POST['nonce'], $_SERVER['REQUEST_URI'])) 
{
echo 'Сохранили токен нашего приложения';

$tuning['id_app'] = sanitize_post($_POST['id_app']);

$wpdb->update($name_bd, // указываем таблицу
	array('val_d' => $tuning['id_app']), // поменяем имя 
	array('id' => 4), // где 
	array( '%s' ), // формат для данных  %d-число, %s-строка
	array( '%d' )  // формат для где
);

}
###


### unid_key
if (isset($_POST['save_unid_key']) and $_POST['unid_key'] and wp_verify_nonce( $_POST['nonce'], $_SERVER['REQUEST_URI'])) 
{
echo 'Сохранили ключ активации '.$_POST['unid_key'].'<br>';

$tuning['unid_key'] = sanitize_post($_POST['unid_key']);

global $wpdb;
$rows = $wpdb->update($name_bd, // указываем таблицу
	array('val_d' => $tuning['unid_key']), // поменяем имя 
	array('id' => 1), // где 
	array( '%s' ), // формат для данных  %d-число, %s-строка
	array( '%d' )  // формат для где
);

$api_go['z']=1;
require( dirname( __FILE__ ) . '/sup_pages/api.php' );
}
###

### checkin
if ((isset($_POST['phone']) or isset($_POST['email'])) and wp_verify_nonce( $_POST['nonce'], $_SERVER['REQUEST_URI'])) 
{
$com='<p style="color:red;font-weight:bold;">Запрос на бесплатный ключ отправлен</p>';

$api_go['z']=1;
require( dirname( __FILE__ ) . '/sup_pages/api.php' );
}
###
if (isset($_POST['id_tag'])) require( dirname( __FILE__ ) . '/sup_pages/tags.php' );


#########################################################
if (!$tuning['good_pages']) echo '<p style="color:red"><b>Ошибка БД. Плагин не может быть настроен!</b></p>';
$nonce = wp_create_nonce($_SERVER['REQUEST_URI']);

	echo '
<script>
function change(idName) {
  if(document.getElementById(idName).style.display=="none") {
    document.getElementById(idName).style.display = "";
  } else {
    document.getElementById(idName).style.display = "none";
  }
  return false;
}
</script>

<h2>Настройки</h2>

	<div class="zmseo_tabs" style="max-width:100%">
	
    <input id="tab1" type="radio" name="zmseo_tabs" checked>
    <label for="tab1" title="Интеграция с Я.Метрикой ">Интеграция с Я.Метрикой</label>
 
    <input id="tab2" type="radio" name="zmseo_tabs">
    <label for="tab2" title="Активация">Активация</label>

    <input id="tab3" type="radio" name="zmseo_tabs">
    <label for="tab3" title="Создание Тегов">Создание Тегов</label>
';


echo '<section id="content-tab1">';
echo '<h2>Интеграция с Я.Метрикой</h2>';
echo '
<table border=0>
<tr>
<td valign="top" width="50%">
<h3>Вариант 1</h3>


<form enctype="multipart/form-data" method="post" action="https://oauth.yandex.ru/authorize?response_type=token&client_id=df160ef01379484bb86d8828fd674e92" target=_blank>
<button class="btn" type="submit" name="giveme_token" value="Запрос токена">Шаг 1. Получить токен</button><br>	
</form>

<br>

<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<input type="hidden" name="nonce" value="'.$nonce.'">
<input class="normal1" type="text" name="token_1" value="'.$tuning['token_1'].'" size="40"><br>
<button class="btn" type="submit" name="save_token_1" value="Сохранение токена">Шаг 2. Сохранить токен</button><br>	
</form>
<br>
<br>
<br>
Вариант 1 - Предоставить доступ zmseo.ru к своей метрике<br>
<br>
Вариант 2 - Связь плагина с метрикой напрямую<br>
(функционал может быть неполный)<br>
<br>
После интеграции с метрикой подождите 5-10 мин.
</td>
<td>
<h3>Вариант 2</h3>

<form enctype="multipart/form-data" method="get" action="https://oauth.yandex.ru/client/new" target=_blank>
<button class="btn" type="submit" name="" value="">Шаг 1. Создаем свое приложение</button><br>	
</form>
<p>
В открытом окне:<br>
1. в поле <b>Название приложения*</b> указываем  <b>Мой SEO Аналитик</b><br>
2. во вкладке <b>Яндекс.Метрика</b> ставим галочку напротив <b>Получение статистики, чтение параметров своих и доверенных счётчиков</b><br>
3. внизу страницы нажимаем <b>Сохранить приложение</b><br>
4. копируем <b>ID</b> и сохраняем в поле ниже<br>
<br>
P.S. При необходимости в поле <b>Callback URL</b> укажите: <b>'.$_SERVER['HTTP_HOST'].'</b> 
</p>

<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<input type="hidden" name="nonce" value="'.$nonce.'">
<input class="normal1" type="text" name="id_app" value="'.$tuning['id_app'].'" size="40" placeholder="ID вашего приложения"><br>
<button class="btn" type="submit" name="save_id_app" value="Добавить">Шаг 2. Сохранить ID</button><br>	
</form>

<br>

<form enctype="multipart/form-data" method="post" action="https://oauth.yandex.ru/authorize?response_type=token&client_id='.$tuning['id_app'].'" target=_blank>
<button class="btn" type="submit" name="giveme_token" value="Добавить">Шаг 3. Получить токен</button><br>	
</form>

<br>

<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<input type="hidden" name="nonce" value="'.$nonce.'">
<input class="normal1" type="text" name="token_2" value="'.$tuning['token_2'].'" size="40"><br>
<button class="btn" type="submit" name="save_token_2" value="Добавить">Шаг 4. Сохранить токен</button><br>	
</form>

</td>
</tr>
</table>
';
echo '</section>';



echo '<section id="content-tab2">';
echo '<h2>Активация</h2>'.$com.'<hr>';


if ($arr_api['info']['email']=='request')
{
echo '
<h3>Регистрация</h3>
<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<input type="hidden" name="nonce" value="'.$nonce.'">
<input class="normal1" type="text" name="name" value="" size="15" maxlength="18" required placeholder="Имя">
<input class="normal1" type="email" name="email" value="" size="15" maxlength="40" required placeholder="E-mail">
<button class="btn" type="submit" name="go_email" value="Отправить">Хочу полный функционал</button><br>	
</form>
<br>
Зарегистрируйтесь и получите бесплатно 3 дня для ознакомления с полным функционалом плагина.<br>
После регистрации вам придет письмо, где необходимо подтвердить свой E-mail.
<br><hr>
';
}

echo '<h3>Ключ активации</h3>';
if (isset($arr_api['info']['invite']))
{
	echo 'Ваш инвайт: <b>'.$arr_api['info']['invite'].'</b><br>
	При оплате плагина по вашему инвайту вы получаете дополнительно месяц доступа, а тот кто оплачивает 10% скидки.<br>
	Сам на себя дает скидку в 20%<br><br>';
}
if ($arr_api['info']['time_end']) $time_end=' (действует до: <b>'.date("d.m.Y",$arr_api['info']['time_end']).'</b>)';

echo '
Активация плагина'.$time_end.':<br>
<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<input type="hidden" name="nonce" value="'.$nonce.'">
<input class="normal1" type="text" name="unid_key" value="'.$tuning['unid_key'].'" size="40" required> 
<button class="btn" type="submit" name="save_unid_key" value="Добавить">Сохранить ключ активации</button><br>	
</form>
<br>
После оплаты ключ активации придет к вам на E-mail. 
<br><hr>
';


if ($arr_api['info']['phone']=='request_0')
{
echo '
<table>
<tr><td colspan="2">
<br><br>
Бесплатный ключ на 3 дня в смс можно получить оставив нам свой номер телефона<br>
<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<input type="hidden" name="nonce" value="'.$nonce.'">
<input class="normal1" type="text" name="phone" value="" size="15" maxlength="18" required placeholder="79139407113"><br>
<button class="btn" type="submit" name="go_phone" value="Отправить">Запросить бесплатный ключ</button><br>	
</form>
</td></tr>
</table>
';
}


echo '</section>';



echo '<section id="content-tab3">';
echo '<h2>Теги</h2>';
echo '
<p>
Тег - элемент позволяющий объединять несколько статей в группу.<br>
Максимально можно создать 5 тегов.
</p>

<table>
<table border=0>
';
for ($i=1;$i<6;$i++)
{
if (isset($tuning['tag_'.$i]) and $tuning['tag_'.$i]!='free')
	{
	$arr_tag=json_decode($tuning['tag_'.$i]);
	unset($sel);
	$sel['green']='';
	$sel['blue']='';
	$sel['red']='';
	$sel['yellow']='';
	$sel['black']='';
	$sel[$arr_tag->color_b]='selected';
	echo '
<tr>
<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<td style="background-color:'.$arr_tag->color_b.'; color:'.$arr_tag->color_t.';">
Тег '.$i.'
<select class="validate" name="color">
<option>Без цвета:</option>
<option '.$sel['green'].' value="1" style="background-color:green; color:white;">Зеленый</option>
<option '.$sel['blue'].' value="2" style="background-color:blue; color:white;">Синий</option>
<option '.$sel['red'].' value="3" style="background-color:red; color:white;">Красный</option>
<option '.$sel['yellow'].' value="4" style="background-color:yellow; color:black;">Желтый</option>
<option '.$sel['black'].' value="5" style="background-color:black; color:white;">Черный</option>
</select>
</td>
<td><input class="normal1" type="text" name="name_tag" value="'.$arr_tag->name_tag.'" size="10" maxlength="10" required></td>
<td><input class="normal1" type="text" name="dec_tag" value="'.$arr_tag->dec_tag.'" size="50" maxlength="50" required></td>
<td><button class="btn" type="submit" name="edit_tag" value="Отправить">Изменить</button></td>
<td><button class="btn" type="submit" name="delete_tag" value="Отправить">Удалить</button></td>
<input type="hidden" name="nonce" value="'.$nonce.'">
<input type="hidden" name="id_tag" value="'.$i.'">
</form>
</tr>
';
	}
else $free_tag[]=$i;	
}

if ($free_tag[0])
{
echo '
<tr>
<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<td>
Тег '.$free_tag[0].'
<select class="validate" name="color">
<option>Без цвета</option>
<option value="1" style="background-color:green; color:white;">Зеленый</option>
<option value="2" style="background-color:blue; color:white;">Синий</option>
<option value="3" style="background-color:red; color:white;">Красный</option>
<option value="4" style="background-color:yellow; color:black;">Желтый</option>
<option value="5" style="background-color:black; color:white;">Черный</option>
</select>
</td>
<td><input class="normal1" type="text" name="name_tag" value="" size="10" maxlength="10" required placeholder="Краткое описание"></td>
<td><input class="normal1" type="text" name="dec_tag" value="" size="50" maxlength="50" required placeholder="Полное описание"></td>
<td><button class="btn" type="submit" name="add_tag" value="Отправить">Добавить</button></td>
<td><button class="btn" type="submit" name="delete_tag" value="Отправить">Удалить</button></td>
<input type="hidden" name="nonce" value="'.$nonce.'">
<input type="hidden" name="id_tag" value="'.$free_tag[0].'">
</form>
</tr>
';
}
echo '
</table>
';

echo '
</section>
</div>';



}
?>