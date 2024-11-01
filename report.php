<?php
if ( ! defined( 'ABSPATH' ) ) exit; #выход при прямом доступе

if ( is_user_logged_in() ) {

global $wpdb;
$my_data = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zmseo_support", ARRAY_A);

if (sizeof($my_data))
foreach ($my_data as $i => $zn)
{
if (isset($zn['name']) || !$tuning[$zn['name']]) $tuning[$zn['name']]=$zn['val_d'];
}	
		
	if ($tuning['token_1'])
	{
		$api_go['z']=8;
		require( dirname( __FILE__ ) . '/sup_pages/api.php' );
		$mass_can=$arr_api['data']['report'];
		$arr_check=$arr_api['data']['report_2']['check'];
		#echo '<h3>***API***</h3>';
	}
### Перебор id всех статей
# Title и дата
$arr_tmp = $wpdb->get_results("SELECT ID, post_date, post_title FROM $wpdb->posts WHERE post_status = 'publish'");
if (sizeof($arr_tmp))
foreach( $arr_tmp as $i => $id_p ) {$title_arr[$id_p->ID]=$id_p->post_title; $date_arr[$id_p->ID]=$id_p->post_date;}
$myposts = new WP_Query( array( 'posts_per_page' => -1, 'cache_results' => false, 'fields' => 'ids', 'post_type' => 'any' ) );

if (sizeof($myposts->posts))
foreach( $myposts->posts as $i => $id_p )
{
	$url_p=get_permalink($id_p); 

	$link_p=str_replace('http://'.$_SERVER['HTTP_HOST'], '', $url_p);
	$link_p=str_replace('https://'.$_SERVER['HTTP_HOST'], '', $link_p);
	$link_p=str_replace('www.', '', $link_p);

	if (isset($title_arr[$id_p])) $mass_link[$link_p][0]=$title_arr[$id_p];
	$mass_link[$link_p][1]=$url_p;
	$mass_link[$link_p][2]=$_SERVER['PHP_SELF'].'?page=zmseo&page_id='.$id_p;
	$mass_link[$link_p][5]=$id_p;
	
	$tmp=date("Y",strtotime($date_arr[$id_p]));
	if ($tmp>date("Y")-5)
	{
		$arr_years[$tmp]++;
		$all_art++;
		$mass_link[$link_p][6]=$tmp;
	}

}
wp_reset_postdata();
###		

	
	
	
if (isset($_POST['yam'])) {
	$checked[3]='checked';
	$metrika_go['z']=4;
	require( dirname( __FILE__ ) . '/sup_pages/metrika.php' );
	
	foreach( $mass_link as $page => $arr )
	{	
		foreach( $mass_kpi_bd[$page] as $year => $kpi )
		{
			$arr_m[$arr[6]][$year]+=$kpi;
		}
	}
}
else $checked[1]='checked';	

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

<h2>Сводка</h2>

	<div class="zmseo_tabs" style="max-width:100%">
    <input id="tab1" type="radio" name="zmseo_tabs" '.$checked[1].'>
    <label for="tab1" title="Анализ страниц">Анализ страниц</label>
 
    <input id="tab2" type="radio" name="zmseo_tabs" '.$checked[2].'>
    <label for="tab2" title="KPI рубрик">KPI рубрик</label>
	
	<input id="tab3" type="radio" name="zmseo_tabs" '.$checked[3].'>
    <label for="tab3" title="KPI сайта">KPI сайта</label>
';	

echo '<section id="content-tab1">';
echo '<h2>Замечания</h2>';
if (isset($arr_check))
foreach ($arr_check[0] as $i_page => $zn)
{
	foreach ($zn as $i_ry => $zn2)
	{
		if (!isset($zmseo_arr_check_all[$i_ry])) $zmseo_arr_check_all[$i_ry]=array();
		foreach ($zn2 as $i_com => $yes)
		{
			$zmseo_arr_check[$i_ry][$i_com][]=$i_page;
			if (isset($i_ry) and  !in_array($i_page, $zmseo_arr_check_all[$i_ry])) 
				$zmseo_arr_check_all[$i_ry][]=$i_page;
		}
	}
}

if (isset($zmseo_arr_check['black']))
{
echo '	
<img src="'.plugin_dir_url( __FILE__ ).'images/black.png" width="20"> 
<a href="javascript:void(null);" onclick="change(\'black_tag\')" style="font-size: 16px;">Возможно страницы под фильтром ПС Яндекс ('.sizeof($zmseo_arr_check_all['black']).' стр.)</a>
<div id="black_tag" style="display:none;">
<ul style="padding: 0px 0px 0px 30px;">';
	foreach ($zmseo_arr_check['black'] as $i => $zn)
	{
		echo '<li>
		<img src="'.plugin_dir_url( __FILE__ ).'images/black_min.png" width="15">
		<a href="javascript:void(null);" onclick="change(\'black_tag_'.$i.'\')">
		'.$arr_check[1]['black'][$i].' ('.sizeof($zmseo_arr_check['black'][$i]).' стр.)
		</a>
		</li>';
		echo '<div id="black_tag_'.$i.'"  style="display:none;">
			<ul style="padding: 0px 0px 0px 30px;">';
			$n=0;
			foreach ($zn as $i2 => $zn2)
			{
				$n++;
				if (isset($mass_link[$zn2][5])) $nonce_id_post=wp_create_nonce($mass_link[$zn2][5]);
				
				if 	(isset($zn2) and isset($mass_link[$zn2][0]) and isset($mass_link[$zn2][2]) and isset($mass_link[$zn2][5]) and isset($arr_check[2][$zn2]))			
				echo '<li>'.$n.'. <a href="'.$mass_link[$zn2][2].'&nonce_id='.$nonce_id_post.'" target="_blank">Запросы и аналитика</a> | KPI: '.$arr_check[2][$zn2].' | <a href="/wp-admin/post.php?post='.$mass_link[$zn2][5].'&action=edit" target="_blank"><b>'.$mass_link[$zn2][0].'</b></a></li>';
				else 
				echo '<li>'.$n.'. Запросы и аналитика | KPI: '.$arr_check[2][$zn2].' | <b>'.$zn2.'</b></li>'; 
			}
			echo ' </ul>
			</div>';
	}

echo ' </ul>
</div>
<hr>
';
}


if (isset($zmseo_arr_check['red']))
{
echo '	
<img src="'.plugin_dir_url( __FILE__ ).'images/red.png" width="20"> 
<a href="javascript:void(null);" onclick="change(\'red_tag\')" style="font-size: 16px;">Допущены существенные ошибки ('.sizeof($zmseo_arr_check_all['red']).' стр.)</a>
<div id="red_tag" style="display:none;">
<ul style="padding: 0px 0px 0px 30px;">';
	foreach ($zmseo_arr_check['red'] as $i => $zn)
	{
		echo '<li>
		<img src="'.plugin_dir_url( __FILE__ ).'images/red_min.png" width="15">
		<a href="javascript:void(null);" onclick="change(\'red_tag_'.$i.'\')">
		'.$arr_check[1]['red'][$i].' ('.sizeof($zmseo_arr_check['red'][$i]).' стр.)
		</a>
		</li>';
			echo '<div id="red_tag_'.$i.'"  style="display:none;">
			<ul style="padding: 0px 0px 0px 30px;">';
			$n=0;
			foreach ($zn as $i2 => $zn2)
			{
				$n++;
				if (isset($mass_link[$zn2][5])) $nonce_id_post=wp_create_nonce($mass_link[$zn2][5]);
				
				if 	(isset($zn2) and isset($mass_link[$zn2][0]) and isset($mass_link[$zn2][2]) and isset($mass_link[$zn2][5]) and isset($arr_check[2][$zn2]))				
				echo '<li>'.$n.'. <a href="'.$mass_link[$zn2][2].'&nonce_id='.$nonce_id_post.'" target="_blank">Запросы и аналитика</a> | KPI: '.$arr_check[2][$zn2].' | <a href="/wp-admin/post.php?post='.$mass_link[$zn2][5].'&action=edit" target="_blank"><b>'.$mass_link[$zn2][0].'</b></a></li>';
				else 
				echo '<li>'.$n.'. Запросы и аналитика | KPI: '.$arr_check[2][$zn2].' | <b>'.$zn2.'</b></li>'; 
			}
			echo ' </ul>
			</div>';
	}

echo ' </ul>
</div>
<hr>
';
}

if (isset($zmseo_arr_check['yellow']))
{
echo '
<img src="'.plugin_dir_url( __FILE__ ).'/images/yellow.png" width="20"> 
<a href="javascript:void(null);" onclick="change(\'yellow_tag\')" style="font-size: 16px;">Есть над чем работать ('.sizeof($zmseo_arr_check_all['yellow']).' стр.)</a>
<div id="yellow_tag" style="display:none;">
<ul style="padding: 0px 0px 0px 30px;">';
	foreach ($zmseo_arr_check['yellow'] as $i => $zn)
	{
		echo '<li>
		<img src="'.plugin_dir_url( __FILE__ ).'images/yellow_min.png" width="15">
		<a href="javascript:void(null);" onclick="change(\'yellow_tag_'.$i.'\')">
		'.$arr_check[1]['yellow'][$i].'  ('.sizeof($zmseo_arr_check['yellow'][$i]).' стр.)
		</a>
		</li>';
			echo '<div id="yellow_tag_'.$i.'" style="display:none;">
			<ul style="padding: 0px 0px 0px 30px;">';
			$n=0;
			foreach ($zn as $i2 => $zn2)
			{
				$n++;	
				if (isset($mass_link[$zn2][5])) $nonce_id_post=wp_create_nonce($mass_link[$zn2][5]);	
				
				if 	(isset($zn2) and isset($mass_link[$zn2][0]) and isset($mass_link[$zn2][2]) and isset($mass_link[$zn2][5]) and isset($arr_check[2][$zn2]))
				echo '<li>'.$n.'. <a href="'.$mass_link[$zn2][2].'&nonce_id='.$nonce_id_post.'" target="_blank">Запросы и аналитика</a> | KPI: '.$arr_check[2][$zn2].' | <a href="/wp-admin/post.php?post='.$mass_link[$zn2][5].'&action=edit" target="_blank"><b>'.$mass_link[$zn2][0].'</b></a></li>';
				else 
				echo '<li>'.$n.'. Запросы и аналитика | KPI: '.$arr_check[2][$zn2].' | <b>'.$zn2.'</b></li>'; 
			}
			echo ' </ul>
			</div>';
	}

echo ' </ul>
</div>
<hr>
';
}

if ($arr_api['info']['active']!='yes') echo 'В бесплатной версии фоновая проверка страниц не производится';
echo '</section>';

echo '<section id="content-tab2">';
echo '<h2>Сводка</h2>';
echo '<div class="container">';
if (!$tuning['token_1']) echo '<h3>Функция сводка доступны только при подключении к метрике по варианту 1</h3>';	

echo '<table class="big-table" style="width:600px">';
echo '
<tr>
<td>Рубрика</td>
<td width="50">Количество страниц</td>
<td width="50">KPI рубрики</td>
<td width="50">KPI на страницу</td>
</tr>
';
if (isset($mass_can))
	foreach ($mass_can as $i => $zn)
	{
		$b[0]=''; $b[1]='';
		if ($zn[0]==1) {$b[0]='<b>'; $b[1]='</b>';}
		if ($zn[0]==5) {$b[0]='<img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="Без учета отдельных страниц и мелких рубрик"><b>'; $b[1]='</b>';}
echo '
<tr>
<td>'.$b[0].$zn[1].$b[1].'</td>
<td width="50">'.$zn[2].'</td>
<td width="50">'.$zn[3].'</td>
<td width="50">'.$zn[4].'</td>
</tr>
';

	}
echo '</table>';
echo '<p>Для определения среднего KPI в разделе должно быть более 5 станиц и суммарный KPI более 10 за прошлый месяц.</p>';	
echo '
</div>';
echo '</section>';

echo '<section id="content-tab3">';
echo '<h2>KPI сайта</h2>';

if ($arr_api['info']['active']!='yes') echo 'В бесплатной версии фоновая проверка страниц не производится';
else {
	$n_year1=date("Y")-4;
	$n_year2=date("Y")-3;
	$n_year3=date("Y")-2;
	$n_year4=date("Y")-1;
	$n_year5=date("Y");
	$n_year6=date("Y")+1;	
	ksort($arr_years);
	
echo '<table class="big-table" style="width:600px">';
echo '
<tr>
<td>Год публикации</td>
<td width="50">Количество публикаций</td>
<td width="150">% от общего количества.</td>
<td width="150">KPI за '.$n_year1.'</td>
<td width="150">KPI за '.$n_year2.'</td>
<td width="150">KPI за '.$n_year3.'</td>
<td width="150">KPI за '.$n_year4.'</td>
<td width="150">Прогноз KPI на '.$n_year5.'</td>
</tr>
';	
	foreach ($arr_years as $i => $zn)
	{
	$prc_y=round($zn*100/$all_art,2);
	$prognoz=round($arr_m[$i][$n_year6]/$arr_m[$i][$n_year5]*$arr_m[$i][$n_year4]/$zn,2); 
echo '
<tr>
<td>'.$i.'</td>
<td>'.$zn.'</td>
<td>'.$prc_y.'%</td>
<td>'.round($arr_m[$i][$n_year1]/$zn,2).'</td>
<td>'.round($arr_m[$i][$n_year2]/$zn,2).'</td>
<td>'.round($arr_m[$i][$n_year3]/$zn,2).'</td>
<td>'.round($arr_m[$i][$n_year4]/$zn,2).'</td>
<td><b>'.$prognoz.'</b></td>

</tr>
';
	}	
echo '</table>';

		echo '
		<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
		<button class="btn" type="submit" name="yam" value="Получить данные из Я.Метрики">Получить данные из Я.Метрики</button></td>
		</form>
		';
}
echo '</section>';
echo '</div>';


}
?>