<?php
$strings = array (
    'en'=>array(
        0=>'Select image to upload',
        1=>'',
        2=>'',
        3=>'',
        4=>''
    ),
    'ru'=>array(
        0=>'Выберите изображение для загрузки',
        1=>'Выбор картинки',
        2=>'Размер и обрезка',
        3=>'Выбор палитры',
        4=>'Расчет стоимости',
        5=>'Заказ',
        6=>'Отслеживание доставки',
        7=>''
    )
);
function getLangString(int $id, string $lang='en'):string {
    global $strings;
    if (!array_key_exists($lang, $strings)) $lang = 'en';
    return $strings[$lang][$id];
}
function getSpecString(int $id): string {
    $l = explode('_', Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))[0];
    return getLangString($id, $l);
}

?>