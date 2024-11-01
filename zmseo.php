<?php
if ( ! defined( 'ABSPATH' ) ) exit; #выход при прямом доступе

/*
Plugin Name: ZMSEO
Plugin URI: https://zmseo.ru/
Description: SEO аналитика запросов и метрики вашего сайта 
Version: 1.14.1
Author: Sergey F
*/

/* *************************************************************************************************************** */
### Удаление таблиц
register_uninstall_hook( __FILE__, 'zmseo_drop_plugin_tables');
function zmseo_drop_plugin_tables()
{
	//drop a custom db table
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$wpdb->query( 'DROP TABLE IF EXISTS '.$wpdb->prefix.'zmseo_support' );
}
###

### активируем плагин и создаем таблицу
function zmseo_create_table() {
global $wpdb;

$table_name = $wpdb->prefix.'zmseo_support';
$charset_collate = $wpdb->get_charset_collate();
	
$sql = "CREATE TABLE $table_name (
id int(11) NOT NULL AUTO_INCREMENT,
name varchar(50) DEFAULT '' NOT NULL,
val_d varchar(500) DEFAULT '' NOT NULL,
PRIMARY KEY  (id)
) $charset_collate;";
	
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
	
$my_data = $wpdb->get_results("SELECT * FROM ".$table_name, ARRAY_A);

if (sizeof($my_data))
foreach ($my_data as $i => $zn)
{
if (!$tuning[$zn['name']]) $tuning[$zn['name']]=$zn['val_d'];
}
if (!$tuning['good_pages'])	
	{
	$wpdb->insert($table_name, array( 'id' => 1, 'name' => 'unid_key', 'val_d' => 'free version'),array(  '%d', '%s', '%s' ));
		$wpdb->insert($table_name, array( 'id' => 2, 'name' => 'token_1'),array(  '%d', '%s' ));
			$wpdb->insert($table_name, array( 'id' => 3, 'name' => 'token_2'),array(  '%d', '%s' ));
				$wpdb->insert($table_name, array( 'id' => 4, 'name' => 'id_app'),array(  '%d', '%s' ));
					$wpdb->insert($table_name, array( 'id' => 5, 'name' => 'id_site'),array(  '%d', '%s' ));
						$wpdb->insert($table_name, array( 'id' => 6, 'name' => 'good_pages', 'val_d' => '200:100:50:0'), array( '%d', '%s', '%s' ));
	}
						
}
###

### Главное меню
function zmseo_admin_menu(){
	
	# Создаем саму страницу
	if ( function_exists('add_menu_page') )
    {	
		
	#title,меню,права,якорь,url,icon,место
	add_menu_page('Страницы | ZMSEO', 'ZMSEO', 1, 'zmseo', 'zmseo_pages'); 
	
	#родитель,title,назв,права,якорь,url
	add_submenu_page( 'zmseo', 'KPI страниц | ZMSEO', 'KPI страниц', 1, 'zmseo', 'zmseo_pages');
	add_submenu_page( 'zmseo', 'Точки роста | ZMSEO', 'Точки роста', 1, 'pointsup', 'zmseo_pointsup');
	add_submenu_page( 'zmseo', 'Каннибалы | ZMSEO', 'Каннибалы', 1, 'cannibals', 'zmseo_cannibals');
	add_submenu_page( 'zmseo', 'Сводка | ZMSEO', 'Сводка', 1, 'report', 'zmseo_report');
	
	if ( current_user_can('manage_options') ) 
		add_submenu_page( 'zmseo', 'Настройки | ZMSEO', 'Настройки', 'manage_options', 'support', 'zmseo_support'); 
	
	wp_enqueue_style( 'zmseo', plugins_url('style.css', __FILE__) );
	
	function zmseo_pages() {
	// тут уже будет находиться содержимое страницы
	 require( dirname( __FILE__ ) . '/pages.php' );
		}
				
	function zmseo_support() {
	// тут уже будет находиться содержимое страницы
	 require( dirname( __FILE__ ) . '/support.php' );
		}
	
	function zmseo_cannibals() {
	// тут уже будет находиться содержимое страницы
	 require( dirname( __FILE__ ) . '/cannibals.php' );
		}
		
	function zmseo_pointsup() {
	// тут уже будет находиться содержимое страницы
	 require( dirname( __FILE__ ) . '/pointsup.php' );
		}
		
	function zmseo_report() {
	// тут уже будет находиться содержимое страницы
	 require( dirname( __FILE__ ) . '/report.php' );
		}
			
	}
	
}
###

### Плашка в посты и страницы
add_action('add_meta_boxes', 'zmseo_add_custom_box');
function zmseo_add_custom_box(){
	$screens = array( 'post', 'page' );
	add_meta_box( 'zmseo_sectionid', 'ZMSEO', 'zmseo_meta_box_callback');
}
###

