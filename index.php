<?php
require './config.php';
$header = '<!DOCTYPE html>
            <html>
              <head>
                <meta http-equiv="content-type" content="text/html; charset=utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=Edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
                <meta name="apple-mobile-web-app-status-bar-style" content="black">

                <meta http-equiv="Cache-Control" content="no-siteapp" /><link rel="alternate" media="handheld" href="#" />

                <title></title>
              </head>
              <body>';

$foter = '  </body>
            </html>';
//目录页数
$page_size = 25;
$fp = fopen('./all.html', 'a+');
fwrite($fp, $header);
for($i=1; $i<=$page_size; $i++) {
    $url = 'http://blog.csdn.net/u013372487/article/list/' . $i;
    $ml_dom = getDom($url);
    $ml_xpath = new DOMXpath($ml_dom);
    $xpath_url = ".//*[@id='skin_center']/div[1]/dl/dd/h3/a/@href";
    $url_dom = $ml_xpath->query($xpath_url);
    foreach ($url_dom as $key => $val) {
        $art = array();
        $newUrl = $url_dom->item($key)->nodeValue;
        $newUrl = 'http://blog.csdn.net' . $newUrl;
        $wz_dom = getDom($newUrl);
        $wz_xpath = new DOMXpath($wz_dom);
        $xpath_title = ".//*[@id='skin_center']/div[1]/div[1]/dl/dd/h3/a";
        $xpath_year = ".//*[@id='skin_center']/div[1]/div[1]/dl/dt/div/div[1]/span";
        $xpath_month = ".//*[@id='skin_center']/div[1]/div[1]/dl/dt/div/div[1]/em";
        $xpath_day = ".//*[@id='skin_center']/div[1]/div[1]/dl/dt/div/div[2]";
        $xpath_class = ".//*[@id='skin_center']/div[1]/div[1]/dl/dd/div[2]/p[1]/label/em";
        $xpath_content = ".//*[@id='article_content']";

        $wz_title = $wz_xpath->query($xpath_title);
        $art['title'] = $wz_title->item(0)->nodeValue;
        if(empty($art['title'])) {
            error_log($newUrl . "\n", 3, './error.log');
        }
        $wz_year = $wz_xpath->query($xpath_year);
        $art['year'] = $wz_year->item(0)->nodeValue;
        $wz_month = $wz_xpath->query($xpath_month);
        $art['month'] = $wz_month->item(0)->nodeValue;
        $wz_day = $wz_xpath->query($xpath_day);
        $art['day'] = $wz_day->item(0)->nodeValue;
        $wz_class = $wz_xpath->query($xpath_class);
        $art['class'] = '';
        foreach ($wz_class as $key_class => $val_class) {
            if(empty($art['class'])) {
                $art['class'] = $wz_class->item($key_class)->nodeValue;
            } else {
                $art['class'] .= "\t" . $wz_class->item($key_class)->nodeValue;
            }
        }
        $wz_content = $wz_xpath->query($xpath_content);
        $art['content'] = $wz_dom->saveXml($wz_content->item(0));
        fwrite($fp, '<a href="' . $newUrl . '" a>' . $art['title'] . '</a><br/>');
        $art['title'] = str_replace('/', '_', $art['title']);
        error_log($header . $art['content'] . $foter, 3, './log/' . $art['title'] . '.html');
    }
}
fwrite($fp, $foter);
fclose($fp);
