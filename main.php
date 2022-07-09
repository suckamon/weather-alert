<?php
require_once(__DIR__ . '/conf.php');
require_once(__DIR__ . '/functions.php');

// 時刻チェック
$hour = intval(date('H'));
echo $hour;
exit;
if($hour >= 23 || $hour < 7) {
  exit;
}

$res = getWeather();

// echo '<pre>';
// var_dump($res);

$status = $res['ResultInfo']['Status'];
if($status === 200) {
  $weather_list = $res['Feature'][0]['Property']['WeatherList']['Weather'];

  $current_date = $weather_list[0]['Date'];
  $current_rainfall = $weather_list[0]['Rainfall'];
  $message = null;

  for($i = 1; $i <= 6; $i++) {
    $target_date = $weather_list[$i]['Date'];
    $target_rainfall = $weather_list[$i]['Rainfall'];
    $diff_min = (strtotime($target_date) - strtotime($current_date)) / 60;

    // testの際は下記をコメントイン
    // $target_rainfall = 2;
  
    // 現在が晴れ（降水量0ミリ）の場合
    if($current_rainfall <= 0) {
      // 予測も晴れの場合スキップ
      if($target_rainfall == 0) {
        continue;
      }

      // 予測が小雨の場合
      elseif($target_rainfall > 0 && $target_rainfall <= LIGHT_RAIN) {
        $message = "${diff_min}分後に小雨が降り出します。予測降水量は${target_rainfall}ミリです。";
        break;
      }

      // 予測が雨の場合
      elseif($target_rainfall > LIGHT_RAIN) {
        $message = "${diff_min}分後に雨が降り出します。予測降水量は${target_rainfall}ミリです。";
        break;
      }
    }
  }

  // 降水警報メッセージがセットされた場合
  if(!is_null($message)) {
    // echo $message;
    notifyLine($message);
  }
}
