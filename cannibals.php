<?php
if ( ! defined( 'ABSPATH' ) ) exit; #выход при прямом доступе

if ( is_user_logged_in() ) {

global $wpdb;
$my_data = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zmseo_support", ARRAY_A);

$tuning=array();
if (sizeof($my_data))
foreach ($my_data as $i => $zn)
{
if (!isset($tuning[$zn['name']])) $tuning[$zn['name']]=$zn['val_d'];
}	
		
	if ($tuning['token_1'])
	{
		$api_go['z']=6;
		require( dirname( __FILE__ ) . '/sup_pages/api.php' );
		if (isset($arr_api['data']['cannibals'])) $mass_can=$arr_api['data']['cannibals'];
		if (isset($arr_api['data']['year_cannibals'])) $mass_can_y=$arr_api['data']['year_cannibals'];
		#echo '<h3>***API***</h3>';
	}


echo '<h2>Каннибалы</h2>';
echo '<div class="container">';
if (!$tuning['token_1']) echo '<h3>Функция каннибалы доступны только при подключении к метрике по варианту 1</h3>';	
echo '<p>Данные обновляются ежедневно и собираются за последние 30 дней</p>';
if ($arr_api['data']['cannibal']=='no') echo '<h3>За последнии 30 дней каннибалы не обнаружены</h3><hr>';	

	if (isset($mass_can))
{
$myposts = new WP_Query( array( 'posts_per_page' => -1, 'cache_results' => false, 'fields' => 'ids' ) );

if (sizeof($myposts->posts))
foreach( $myposts->posts as $i => $id_p )
	{
		setup_postdata($id_p);
		$nonce_id_post = wp_create_nonce($id_p);
		$url_p=get_permalink($id_p);

		$mass_link[$url_p][0]=get_the_title($id_p);
		$mass_link[$url_p][1]=$_SERVER['PHP_SELF'].'?page=zmseo&page_id='.$id_p.'&nonce_id='.$nonce_id_post;
		$mass_link[$url_p][2]=$id_p;
	}
}	
	
### месяц	
if (isset($mass_can))
foreach ($mass_can as $i_z => $zn_z)
{
	
echo '<table class="big-table">';
echo '
<tr>
<td><b>'.$i_z.'</b></td>
<td width="50">Посетители</td>
<td width="50">Отказы</td>
<td width="50">Глубина просмотра</td>
<td width="50">Время на сайте</td>
</tr>
';
	if (sizeof($zn_z['url']))
	foreach ($zn_z['url'] as $i_p => $zn_p)
	{
if ($mass_link[$zn_p][1]) $lin_p='<a href="'.$mass_link[$zn_p][1].'" target="_blank">Запросы и аналитика</a> | <a href="/wp-admin/post.php?post='.$mass_link[$zn_p][2].'&action=edit" target="_blank">Редактировать</a>';
else $lin_p='';
	
echo '<tr><td>'.$mass_link[$zn_p][0].'<br>'.substr($zn_p, 0, 80).' '.$lin_p.'</td>
<td>'.$zn_z['data'][$i_p][0].'</td><td>'.$zn_z['data'][$i_p][1].'%</td><td>'.$zn_z['data'][$i_p][2].'</td><td>'.$zn_z['data'][$i_p][3].'</td></tr>';
	}
echo '</table>';
	
}
###
### Год
if (isset($mass_can_y))
{
echo '<h3>Каннибалы за год</h3>';
foreach ($mass_can_y as $i_z => $zn_z)
{
		if (!isset($n)) $n=0;
		$n++;	
echo '<table class="big-table">';
echo '
<tr>
<td>'.$n.'. <b>'.$i_z.'</b></td>
<td width="50">Посетители</td>
<td width="50">Отказы</td>
<td width="50">Глубина просмотра</td>
<td width="50">Время на сайте</td>
</tr>
';
	if (sizeof($zn_z['url']))
	foreach ($zn_z['url'] as $i_p => $zn_p)
	{
if ($mass_link[$zn_p][1]) $lin_p='<a href="'.$mass_link[$zn_p][1].'" target="_blank">Запросы и аналитика</a> | <a href="/wp-admin/post.php?post='.$mass_link[$zn_p][2].'&action=edit" target="_blank">Редактировать</a>';
else $lin_p='';
	
echo '<tr><td>'.$mass_link[$zn_p][0].'<br>'.substr($zn_p, 0, 80).' '.$lin_p.'</td>
<td>'.$zn_z['data'][$i_p][0].'</td><td>'.$zn_z['data'][$i_p][1].'%</td><td>'.$zn_z['data'][$i_p][2].'</td><td>'.$zn_z['data'][$i_p][3].'</td></tr>';
	}
echo '</table>';
}	
}
###

echo '
</div>';
}
?>