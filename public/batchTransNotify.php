<?php
@ini_set('memory_limit', '256M');
    //支付宝批量转账异步回调地址
    $url = "http://www.demo2.com/official/finance/manageWithDrawResAsync";
    //$url = "http://www.baidu.com";
    //$post_data = $_POST;
    // var_dump($post_data);
    // die;

    $post_data = $_POST;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //timeout on connect
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout on response
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $out = curl_exec($ch);
    var_dump( curl_error($ch) );
    curl_close($ch);
    echo $out;