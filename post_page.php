<?php
if (!defined('ABSPATH')) exit; #выход при прямом доступе

if (is_user_logged_in()) {

### чтение настоек
	global $wpdb;
	$my_data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "zmseo_support", ARRAY_A);
	if (sizeof($my_data))
		foreach ($my_data as $i => $zn) {
			if (isset($zn['name']) || !$tuning[$zn['name']]) $tuning[$zn['name']] = $zn['val_d'];
		}
	$sel_sup = json_decode($tuning['sup_post']);
	$nonce = wp_create_nonce($_SERVER['REQUEST_URI']);
	$nonce_id_post = wp_create_nonce($post->ID);
### 

### подгрузка своих запросов
	$zmseo_keys = get_post_meta($post->ID, '_zmseo_keys', true);
	$zm_quantity = substr_count($zmseo_keys, ";");
	if ($zmseo_keys) $name_sub = 'upg';
	else $name_sub = 'add';
	if ($zmseo_keys == 'free') $zmseo_keys = '';
###

# Поиск title и h1
	$tag = get_post_meta($post->ID, '', true);
	$tag_arr['_word_press'] = $post->post_title;
	if (sizeof($tag))
		foreach ($tag as $i => $zn) {
			if (strpos('_' . $i, 'title') or strpos('_' . $i, 'h1') or strpos('_' . $i, 'desc')) $tag_arr[$i] = $zn[0];
		}
###	

### API
	$api_go['z'] = 9;
	require(dirname(__FILE__) . '/sup_pages/links.php');
	require(dirname(__FILE__) . '/sup_pages/api.php');
	$zmseo_arr_post = $arr_api['data']['post'];
###	
	$data1 = array('зеленый','зеленоватый','зелень');
	$data2 = array('рука','рукой','руке','руки','р "уки"','ру, ки');
	$lampCounter = 1;
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
	<div class="zmseo_tabs">
    <input id="tab1" type="radio" name="zmseo_tabs" checked>
    <label for="tab1" title="Анализ страницы">Анализ страницы</label>
	
	<input id="tab2" type="radio" name="zmseo_tabs">
    <label for="tab2" title="Настройка">ТОР запросов</label>
 
    <input id="tab3" type="radio" name="zmseo_tabs">
    <label for="tab3" title="Ссылки">Ссылки</label>
 
    <input id="tab4" type="radio" name="zmseo_tabs">
    <label for="tab4" title="Добавить запросы">Добавить запросы</label>
 
    <input id="tab5" type="radio" name="zmseo_tabs">
    <label for="tab5" title="Настройка">Настройка</label>
';

### Анализ
# Лампочка
if (is_array($zmseo_arr_post['assay']['lamp']))
{
foreach ($zmseo_arr_post['assay']['lamp'] as $i => $zn) {
if (is_array($zn)) $lamp[$i]='<a class="zm-lamp-button js-zm-mark-button" data-zm-counter="'.($lampCounter++).'" data-keywords="' . str_replace('"',"&quot;",implode('-|-', $zn)) . '"></a>';
}
}
#
	echo '
    <section id="content-tab1">
        <p>
* Раздел в процессе пополнения<br>
<b>Title: </b> ' . $zm_title . '<br>
<b>KPI: ' . $zmseo_arr_post['assay']['kpi'] . ' | <span style="color:red">Яндекс:</span> ' . $zmseo_arr_post['assay']['kpi_y'] . ' | <span style="color:blue">Гугл:</span> ' . $zmseo_arr_post['assay']['kpi_g'] . ' |</b>
<a href="' . $_SERVER['PHP_SELF'] . '?page=zmseo&page_id=' . $post->ID . '&nonce_id=' . $nonce_id_post . '">Запросы и аналитика</a>
<hr>
';
	if (isset($zmseo_arr_post['assay']['black'])) {
		echo '
<img src="' . plugin_dir_url(__FILE__) . 'images/black.png" width="20">
<a href="javascript:void(null);" onclick="change(\'black_tag\')" style="font-size: 16px;">Страница под фильтром ПС Яндекс (' . sizeof($zmseo_arr_post['assay']['black']) . ')</a>
<div id="black_tag" style="display:none;">
<ul style="padding: 0px 0px 0px 30px;">';
		foreach ($zmseo_arr_post['assay']['black'] as $i => $zn) {
			echo '<li><img src="' . plugin_dir_url(__FILE__) . 'images/black_min.png" width="15"> ' . $zn . '</li>';
		}

		echo ' </ul>
</div>
<hr>
';
	}

	if (isset($zmseo_arr_post['assay']['red'])) {
		echo '
<img src="' . plugin_dir_url(__FILE__) . 'images/red.png" width="20">
<a href="javascript:void(null);" onclick="change(\'red_tag\')" style="font-size: 16px;">Допущены существенные ошибки (' . sizeof($zmseo_arr_post['assay']['red']) . ')</a>
<div id="red_tag" style="display:none;">
<ul style="padding: 0px 0px 0px 30px;">';
		foreach ($zmseo_arr_post['assay']['red'] as $i => $zn) {
			echo '<li><img src="' . plugin_dir_url(__FILE__) . 'images/red_min.png" width="15"> ' . $zn . ' ' . $lamp[$i] . '</li>';
		}

		echo ' </ul>
</div>
<hr>
';
	}

	if (isset($zmseo_arr_post['assay']['yellow'])) {
		echo '
<img src="' . plugin_dir_url(__FILE__) . '/images/yellow.png" width="20">
<a href="javascript:void(null);" onclick="change(\'yellow_tag\')" style="font-size: 16px;">Есть над чем работать (' . sizeof($zmseo_arr_post['assay']['yellow']) . ')</a>
<div id="yellow_tag" style="display:none;">
<ul style="padding: 0px 0px 0px 30px;">';
		foreach ($zmseo_arr_post['assay']['yellow'] as $i => $zn) {
			echo '<li><img src="' . plugin_dir_url(__FILE__) . 'images/yellow_min.png" width="15"> ' . $zn . ' ' . $lamp[$i] . '</li>';
		}

		echo ' </ul>
</div>
<hr>
';
	}

	if (isset($zmseo_arr_post['assay']['green'])) {
		echo '
<img src="' . plugin_dir_url(__FILE__) . 'images/green.png" width="20">
<a href="javascript:void(null);" onclick="change(\'green_tag\')" style="font-size: 16px;">Хороший результат (' . sizeof($zmseo_arr_post['assay']['green']) . ')</a>
<div id="green_tag" style="display:none;">
<ul style="padding: 0px 0px 0px 30px;">';
		foreach ($zmseo_arr_post['assay']['green'] as $i => $zn) {
			echo '<li><img src="' . plugin_dir_url(__FILE__) . 'images/green_min.png" width="15"> ' . $zn . '</li>';
		}

		echo ' </ul>
</div>
';
	}
	echo '
        </p>
    </section>  
