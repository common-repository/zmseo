<?php
if ( ! defined( 'ABSPATH' ) ) exit; #выход при прямом доступе

if ( is_user_logged_in() ) {

	function zmseo_keys() {
	// тут уже будет находиться содержимое страницы
	 require( dirname( __FILE__ ) . '/keys.php' );
		}
		
	function zmseo_links() {
	// тут уже будет находиться содержимое страницы
	 require( dirname( __FILE__ ) . '/links_page.php' );
		}

### страница задана	/ запросы и аналитика	
if (isset($_GET['page_id']) and !isset($_GET['page_us'])) zmseo_keys($_GET['page_id']); 

### страница задана	/ запросы и аналитика	
if (isset($_GET['page_id']) and isset($_GET['page_us']) and $_GET['page_us']=='links_page') zmseo_links($_GET['page_id']); 

### страница не задана, выводим весь список		
if (!isset($_GET['page_id']))
{
	
### чтение настоек		
global $wpdb;
$my_data = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zmseo_support", ARRAY_A);
if (sizeof($my_data))
foreach ($my_data as $i => $zn)
{
if (!isset($tuning[$zn['name']])) $tuning[$zn['name']]=$zn['val_d'];
}
###	

### Перебор id всех статей
# Title и дата
$arr_tmp = $wpdb->get_results("SELECT ID, post_date, post_title FROM $wpdb->posts WHERE post_status = 'publish'");
if (sizeof($arr_tmp))
foreach( $arr_tmp as $i => $id_p ) {$title_arr[$id_p->ID]=$id_p->post_title; $date_arr[$id_p->ID]=$id_p->post_date;}
# Теги
$arr_tmp2 = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_zmseo_tags'");
if (sizeof($arr_tmp2))
foreach( $arr_tmp2 as $i => $id_p ) {$tag_arr[$id_p->post_id]=$id_p->meta_value;}
#
$myposts = new WP_Query( array( 'posts_per_page' => -1, 'cache_results' => false, 'fields' => 'ids', 'post_type' => 'any' ) );

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
	
	if (isset($date_arr[$id_p])) 
	{
	$age=time()-strtotime($date_arr[$id_p]);
	$age=$age/86400/30.5;
	$age=round($age,1);
	$mass_link[$link_p][3]=$age;
	}
	$mass_link[$link_p][4]='';
	$mass_link[$link_p][5]=$id_p;
	
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
		if (isset($_POST['search']) and (strpos('_'.$link_p, $_POST['search']) or strpos('_'.$title_arr[$id_p], $_POST['search']))) $filter_pag=1;
		if (isset($_GET['filter']) and !$filter_pag) unset($mass_link[$link_p]);
		if (isset($_GET['filter']) and $filter_pag) $mass_filter[$id_p]=$link_p;
	#
}
wp_reset_postdata();
###		
	
### подключен вариант 1, делаем запрос на KPI	
	if ($tuning['token_1'])
	{
		$api_go['z']=3;
		require( dirname( __FILE__ ) . '/sup_pages/api.php' );
		$arr_m_day=$arr_api['data']['arr_m_day'];
		$mass_12m=$arr_api['data']['kpi_12m'];
		$mass_kpi=$arr_api['data']['kpi_pages'];
		$mass_f=$arr_api['data']['ya_filter'];
		$mass_l=$arr_api['data']['links'];
		#echo '<h3>***API***</h3>';
	}
###
	
### подключен вариант 2, делаем запрос на KPI к метрике	
	if (!$tuning['token_1'] and $tuning['token_2'])
	{
		$api_go['z']=5;
		require( dirname( __FILE__ ) . '/sup_pages/api.php' );	
	
		$metrika_go['z']=2;
		require( dirname( __FILE__ ) . '/sup_pages/metrika.php' );
		#echo '<h3>***Дом***</h3>';
	}
###
	
### нет подключений=нет KPI, показываем просто список страниц	
	if (!$tuning['token_1'] and !$tuning['token_2']) 
	{
		echo '<h3>Не задан токен для доступа к метрике</h3>';
		$notoken=1;
		for ($i_month=1;$i_month<7;$i_month++)	
		{
		$t=date("Y").'-'.date("m").'-01 -'.$i_month.' month';
		$arr_m_day[]=date( "Y-m", strtotime($t) );
		}
	}
###

### показываем все свои страниц или те, что получили по api
if (isset($notoken)) {$mass_tmp=$mass_link; unset($mass_kpi); unset($mass_12m);}
else $mass_tmp=$mass_kpi;
###
	
### создаем меню
$g_pages=explode(':', $tuning['good_pages']);
if (sizeof($g_pages))
foreach ($g_pages as $i => $zn)
{
if ($i==0) $menu_up='<a href="'.$_SERVER['SCRIPT_NAME'].'?page=zmseo&g_page='.$i.'">более '.$zn.'</a> | ';
if ($i==1) $menu_up.='<a href="'.$_SERVER['SCRIPT_NAME'].'?page=zmseo&g_page='.$i.'">от '.$zn.' до '.$lz.'</a> | ';
if ($i==2) $menu_up.='<a href="'.$_SERVER['SCRIPT_NAME'].'?page=zmseo&g_page='.$i.'">от '.$zn.' до '.$lz.'</a> | ';
if ($i==3) $menu_up.='<a href="'.$_SERVER['SCRIPT_NAME'].'?page=zmseo&g_page='.$i.'">менее '.$lz.'</a> | ';

if ((!isset($_GET['g_page']) or $_GET['g_page']==0) and $i==0) {$limp[0]=$zn; $limp[1]=99999;}
if (isset($_GET['g_page']) and $_GET['g_page']==1) {$limp[0]=$g_pages[1]; $limp[1]=$g_pages[0];}
if (isset($_GET['g_page']) and $_GET['g_page']==2) {$limp[0]=$g_pages[2]; $limp[1]=$g_pages[1];}
if (isset($_GET['g_page']) and $_GET['g_page']==3) {$limp[0]=$g_pages[3]; $limp[1]=$g_pages[2];}

$lz=$zn;
}
if ($mass_f['all']>0) $menu_up.='<br><img src="'.plugin_dir_url( __FILE__ ).'images/black_kpi.png" width="20" title="Список страниц возможно находящихся под фильтром доступен в полной версии"> <b>Возможно страницы под фильтром ПС Яндекс '.$mass_f['all'].' шт.</b>';
###

	
echo '<h2>Страницы</h2>';
echo '<div class="container">';

if (!isset($arr_m_day)) { echo '<p style="color:red;"><b>Ошибка! Не задан массив столбцов</b></p>'; exit;}


$tt='<p><img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="Общее количество уникальных посетителей"> KPI страниц: '.$menu_up.'</p>';

if (isset($_POST['search'])) $search=$_POST['search'];
else $search='';
echo '
<table>
<tr><td>
<p><img src="'.plugin_dir_url( __FILE__ ).'/info.png" title="Общее количество уникальных посетителей"> KPI страниц: '.$menu_up.'</p>
</td><td>
<form enctype="multipart/form-data" method="post" action="'.$_SERVER['SCRIPT_NAME'].'?page=zmseo&filter=search">
<input type="text" name="search" value="'.$search.'" size="40" placeholder="Поиск по названию или URL"> 
<button class="btn" type="submit" name="butt" value="Найти">Найти</button>
</form>
</td></tr>
</table>
';


echo '
<table class="big-table">
<tr>
<td width="55">Возраст</td>
<td width="50%">Страница</td>
';
foreach ($arr_m_day as $i => $zn)
{
echo '<td class="center">'.$zn.' KPI</td>
';
}
echo '
</tr>
';

if (isset($mass_tmp))
foreach ($mass_tmp as $i => $zn)
{
	if ((isset($mass_link[$i][1]) and $mass_kpi[$i][$arr_m_day[0]]>=$limp[0] and $mass_kpi[$i][$arr_m_day[0]]<$limp[1]) or isset($notoken) or (isset($_GET['filter']) and isset($mass_link[$i][1])))
	{
	if (!isset($n_url2)) $n_url2=0;	
	$n_url2++;
	if (!isset($ps_m)) $ps_m=0;		
	$ps_m+=$mass_kpi[$i][$arr_m_day[0]]/30;

	if (isset($mass_link[$i][3]) and $mass_link[$i][3]<4) $young='style="background:#f2bd35" title="Страница еще не набрала возраст"';
	else $young='';
	
$nonce_id_post=wp_create_nonce($mass_link[$i][5]);	
if (isset($mass_f[$i]) and $mass_f[$i]=='yes' and !$young) $ya_filter='<br><img src="'.plugin_dir_url( __FILE__ ).'images/black.png" width="20" title="Страница возможно под фильтром ПС Яндекс">';
else $ya_filter='';


if (!isset($mass_l[$i][0])) $mass_l[$i][0]='-';
if (!isset($mass_l[$i][1])) $mass_l[$i][1]='-';
echo '
<tr>
<td '.$young.'>'.$mass_link[$i][3].' мес.'.$ya_filter.'</td>
<td>'.$n_url2.'. '.$mass_link[$i][0].' <br>
URL: <a href="'.$mass_link[$i][1].'" title="'.$mass_link[$i][1].'" target="_blank">'.substr($mass_link[$i][1], 0, 55).'</a> <a href="'.$mass_link[$i][2].'&nonce_id='.$nonce_id_post.'">Запросы и аналитика</a><br>

<a href="'.$mass_link[$i][2].'&page_us=links_page&nonce_id='.$nonce_id_post.'">
<img src="'.plugin_dir_url( __FILE__ ).'images/links.png" width="12" title="Внутренние ссылки">  :
<img src="'.plugin_dir_url( __FILE__ ).'images/down.png" width="12" title="Входящие"> '.$mass_l[$i][0].'
<img src="'.plugin_dir_url( __FILE__ ).'images/up.png" width="12" title="Исходящие"> '.$mass_l[$i][1].'</a><br>
<div class="menutags">
'.$mass_link[$i][4].'
</div>
</td>
';

		foreach ($arr_m_day as $i2 => $zn2)
		{
			if (!isset($mass_kpi[$i][$zn2])) $mass_kpi[$i][$zn2]=0;
			if ($n_url2>=$arr_api['data']['lim1']) $mass_kpi[$i][$zn2]='n/a';
			if ($ps_m>=$arr_api['data']['lim2']) $mass_kpi[$i][$zn2]='n/a';
			if (isset($mass_12m[$i][$zn2]) and $mass_12m[$i][$zn2]>0) $prc=round($mass_kpi[$i][$zn2]*100/$mass_12m[$i][$zn2],2)-100;
			if (($prc>-1 and $prc<1) or ($mass_kpi[$i][$zn2]<5 and $mass_12m[$i][$zn2]<5) or $mass_12m[$i][$zn2]==0) $prc='';
			if ($prc<0) $color='red';
			else $color='green';
			if ($prc>0) $prc='+'.$prc;
			if ($prc) $prc=$prc.'%';
			echo '<td class="center">'.$mass_kpi[$i][$zn2].'<div class="'.$color.'" title="В прошлом году: '.$mass_12m[$i][$zn2].'">'.$prc.'</div></td>
			';
		}

echo '
</tr>
';
	}
}

echo '
</table>
';

if (!$n_url2) echo 'Страниц с KPI '.$limp[0].' пока нет, выберите значение чуть меньше';
echo '
</div>';

} #!$_GET['page_id']

} #is_user_logged_in()
?>