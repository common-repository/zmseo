<?php
if ( ! defined( 'ABSPATH' ) ) exit; #выход при прямом доступе

if ( is_user_logged_in() ) {

### чтение выбранной страницы
global $post;
if (wp_verify_nonce( $_GET['nonce_id'], $_GET['page_id'])) 
	{
		$post = get_post( (int)$_GET['page_id'] );
		setup_postdata($post);
	}
else 
	{
	echo '<p>Не получен id страницы</p>';
	exit;
	}

$tag = get_post_meta( $post->ID, '_zmseo_tags', true );
$nonce_id = wp_create_nonce($post->ID);
###

### чтение настоек
global $wpdb;
$my_data = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zmseo_support", ARRAY_A);

if (sizeof($my_data))
foreach ($my_data as $i => $zn)
{
if (!isset($tuning[$zn['name']])) $tuning[$zn['name']]=$zn['val_d'];
}
###

### Запрос ключей и результата анализа страницы
$api_go['z']=4;
$zmseo_keys = get_post_meta( $post->ID, '_zmseo_keys', true );
require( dirname( __FILE__ ) . '/sup_pages/api.php' );
$arr_m_day=$arr_api['data']['arr_m_day'];
###

### не получили $arr_m_day, создаем сами
if (!$arr_m_day) 
{
		for ($i_month=1;$i_month<7;$i_month++)	
		{
		$t=date("Y").'-'.date("m").'-01 -'.$i_month.' month';
		$arr_m_day[]=date( "Y-m", strtotime($t) );
		}
}
###

### Запрос к метрики, если подключен вариант 2
if ($tuning['token_2'] and !$arr_api['data']['kpi'])
{
$metrika_go['z']=3;
require( dirname( __FILE__ ) . '/sup_pages/metrika.php' );
$arr_api['data']['kpi']=$kpi_page;
}
###

###Работа с тегами
if (isset($_POST['tag']) or isset($_POST['color'])) require( dirname( __FILE__ ) . '/sup_pages/tags.php' );

$z_tag='
<select class="validate" name="color">
<option>Без тега</option>
';
$sel_tag=json_decode($tag);
$tags_page='';
for ($i=1;$i<6;$i++)
{
	$tmp='tag_'.$i;
	if (isset($sel_tag->$tmp) and $sel_tag->$tmp==1) $sel='checked';
	else $sel='';
if (isset($tuning['tag_'.$i]) and $tuning['tag_'.$i]!='free')
	{

		$arr_tags[$i]=json_decode($tuning['tag_'.$i]);
		$z_tag.='<option value="tag_'.$arr_tags[$i]->id.'" style="background-color:'.$arr_tags[$i]->color_b.'; color:'.$arr_tags[$i]->color_t.';">'.$arr_tags[$i]->name_tag.'</option>
		';
		$tags_page.='
		<div class="menutags__item_'.$arr_tags[$i]->color_b.'">
		<input type="checkbox" name="tag_'.$i.'" value="1" '.$sel.' style="border-radius: 0.8em;"> '.$arr_tags[$i]->name_tag.'
		</div>
		';
	}
}
$z_tag.='</select>';

if (!$tags_page) $tags_page='<p>Тегов нет. Создать тег можно во вкладке "Настройки".</p>';
else $tags_page.='<button class="btn" type="submit" name="tag" value="tag">Присвоить</button>';
$nonce = wp_create_nonce('change_new');
###

echo '<h2>Запросы</h2>';

echo '<div class="container">
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
';

$seo=$arr_api['data']['seo'];
if (isset($arr_api['data']['words']))
{
$table='
<table class="big-table">
<tr>
<td width="75">[!Частота]</td>
<td width="500">Запрос</td>
<td width="50">Позиции</td>
<td width="50">Трафик 12мес.</td>
<td width="50">Отказы</td>
</tr>
';
}

if (isset($arr_api['data']['words']))
foreach ($arr_api['data']['words'] as $i => $zn)
{
	if (!isset($z)) $z=0;
	$z++;
	if (!isset($sz)) $sz=0;
	if (isset($zn[2])) $sz+=(int)$zn[2];


if (isset($zn['metrika']))
{
$traffic=$zn['metrika'][0];
$waivers=round($zn['metrika'][1],2).'%';
}
else {$traffic='-'; $waivers='-';}

$table.='	
<tr>
<td>'.$zn[2].'</td>
<td><img src="'.plugin_dir_url( __FILE__ ).'images/'.$seo[$zn[0]][1].'_min.png" width="10" title="'.$seo[$zn[0]][2].'">  '.$zn[0].'</td>
<td>'.$zn[1].'</td>
<td>'.$traffic.'</td>
<td>'.$waivers.'</td>	
</tr>

';	
	
if ($i==10 and sizeof($arr_api['data']['words'])>10)	$table.='
<tr>
<td colspan="2">
<a href="javascript:void(null);" onclick="change(\'more_word\')">Развернуть ('.sizeof($arr_api['data']['words']).')</a>
</td>		
</tr>
</table>

<div id="more_word" style="display:none;">
<table class="big-table">
<tr>
<td width="75">[!Частота]</td>
<td width="500">Запрос</td>
<td width="50">Позиции</td>
<td width="50">Трафик 12мес.</td>
<td width="50">Отказы</td> 
</tr>
';	
}


if (isset($arr_api['data']['words']) and sizeof($arr_api['data']['words'])>0)
{
		$table.='</table>';
			if (sizeof($arr_api['data']['words'])>10)
		$table.='</div>';
}

if (!$z) {$z='0 - еще не найдены'; $sz=0;}


echo '
<h3>Страница: '.$post->post_title.'</h3>
<p><a href="'.get_permalink($post->ID).'" target="_blank">URL</a> | <a href="/wp-admin/post.php?post='.$post->ID.'&action=edit" target="_blank">Редактировать</a> | KPI: <b>'.$arr_api['data']['kpi'].'</b> | Запросов: <b>'.$z.'</b> | [!суммарная !частота]: <b>'.$sz.'</b> | Ядро: <b>'.$seo['kf_art'].'</b>
';

echo ' 
<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<div class="menutags">
'.$tags_page.'
</div>
<input type="hidden" name="nonce" value="'.$nonce.'">	
</form>
</p>
';

echo $table;


echo '
<table>
<tr><td style="vertical-align: top; width:33%;">';
echo '<img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="Слова, встречающиеся в запросах, но почти или полностью отсутствующие в тексте"><b>Доп.Слова:</b><br>';
if (isset($arr_api['data']['dop_key']))
foreach ($arr_api['data']['dop_key'] as $i => $zn)
{
	echo $zn[0].': в '.$zn[1].' запросах, '.$zn[2].' шт. на странице<br>';
}
echo '</td><td style="vertical-align: top; width:33%;">';
echo '<img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="Тор-10 слов наиболее часто встречающихся в тесте"><b>Плотность:</b><br>';
if (isset($arr_api['data']['spam_words']))
foreach ($arr_api['data']['spam_words'] as $i => $zn2)
{
	echo $zn2[0].' | '.$zn2[1].'% - '.$zn2[2].' шт.<br>';
}
echo '</td><td style="vertical-align: top; width:33%;">';
echo '<img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="Cловосочетания повторяющиеся 2 и более раз"><b>N-граммы:</b><br>';
if (isset($arr_api['data']['spam_fraz']))
foreach ($arr_api['data']['spam_fraz'] as $i => $zn)
{
	echo $zn[0].'% | '.$zn[1].' - '.$zn[2].' шт. на странице<br>';
}
echo '
</td></tr>
</table>';

############################
if (isset($arr_api['data']['tav']) and sizeof($arr_api['data']['tav'])>1)
{	
echo '
<hr>
<h3>Тавтология</h3>
<a href="javascript:void(null);" onclick="change(\'tav\')">Показать ('.sizeof($arr_api['data']['tav']).')</a>
<div id="tav" style="display:none;">';
foreach ($arr_api['data']['tav'] as $i => $zn)
{
	$n++;
	echo $n.'. '.$zn.' <br>';
}
echo '</div>';
}
############################


############################
echo '
<hr>
<h3>История изменений</h3>

<table class="big-table">
<tr>
<td width="100">Редакция</td>
<td width="500">
Изменения
<a href="javascript:void(null);" onclick="change(\'change_new\')">Добавить</a>
<div id="change_new" style="display:none;">
<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<textarea name="text_change" rows="2" cols="50" placeholder="Максимум 200 символов" maxlength="200"></textarea><br>
Присвоить тег: '.$z_tag.'<br>
<button class="btn" type="submit" name="change_new" value="change_new">Сохранить</button><br>
<input type="hidden" name="nonce" value="'.$nonce.'">	
</form>
</div>
</td>
';
foreach ($arr_m_day as $i => $zn)
{
echo '<td class="center">'.$zn.' KPI</td>
';
}
echo '
</tr>
';

foreach ($arr_api['data']['changes'] as $i => $zn)
{
	
	
if (date("d.m.Y",$zn[1])==date("d.m.Y"))
	{
		$nonce = wp_create_nonce('change_'.$zn[0]);
		$edit_com='
<a href="javascript:void(null);" onclick="change(\'change_'.$zn[0].'\')">Редактировать</a>
<div id="change_'.$zn[0].'" style="display:none;">
<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
<textarea name="text_change" rows="2" cols="50">'.$zn[3].'</textarea><br>
Присвоить тег: '.$z_tag.'<br>
<button class="btn" type="submit" name="save_up" value="change_'.$zn[0].'">Сохранить</button><br>	
<input type="hidden" name="nonce" value="'.$nonce.'">
</form>
</div>';
	}
else $edit_com='';	
	
echo '
<tr>
<td>'.date("d.m.Y",$zn[1]).'</td>
<td>'.$zn[3].' '.$edit_com.'</td>
';
$go=0;

	foreach ($arr_m_day as $i2 => $zn2)
	{
		if (!isset($last_d) and strtotime(date("Y-m",$zn[1]))<=strtotime($zn2)) $go=1;
		else {$go=0; $kpi_m='';}
		
		if (isset($last_d) and $last_d>=strtotime($zn2) and strtotime(date("Y-m",$zn[1]))<=strtotime($zn2)) $go=1;
			
		if ($go or sizeof($arr_api['data']['changes'])==1) $kpi_m=$arr_api['data']['kpi_m'][$i2];
		echo '<td class="center">'.$kpi_m.'</td>
		';
	}
echo '
</tr>
';
$last_d=strtotime(date("d.m.Y",$zn[1]));
}
echo '
</table>
';
#################################

echo '
</div>';
}
?>