### HTML код плашки
function zmseo_meta_box_callback( $post, $meta ){
	wp_enqueue_style( 'zmseo', plugins_url('style.css', __FILE__) );
	//20.02.2019
	wp_enqueue_script( 'zmseo', plugins_url('script.js', __FILE__) );
	require( dirname( __FILE__ ) . '/post_page.php' );
}
###

### Сохранение плашки
function zmseo_sup_save($post_id){

	if ($_POST['save_supp'])
	{
	$arr_sup['title']=$_POST['title'];
	$arr_sup['desc']=$_POST['description'];
	$arr_sup['h1']=$_POST['h1'];
	$data_sup=sanitize_post(json_encode($arr_sup));

global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$name_bd=$wpdb->prefix.'zmseo_support';

$my_data = $wpdb->get_results("SELECT * FROM ".$name_bd, ARRAY_A);

if (sizeof($my_data))
foreach ($my_data as $i => $zn)
{
if (!$tuning[$zn['name']]) $tuning[$zn['name']]=$zn['val_d'];
}
if (!$tuning['sup_post']) $wpdb->insert($name_bd, array( 'name' => 'sup_post', 'val_d' => $data_sup), array( '%s', '%s' ));
else $wpdb->update($name_bd, array('val_d' => $data_sup), array('name' => 'sup_post'), array( '%s' ), array( '%s' ));
	}

	if ($_POST['add_keys']) 
	{
		$arr_text=explode("
", sanitize_post($_POST['arr_keys']));
		if (sizeof($arr_text)){
			$kol=0;
			foreach ($arr_text as $n => $zn){
				if (trim($zn)) {
					$arr_tmp=explode(";", $zn);
					if (!$arr_tmp[1] and $n==0) $arr_tmp[1]=1;
					if (!$arr_tmp[1] and $n>0)$arr_tmp[1]=0;
					$tmp_arr[trim($arr_tmp[0])]=trim($arr_tmp[1]);
					$kol++;
					if ($kol==50) break;
				}
			}
			arsort($tmp_arr);

			if (sizeof($tmp_arr)){
				$mas_key = '';
				foreach ($tmp_arr as $n => $zn) $mas_key.=trim($n).';'.$zn.'*
';

				$mas_key=trim($mas_key);
				if (!trim($_POST['arr_keys'])) $mas_key='free';
				if ($_POST['type']=='add'){
					add_post_meta( $post_id, '_zmseo_keys', $mas_key, true );
				}
				if ($_POST['type']=='upg') {
					update_post_meta($post_id, '_zmseo_keys', $mas_key);
				}
			}
		}
	}
}
###

register_activation_hook(__FILE__, 'zmseo_create_table');
add_action('admin_menu',  'zmseo_admin_menu' );
add_action('save_post',  'zmseo_sup_save' );



add_filter( 'tiny_mce_before_init', 'my_tinymce_setup_function' );
function my_tinymce_setup_function( $initArray ) {
	$initArray['setup'] = 'function(ed) {
    ed.on("mousedown", function() {
       tinyOnChange();
    });
}';
	return $initArray;
}

//Чистим наши MARK-и перед сохранением полюбэ
/*add_filter( 'wp_insert_post_data' , 'zm_filter_post_data' , '99', 2 );
function zm_filter_post_data( $data , $postarr ) {
	// Change post title
	$pattern = '/<mark\\b[^>]*data-zm-counter-\\b[^>]*>([^<>]*)<\/mark>/gi';
	$replacement = '${1}';
	$data['post_content'] = preg_replace($pattern,$replacement,$data['post_content']);

	$data['post_title'] .= '_suffix';
	return $data;
}*/


//19.02.2019
//Это хук на сохранение настроек в новом редакторе по Ajax БЕЗ СОХРАНЕНИЯ ПОСТА
if (
	isset($_POST['zm_save_ajax'])
	&&
	( isset($_POST['save_supp']) || isset($_POST['add_keys']))
) {
	if(isset($_POST['post_id']) && (int)$_POST['post_id']){
		$postId = (int)$_POST['post_id'];
	} else {
		$post = get_post();
		$postId = $post->ID;
	}
	zmseo_sup_save($postId);
}

// Кейвордс - , " и т.д.
// отжатие кнопки убирает все <mark
function metrika_api( $token, $metrika_url ) {
	
$headers = array('Authorization: OAuth '.$token, 'Content-Type: application/x-yametrika+json');

$ch = curl_init();  
curl_setopt($ch, CURLOPT_URL,$metrika_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 35);
curl_setopt ($ch, CURLOPT_TIMEOUT, 35);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
$data_met = curl_exec($ch); 
curl_close($ch);
$array = json_decode($data_met);
	return $array;
}
?>