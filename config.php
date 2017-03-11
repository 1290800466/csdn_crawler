<?php
/**
 * 爬取网站总入口url
 * @var unknown
 */
$website = array(
    'http://blog.csdn.net/u013372487/article/list/',
);

/**
 * 请求url页面信息
 * @param str $url
 * @return  str mixed|boolean
 */
function curl_get($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //302跳转
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0');
    curl_setopt($curl, CURLOPT_REFERER, $url);
    $data = curl_exec($curl);
    $code = curl_getinfo($curl,CURLINFO_HTTP_CODE); //输出请求状态码
    curl_close($curl);
    if(200 == $code) {
        if (preg_match('#<meta[^>]*charset="?gb2312"[^>]*>#', $data)) {
            $data = iconv("gb2312","utf-8//IGNORE",$data);
            $data = preg_replace('#<meta[^>]*charset="?gb2312"[^>]*>#is', '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">', $data);
        }

        if (!preg_match('#<meta charset="utf-8"[^>]*>#is', $data)) {
            $data = str_replace('<head>', '<head><meta http-equiv="Content-Type" content="text/html;charset=UTF-8">', $data);
        }

        if (preg_match('#<meta charset="utf-8"[^>]*>#is', $data)) {
            $data = preg_replace('#<meta charset="utf-8"[^>]*>#is', '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">', $data);
        }
        if (empty($data) || strlen($data) > 1048576){
            return false;
        } else {
            return $data;
        }
    } else {
        return false;
    }

}


/**
 * 获取 DOMDocument 对象
 * @param str $url
 * @return boolean|DOM
 */
function getDom($url) {
    $html_content = curl_get($url);
    if(empty($html_content)) {
        return false;
    }
    $dom = new DOMDocument('1.0', 'utf-8');
    libxml_use_internal_errors(true);
    $dom->loadHTML($html_content);
    return $dom;
}