';
###

### ТОР запросов
	echo '
    <section id="content-tab2">
        <p>
          <b>ТОР запросов на страницу за 12 мес.</b>
        </p>
<h3><span style="color:red">ПС Яндекс</span></h3>		
<table class="big-table">
<tr>
<td>№</td>
<td>Запрос</td>
<td>Трафик</td>
<td>Отказы</td>
</tr>
';
	if (isset($zmseo_arr_post['assay']['top_y']))
		foreach ($zmseo_arr_post['assay']['top_y'] as $word => $kpi_y) {
			if (!isset($ny)) $ny = 0;
			$ny++;
			echo '
<tr>
<td>' . $ny . '</td>
<td>' . $word . '</td>
<td>' . $kpi_y[0] . '</td>
<td>' . $kpi_y[1] . '%</td>
</tr>';
		}
	echo '
</table>		
	</section>  
';
###

### Ссылки
	echo '
    <section id="content-tab3">
        <p>	
<h3>Возможные исходящие ссылки</h3>
Здесь подобраны страницы на которые вы можете сослаться внутри этого текста.<br>
<table class="big-table">
<tr><td width="200"><b>Анкор</b></td><td><b>URL</b></td><td><b>Статья</b></td><td><b>KPI</b></td></tr>
';
	if (isset($zmseo_arr_post['links']['out']))
		foreach ($zmseo_arr_post['links']['out'] as $i => $zn) {
			echo '<tr>
	<td>' . $zn[0] . '</td>
	<td><a href="' . get_permalink($zn[1]) . '" target="_blank">URL</a></td>
	<td><a href="/wp-admin/post.php?post=' . $zn[1] . '&action=edit">' . esc_html(get_the_title($zn[1])) . '</a></td>
	<td>' . $zn[2] . '</td>
	</tr>';
		}
	echo '</table>
<hr>
<h3>Возможные входящие ссылки</h3>
Здесь подобраны страницы с которых скорее всего вы можете сослаться на текущую.<br>
<ol>
';
	if (isset($zmseo_arr_post['links']['in']))
		foreach ($zmseo_arr_post['links']['in'] as $i => $zn) {
			echo '<li><a href="/wp-admin/post.php?post=' . $i . '&action=edit">' . $zn['name'] . '</a></li>';
		}
	echo '</ol>

		</p>
    </section> 
';
###

### Добавление запросов
	echo '
    <section id="content-tab4">
        <p>
          Добавлено ' . $zm_quantity . ' из 50<br>
        </p>
<textarea name="arr_keys" style="width: 80%;" rows="10" placeholder="* новый запрос с новой строки, добавлять в порядке убывания частоты">' . str_replace('*', '', $zmseo_keys) . '</textarea>
<br><br>
<button class="btn js-zm-ajax-btn" type="submit" name="add_keys" value="Добавить">Добавить</button><br>
<input type="hidden" name="type" value="' . $name_sub . '">
<input type="hidden" name="nonce" value="' . $nonce . '">
    </section> 
';
###

### Настройки
	echo '
    <section id="content-tab5">
        <p>
          <b>Для адаптации со сторонними плагинами укажите кто управляет Title, Description и H1</b>
        </p>


<table class="big-table">
<tr>
<td>Title</td>
<td>Description</td>
<td>H1</td>
<td>Плагин</td>
<td>Текст</td>
</tr>
';
	if (sizeof($tag_arr))
		foreach ($tag_arr as $i => $zn) {
			if ($sel_sup->title == $i) $sel['t'] = 'checked';
			else $sel['t'] = '';
			if ($sel_sup->desc == $i) $sel['d'] = 'checked';
			else $sel['d'] = '';
			if ($sel_sup->h1 == $i) $sel['h'] = 'checked';
			else $sel['h'] = '';
			echo '<tr>';
			echo '<td><input type="radio" name="title" value="' . $i . '" ' . $sel['t'] . '></td>';
			echo '<td><input type="radio" name="description" value="' . $i . '" ' . $sel['d'] . '></td>';
			echo '<td><input type="radio" name="h1" value="' . $i . '" ' . $sel['h'] . '></td>';
			echo '<td>' . $i . '</td>';
			echo '<td>' . $zn . '</td>';
			echo '</tr>';
			unset($sel);
		}
	echo '
</table>
<button class="btn js-zm-ajax-btn" type="submit" name="save_supp" value="Сохранить">Сохранить</button><br>
<input type="hidden" name="nonce" value="' . $nonce . '">
    </section>  
';
####

	echo '
</div>
';

}




?>