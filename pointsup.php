<?php
if ( ! defined( 'ABSPATH' ) ) exit; #выход при прямом доступе

if ( is_user_logged_in() ) {

global $wpdb;
$my_data = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zmseo_support", ARRAY_A);

if (sizeof($my_data))
foreach ($my_data as $i => $zn)
{
if (!isset($tuning[$zn['name']])) $tuning[$zn['name']]=$zn['val_d'];
}	
		
	if ($tuning['token_1'])
	{
		$api_go['z']=7;
		require( dirname( __FILE__ ) . '/sup_pages/api.php' );
		$mass_pup=$arr_api['data']['pointsup'];
		#echo '<h3>***API***</h3>';
	}
	else echo '<p>Потенциал и KPI не доступны при подключении к метрике по варианту 2</p>';	

echo '<h2>Точки роста</h2>';
echo '<div class="container">';

### Перебор id всех статей
# Title и дата
$arr_tmp = $wpdb->get_results("SELECT ID, post_date, post_title FROM $wpdb->posts WHERE post_status = 'publish'");
if (sizeof($arr_tmp))
foreach( $arr_tmp as $i => $id_p ) {$title_arr[$id_p->ID]=$id_p->post_title; $date_arr[$id_p->ID]=$id_p->post_date;}
#
# Теги
$arr_tmp2 = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_zmseo_tags'");
if (sizeof($arr_tmp2))
foreach( $arr_tmp2 as $i => $id_p ) {$tag_arr[$id_p->post_id]=$id_p->meta_value;}
#
if (sizeof($mass_pup))
{
$myposts = new WP_Query( array( 'posts_per_page' => -1, 'cache_results' => false, 'fields' => 'ids', 'post_type' => 'any' ) );

if (sizeof($myposts->posts))
foreach( $myposts->posts as $i => $id_p )
	{
		setup_postdata($id_p);
		$nonce_id_post = wp_create_nonce($id_p);
		$url_p=get_permalink($id_p);

		$link_p=str_replace('http://'.$_SERVER['HTTP_HOST'], '', $url_p);
		$link_p=str_replace('https://'.$_SERVER['HTTP_HOST'], '', $link_p);
		$link_p=str_replace('www.', '', $link_p);

		if (isset($title_arr[$id_p]))
		$mass_link[$link_p][0]=$title_arr[$id_p];
		$mass_link[$link_p][1]=$_SERVER['PHP_SELF'].'?page=zmseo&page_id='.$id_p.'&nonce_id='.$nonce_id_post;
		$mass_link[$link_p][2]=$url_p;
		
		if (isset($date_arr[$id_p])) 
		{
		$age=time()-strtotime($date_arr[$id_p]);
		$age=$age/86400/30.5;
		$age=round($age,1);
		$mass_link[$link_p][3]=$age;
		}
		$mass_link[$link_p][4]='';
		
	#смотрим теги
	$filter_pag=0;
	if (isset($tag_arr[$id_p]))
	{
	$sel_tag=json_decode($tag_arr[$id_p]); $tags_page='';
	
		for ($i=1;$i<6;$i++)
		{
			$tmp='tag_'.$i;
			if ($sel_tag->$tmp==1 and $tuning['tag_'.$i] and $tuning['tag_'.$i]!='free')
			{
				$arr_tags[$i]=json_decode($tuning['tag_'.$i]);
				$mass_link[$link_p][4].='<a class="menutags__item_'.$arr_tags[$i]->color_b.'" href="'.$_SERVER['PHP_SELF'].'?page=zmseo&filter='.$tmp.'" title="'.$arr_tags[$i]->dec_tag.'">'.$arr_tags[$i]->name_tag.'</a>';
				if (isset($_GET['filter']) and $_GET['filter']==$tmp) $filter_pag=1;
			}
		}
	}
	
	
	}
}
else '<p>Потенциальные страницы для роста еще не найдены.</p>';	

echo '<table class="big-table">';
echo '
<tr>
<td width="55">Возраст</td>
<td width="50%">Страница</td>
<td width="75"><a href="'.$_SERVER['SCRIPT_NAME'].'?page=pointsup&sort=fre"><img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="Суммарная частота запросов состоящих минимум из 3 слов и находящихся на 11-20 позициях">Частота</a></td>
<td width="80"><a href="'.$_SERVER['SCRIPT_NAME'].'?page=pointsup&sort=qua"><img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="Количество запросов состоящих минимум из 3 слов и находящихся на 11-20 позициях">Запросов</a></td>
<td width="80"><a href="'.$_SERVER['SCRIPT_NAME'].'?page=pointsup&sort=tai"><img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="см. вкладку Запросы и аналитика">Доп.Слова</a></td>
<td width="80"><a href="'.$_SERVER['SCRIPT_NAME'].'?page=pointsup&sort=pot"><img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="Отношение суммарной частоты запросов к KPI страницы">Потенциал</a></td>
<td width="50"><a href="'.$_SERVER['SCRIPT_NAME'].'?page=pointsup&sort=kpi"><img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="KPI за последний месяц">KPI</a></td>
</tr>
';	
	
if (isset($mass_pup))
foreach ($mass_pup as $i_z => $zn_z)
{
	if (isset($mass_link[$zn_z[0]][0]))
	{
		if (!isset($n)) $n=0;
		$n++;	
	if ($mass_link[$zn_z[0]][3]<4) $young='style="background:#f2bd35"';
	else $young='';
	
	if (!$tuning['token_1']) {$zn_z[4]='-'; $zn_z[5]='-';}
	
echo '
<tr>
<td '.$young.'>'.$mass_link[$zn_z[0]][3].' мес.</td>
<td>
'.$n.'. '.$mass_link[$zn_z[0]][0].'<br>
URL: <a href="'.$mass_link[$zn_z[0]][2].'" title="'.$mass_link[$zn_z[0]][2].'" target="_blank">'.substr($mass_link[$zn_z[0]][2], 0, 55).'</a> <a href="'.$mass_link[$zn_z[0]][1].'">Запросы и аналитика</a><br>
<div class="menutags">
'.$mass_link[$zn_z[0]][4].'
</div>
</td>
<td>'.$zn_z[1].'</td>
<td>'.$zn_z[2].'</td>
<td>'.$zn_z[3].'</td>
<td>'.$zn_z[4].'</td>
<td>'.$zn_z[5].'</td>
</tr>';
	}
}

echo '</table>';

if (!sizeof($mass_pup)) echo '<h3>Точки роста пока не найдены. Обновляются 1 раз в месяц.</h3>';

echo '<hr>';

echo '<b>Кто сюда попал?</b><br>
1. Страницы у которых есть запросы на второй странице выдачи Яндекса, состоящие из 3 и более слов (пример: утепление своими руками) и суммарно набравшие частоту более 100.<br>
2. Страницы у которых есть 5 и более запросов на второй странице выдачи Яндекса состоящие из 3 и более слов (пример: утепление своими руками).<br>
3. Страницы у которых [!суммарная !частота] деленная на KPI более 100. ([!суммарная !частота]/KPI>100)';

echo '
</div>';
}
?>