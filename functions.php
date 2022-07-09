<?php

/**
 * Yahoo気象情報APIから、この先1時間の降水情報を取得
 * 
 * @return array
 */
function getWeather() {
  $call_url = API_URL . '?output=json&coordinates=' . LONGITUDE . ',' . LATITUDE . '&appid=' . APP_ID;

  $call_options = array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_SSL_VERIFYPEER => false,
  );
  
  // curlでリクエスト実行
  $ch = curl_init($call_url);
  curl_setopt_array($ch, $call_options);
  $result = curl_exec($ch);
  $info = curl_getinfo($ch);
  $errorNo = curl_errno($ch);
  curl_close($ch);
  
  // レスポンスのJSONデータを連想配列に変換
  return json_decode($result, true);  
}

/**
 * LINE通知
 * 
 * @param string $message
 * @return void
 */
function notifyLine($message) {
  // トークンを記載します
  $token = LINE_TOKEN;

  // リクエストヘッダを作成します
  $query = http_build_query(['message' => $message]);
  $header = [
          'Content-Type: application/x-www-form-urlencoded',
          'Authorization: Bearer ' . $token,
          'Content-Length: ' . strlen($query)
  ];
  $ch = curl_init('https://notify-api.line.me/api/notify');
  $options = [
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_POST            => true,
      CURLOPT_HTTPHEADER      => $header,
      CURLOPT_POSTFIELDS      => $query,
      CURLOPT_SSL_VERIFYPEER => false,
  ];
  curl_setopt_array($ch, $options);
  curl_exec($ch);
  curl_close($ch);
}