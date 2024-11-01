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
$sel_sup=json_decode($tuning['sup_post']);
# TH
if ($sel_sup->title) $zm_title = get_post_meta( $post->ID, $sel_sup->title, true );
if (!$sel_sup->title) $zm_title = $post->post_title;
if ($sel_sup->h1) $zm_h1 = get_post_meta( $post->ID, $sel_sup->h1, true );
#
###

### Запрос массива перелинковки
	if ($tuning['token_1'])
	{
		$api_go['z']=10;
		require( dirname( __FILE__ ) . '/sup_pages/api.php' );
		$mass_l=$arr_api['data']['links'];
		#echo '<h3>***API***</h3>';
	}
	else echo '<p>Перелинковка может быть не полной при подключении к метрике по варианту 2</p>';
	if (!$mass_l[6]['status']) echo '<p>Анкор лист в данной версии не доступен</p>';
###
### Перебор id всех статей

# Title
$arr_tmp = $wpdb->get_results("SELECT ID, post_date, post_title FROM $wpdb->posts WHERE post_status = 'publish'");
if (sizeof($arr_tmp))
foreach( $arr_tmp as $i => $id_p ) {$title_arr[$id_p->ID]=$id_p->post_title; $date_arr[$id_p->ID]=$id_p->post_date;}
#
$myposts = new WP_Query( array( 'posts_per_page' => -1, 'cache_results' => false, 'fields' => 'ids', 'post_type' => 'any' ) );
#
if (sizeof($myposts->posts))
foreach( $myposts->posts as $i => $id_p )
{
	$url_p=get_permalink($id_p); 

	$link_p=str_replace('http://'.$_SERVER['HTTP_HOST'], '', $url_p);
	$link_p=str_replace('https://'.$_SERVER['HTTP_HOST'], '', $link_p);
	$link_p=str_replace('www.', '', $link_p);

	if (isset($title_arr[$id_p]))
	$mass_link[$link_p][0]=$title_arr[$id_p];
	$mass_link[$link_p][1]=$url_p;
	$mass_link[$link_p][2]=$_SERVER['PHP_SELF'].'?page=zmseo&page_id='.$id_p;
	$mass_link[$link_p][5]=$id_p;
}
wp_reset_postdata();
###
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
';

echo '<h2>Cсылки</h2>';
echo '<b>URL: </b> '.get_permalink().'<br>';
echo '<b>Страница: </b> '.$zm_title.'<br><br>';
echo '
	<div class="zmseo_tabs" style="max-width:100%">
    <input id="tab1" type="radio" name="zmseo_tabs" checked>
    <label for="tab1" title="Входящие ссылки">Входящие ссылки - '.$mass_l[0].' шт.</label>
 
    <input id="tab2" type="radio" name="zmseo_tabs">
    <label for="tab2" title="Исходящие ссылки">Исходящие ссылки - '.$mass_l[1].' шт.</label>
';	

echo '<section id="content-tab1">';
echo '<h2>Анкоры</h2>';

if (isset($mass_l[6]['skv']) and $mass_l[6]['skv']=='yes') echo 'На страницу ведут сквозные ссылки. Количество URL ограничего.<br><br>';

if (isset($mass_l[2]))
foreach ($mass_l[2] as $ankor => $zn_z)
{
	if (!isset($l)) $l=0;
	$l++;
	if (!$ankor) $ankor='*img - ссылка картинкой';
	echo '
	<img src="'.plugin_dir_url( __FILE__ ).'images/down.png" width="20"> 
	<a href="javascript:void(null);" onclick="change(\'ank_tag_'.$l.'\')" style="font-size: 16px;">'.$ankor.'</a>  ('.sizeof($zn_z).' шт.)
	<div id="ank_tag_'.$l.'" style="display:none;">
	<ul style="padding: 0px 0px 0px 30px;">
	';
$n=0;
foreach ($zn_z as $i_z2 => $zn_z2)
{
$n++;	
if (isset($mass_link[$zn_z2][0]))
{
	$kpi_p='KPI: '.$mass_l[4][$zn_z2];
	$url_p='<a href="'.$mass_link[$zn_z2][1].'" title="'.$mass_link[$zn_z2][1].'" target="_blank">URL</a>';
	$name_p='<a href="/wp-admin/post.php?post='.$mass_link[$zn_z2][5].'&action=edit" target="_blank">'.$mass_link[$zn_z2][0].'</a>';
}
else {$kpi_p='KPI: -'; $url_p='<a href="'.$zn_z2.'" title="'.$zn_z2.'" target="_blank">URL</a>'; $name_p=$zn_z2;}
echo '<li>
<div style="overflow: hidden;">
	<div style="width: 100%;">
		<div style="float: left; width: 30px;">'.$n.'.</div>
		<div style="float: left; width: 60px;">'.$kpi_p.'</div>
		<div style="float: left; width: 30px;">'.$url_p.'</div>
		<div style="float: left;">'.$name_p.'</div>
    </div>
</div>
</li>
';	
}
echo '</ul>
</div>
<hr>
';
}

echo '</section>';
echo '<section id="content-tab2">';

echo '<h2>Анкоры</h2>';

if (isset($mass_l[3]))
foreach ($mass_l[3] as $ankor => $zn_z)
{
	$l++;
	if (!$ankor) $ankor='*img - ссылка картинкой';
	echo '
	<img src="'.plugin_dir_url( __FILE__ ).'images/up.png" width="20"> 
	<a href="javascript:void(null);" onclick="change(\'ank_tag_'.$l.'\')" style="font-size: 16px;">'.$ankor.'</a>  ('.sizeof($zn_z).' шт.)
	<div id="ank_tag_'.$l.'" style="display:none;">
	<ul style="padding: 0px 0px 0px 30px;">
	';
$n=0;
foreach ($zn_z as $i_z2 => $zn_z2)
{
$n++;	
if (isset($mass_link[$zn_z2][0]))
{
	$kpi_p='KPI: '.$mass_l[5][$zn_z2];
	$url_p='<a href="'.$mass_link[$zn_z2][1].'" title="'.$mass_link[$zn_z2][1].'" target="_blank">URL</a>';
	$name_p='<a href="/wp-admin/post.php?post='.$mass_link[$zn_z2][5].'&action=edit" target="_blank">'.$mass_link[$zn_z2][0].'</a>';
}
else {$kpi_p='KPI: -'; $url_p='<a href="'.$zn_z2.'" title="'.$zn_z2.'" target="_blank">URL</a>'; $name_p=$zn_z2;}

echo '<li>
<div style="overflow: hidden;">
	<div style="width: 100%;">
		<div style="float: left; width: 20px;">'.$n.'.</div>
		<div style="float: left; width: 50px;">'.$kpi_p.'</div>
		<div style="float: left; width: 30px;">'.$url_p.'</div>
		<div style="float: left;">'.$name_p.'</div>
    </div>
</div>
</li>
';	
}
echo '</ul>
</div>
<hr>
';
}

echo '</section><hr>';

if ($mass_l[6]['status']=='youcan') 
	{
		echo '
		<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'">
		<button class="btn" type="submit" name="doit" value="Выполнить переобход">Выполнить переобход</button></td>
		<input type="hidden" name="up_links" value="go">
		</form>
		<p>'.$mass_l[6]['desc'].'<p>
		';
	}
else echo '<p>'.$mass_l[6]['desc'].'<p>';
echo '</div>';
}
?>