=== ZMSEO ===
Contributors: ZMSEO
Donate link: https://zmseo.ru/
Tags: seo, zmseo, content analysis, google, yandex
Requires at least: 4.6
Tested up to: 5.1
Stable tag: trunk
Requires PHP: 5.2.4
License: GPLv2 or later

Wordpress plugin for SEO analytic – ZMSEO. KPI and page’s requests directly in your personal account. During work plagin makes calls to Yandex.Metrika (https://api-metrika.yandex.ru) and collects data for processing and loading them in your personal area.
Also, the most part of operations pass on external web-site https://zmseo.ru/api/api_sistem.php 


== Description ==
Capabilities of plugin:

1.Cannibalization of traffic
Shows requests for which traffic is divided on two or more pages

2.Tautology of words
Shows words which repeats 2 or more times in one sentence

3.Ngram
Shows phrases which repeats on one page

4.List of requests
Shows list of requests which in the TOP-50 of Yandex for every page 
 
5.Wordstat for request
Shows exact frequency for every request in month

6.SEO KPI
Shows SEO KPI of pages with preservation history during half a year

7.Position on request
Shows the place of web-site in Yandex on every request

8.List of words
Plugin analyses a website and shows a list of missing words for every article

9.Density of words
Shows density of Top-10 words that were used on the page


== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/zmseo` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress


== Screenshots ==
1. KPI страниц.
2. Запросы на страницу.
3. Необходимые дополнительные слова, 10 наиболее часто употребляемых слов, n-граммы.
4. Тавтология. Слова встречающиеся 2 и более раза в одном предложении. 
5. История изменений на странице с динамикой KPI.
6. Каннибалы. Страницы собирающие трафик по одним и тем же запросам (нет ярко выраженной страницы для запроса).


== Changelog ==
= 1.14.1 =
Обновление от 28.05.2019

Обновления:
* Мелкие допилки

= 1.14.0 =
Обновление от 27.05.2019

Обновления:
* KPI сайта по годам

= 1.13.4 =
Обновление от 11.03.2019

Обновления:
* Мелкие допилки

= 1.13.3 =
Обновление от 11.03.2019

Обновления:
* Мелкие допилки

= 1.13.2 =
Обновление от 09.03.2019

Обновления:
* Мелкие допилки

= 1.13.1 =
Обновление от 07.03.2019

Обновления:
* Реализация подсветки в редакторе
* Мелкие допилки

= 1.12.1 =
Обновление от 24.01.2019

Обновления:
* Мелкие допилки

= 1.12.0 =
Обновление от 21.01.2019

Обновления:
* Поиск
* Мелкие допилки

= 1.11.1 =
Обновление от 02.01.2019

Обновления:
* Мелкие допилки

= 1.11.0 =
Обновление от 31.12.2018

Обновления:
* Увеличили количество запросов в аналитике
* Результат анализа по каждому ключу
* Мелкие допилки

= 1.10.1 =
Обновление от 20.11.2018

Обновления:
* Добавили поиск каннибалов за год
* Первый вариант подключения к метрики стал важнее
* Мелкие допилки

Исправленные ошибки:
* Подключение при варианте 2

= 1.10.0 =
Обновление от 12.11.2018

Обновления:
* В раздел сводка добавлен анализ всего сайта.
* При редактировании выводим TOP10 запросов.
* В запросах и аналитике выводим трафик и отказы.

Исправленные ошибки:
* Устранили замечания в логах

= 1.9.0 =
Обновление от 16.10.2018

Обновления:
* Отображение анкоров при внутренней перелинковке.
* Добавлены теги в точки роста.

Исправленные ошибки:
* Для стабильно работы лонгриды ограничены в 30к знаков.
* Устранили ошибки в тавтологии.

= 1.8.0 =
Обновление от 10.09.2018

Обновления:
* Добавлены метки к страницам которые возможно под фильтром ПС Яндекс.

Исправленные ошибки:
* Мелкие баги.

= 1.7.1 =
Обновление от 31.08.2018

Исправленные ошибки:
* Мелкие баги при нулевых циклах.

= 1.7.0 =
Обновление от 20.08.2018

Обновления:
* Добавлены рекомендации при редактировании страниц.
* Поиск анкоров для внутренней перелинковки.
* Загрузка собственных запросов.

Исправленные ошибки:
* Ускорена работа плагина.
* Возникновение ошибки при пустых циклах.

= 1.6.0 =
Обновление от 16.07.2018

Обновления:
* Добавлены теги для объединения и сортировки страниц по группам.

Исправленные ошибки:
* Дублирование страниц с 0.
* Пустой экран на сайтах с 2000 страниц и более.

= 1.5.0 =
Обновление от 05.07.2018

Обновления:
* Интеграция с Я.Метрикой не обязательна. 
* Данные по KPI сравниваются с прошлым годом.

= 1.4 =
* First version.
 

== Upgrade Notice ==
=1.5.0=
Плагин можно использовать без Я.Метрики. Добавлено сравнение KPI с прошлым годом.
 
= 1.4 =
New capabilities were added