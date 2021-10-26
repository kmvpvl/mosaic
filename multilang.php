<?php
$strings = array (
    'en'=>array(
        0=>'Выберите изображение для загрузки',
        1=>'Картинка',
        2=>'Размер',
        3=>'Палитра',
        4=>'Стоимость',
        5=>'Заказ',
        6=>'Доставка',
        7=>'Получи мозаику за 6 шагов',
        8=>''
    ),
    'ru'=>array(
        0=>'Выберите изображение для загрузки',
        1=>'Картинка',
        2=>'Размер',
        3=>'Палитра',
        4=>'Стоимость',
        5=>'Заказ',
        6=>'Доставка',
        7=>'Получи мозаику за 6 шагов',
        8=>''
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