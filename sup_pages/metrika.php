<?php
if ( ! defined( 'ABSPATH' ) ) exit; #выход при прямом доступе

if ( is_user_logged_in() ) {
	
global $wpdb;
$my_data = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zmseo_support", ARRAY_A);

if (sizeof($my_data))
foreach ($my_data as $i => $zn)
{
if (!$tuning[$zn['name']]) $tuning[$zn['name']]=$zn['val_d'];
}

$url_pro=$_SERVER['HTTP_HOST'];





### Запрос id счетчика
if ($metrika_go['z']==1)
{

if ($url_pro)
{
$token=$tuning['token_2'];
###----Получаем id счетчика-----------------------------------------------------------------
if ($token)
{
$metrika_url='https://api-metrika.yandex.ru/management/v1/counters';
$array=metrika_api($token, $metrika_url);

for ($i=0;$i<sizeof($array->counters);$i++) #перебор счетчиков
{
if ($url_pro==$array->counters[$i]->site)	{$id_site=$array->counters[$i]->id; break;}
}

if ($id_site) 
	{
		echo '<br>Получили id счетчика - '.$id_site.'<br>';
		$wpdb->update( 
	$wpdb->prefix . 'zmseo_support', // указываем таблицу
	array('val_d' => $id_site), // поменяем имя 
	array('id' => 5), // где 
	array( '%d' ), // формат для данных  %d-число, %s-строка
	array( '%d' )  // формат для где
);

	}
else echo '<p style="color:red;"><b>!!! id счетчика - '.$url_pro.' не получен</b></p>';	
}
else echo '<p style="color:red;"><b>!!! Токен не получен</b></p>';
}
else echo '<p style="color:red;"><b>!!! Не задан URL проекта</b></p>';
}
###




###
if ($metrika_go['z']==2 or $metrika_go['z']==3)
{
$token=$tuning['token_2'];	
$id_site=$tuning['id_site'];
if ($metrika_go['z']==3) {$lim=2; $link_page=get_permalink( $my_post->ID );} 
else $lim=7;

if (!$_GET['g_page']) $fil='&filters=ym:s:users>'.$g_pages[0];
if ($_GET['g_page']==1) $fil='&filters=ym:s:users>'.$g_pages[1].'%20AND%20ym:s:users<'.$g_pages[0];
if ($_GET['g_page']==2) $fil='&filters=ym:s:users>'.$g_pages[2].'%20AND%20ym:s:users<'.$g_pages[1];
if ($_GET['g_page']==3) $fil='&filters=ym:s:users<'.$g_pages[2];
	
	if ($id_site and $token)
	{
		
for ($i_month=1;$i_month<$lim;$i_month++)	
{
$t=date("Y").'-'.date("m").'-01 -'.$i_month.' month';
$s_day=date( "Y-m-d", strtotime($t) );	
$f_day=date( "Y-m", strtotime($t) ).'-'.date("t", strtotime($t));
$m_day=date( "Y-m", strtotime($t) );
$arr_m_day[]=$m_day;
$time_ot='&date1='.$s_day.'&date2='.$f_day;
$t_m=date( "Ym", strtotime($t) );
$dop='&limit=1000&group=month';

$metrika_url='https://api-metrika.yandex.ru/stat/v1/data?preset=content_entrance&ids='.$id_site.$time_ot.$dop.$filters;
$array=metrika_api($token, $metrika_url);

if (sizeof($array->data)<1) {echo 'Нет связи с метрикой / или ни чего не нашли'; 	break;}


unset($mass_p);
$n=0;
	if (sizeof($array->data))
	foreach ($array->data as $i => $zn)
	{
	$n++; 
	$kpi_m=$array->data[$i]->metrics[1];
	$kpi_p=$array->data[$i]->dimensions[4]->name;
	$vh=strpos($kpi_p, $url_pro);
	$s1=strpos($kpi_p, ' ');
	$s2=strpos($kpi_p, '#');
	if (!$kpi_m) $kpi_m=0;
	
		if ($metrika_go['z']==3 and $link_page==$kpi_p) $kpi_page=$kpi_m;

	$kpi_p=str_replace('www', '', $kpi_p);
	$kpi_p=str_replace('http://'.$url_pro, '', $kpi_p);
	$kpi_p=str_replace('https://'.$url_pro, '', $kpi_p);
	
	if ($vh and $vh<10 and !$s1 and !$s2 and !in_array($kpi_p, $mass_p)) # долой мусор, только страницы сайта
		{
		if ($metrika_go['z']==2) {$mass_kpi[$kpi_p][$m_day]=$kpi_m; $mass_p[]=$kpi_p;}
		}
	}	
#	echo 'Обход '.$i_month.', страниц '.$n.'<br>';
}

	}
	else echo 'Не задан токен или не определен id счетчика';
	
}
###




###
if ($metrika_go['z']==4)
{
if (isset($tuning['token_2'])) $token=$tuning['token_2'];	
if (isset($tuning['token_1'])) $token=$tuning['token_1'];
if (isset($tuning['token_1'])) $id_site=$tuning['id_site'];
if (isset($arr_api['info']['id_site'])) $id_site=$arr_api['info']['id_site'];
$url_site=$_SERVER['HTTP_HOST'];

$time_s=time();
$i_year=date("Y")-4;
$f_year=date("Y")+1;

for ($i_year;$i_year<=$f_year;$i_year++)
{

#задаем начало и конец для запроса в метрику
if ($i_year==date("Y")) {$t1=$i_year.'-01-01'; $t2=date("Y-m-d");}
if ($i_year<date("Y")) {$t1=$i_year.'-01-01'; $t2=$i_year.'-12-31';}
if ($i_year>date("Y")) {$i_tmp=$i_year-2; $t1=$i_tmp.'-01-01'; $t2=$i_tmp.'-'.date("m-d");}

$metrika_url='https://api-metrika.yandex.ru/stat/v1/data?preset=content_entrance&ids='.$id_site;
$metrika_url.='&date1='.$t1.'&date2='.$t2;
$metrika_url.='&limit=10000&group=year';
$metrika_url.='&accuracy=0.5';

$array=metrika_api($token, $metrika_url);

foreach ($array->data as $i => $zn)
	{
	$n++; 
	$kpi_m=$array->data[$i]->metrics[1];
	$kpi_p=$array->data[$i]->dimensions[4]->name;
	$url_site=str_replace('www.', '', $url_site);
	$vh=strpos($kpi_p, $url_site);
	$s1=strpos($kpi_p, ' ');
	$s2=strpos($kpi_p, '#');
	
	$kpi_p=str_replace('www.', '', $kpi_p);
	$kpi_p=str_replace('http://'.$url_site, '', $kpi_p);
	$kpi_p=str_replace('https://'.$url_site, '', $kpi_p);
	$pos = strpos($kpi_p, '.html');
	if ($pos) $kpi_p=substr($kpi_p, 0, $pos+5);
	#Дзен
	$kpi_p=str_replace('?utm_referrer=https:%2F%2Fzen.yandex.com', '', $kpi_p);
	$kpi_p=str_replace('?utm_referrer=https://zen.yandex.com', '', $kpi_p);
	$kpi_p=str_replace('?utm_referrer=https', '', $kpi_p);
	$kpi_p=str_replace('%2F%3Ffrom%3Dsearchapp', '', $kpi_p);
	#
	$kpi_p=trim($kpi_p);

	
	if ($vh and $vh<10 and !$s1 and !$s2 and in_array($kpi_p, $mass_p_tmp)) $mass_kpi_bd[$kpi_p][$i_year]+=round($kpi_m/12); # добавка, дзен
	if ($vh and $vh<10 and !$s1 and !$s2 and !in_array($kpi_p, $mass_p_tmp)) # долой мусор, только страницы сайта
		{
			$mass_kpi_bd[$kpi_p][$i_year]=round($kpi_m/12);
			$mass_p_tmp[]=$kpi_p;	
		}
	}
}

}
###




}
?>