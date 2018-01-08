<?php

namespace App\Classes;

use PHPHtmlParser\Dom;
use GuzzleHttp\Client;

class Girl {

    private static $baseUrl = 'http://www.mmjpg.com';
    private static $downloadDir = 'girls/';

    public static function findGirl() {
        $dom = new Dom;
        //查找总数
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $client = new Client(['base_uri' => self::$baseUrl, 'cookies' => true]);
        $response = $client->request('GET', '', ['cookies' => $jar]);
        $dom->load((string) $response->getBody());
        $info = $dom->find('.info')->text;
        preg_match('/\d+/', $info, $pageCountArr); //总页数
        $firstPageLi = $dom->find('li'); //第一页
        $firstCount = count($firstPageLi); //第一页数量
        $dom->load(self::$baseUrl . "/home/{$pageCountArr[0]}");
        $lastPageLi = $dom->find('li'); //最后一页
        $lastCount = count($lastPageLi); //最后一页数量
        $allCount = $firstCount * ($pageCountArr[0] - 1) + $lastCount; //总数
        $tarentIndex = rand(1, $allCount);
        //$tarentUrl = $tarentIndex > $firstCount ? self::$baseUrl . "/home/{$tarent}" : self::$baseUrl;
        $page = ceil($tarentIndex / $firstCount);
        $index = $tarentIndex % $firstCount;
//        echo "第一页有{$firstCount}个,最后一页有{$lastCount}个\n\r";
//        echo "第{$tarentIndex}个\n\r";
//        echo "第{$page}页的第{$index}个\n\r";
        $tarentUrl = $page == 1 ? self::$baseUrl : self::$baseUrl . "/home/{$page}";
        $dom->load($tarentUrl);
        $tarentLi = $dom->find('li');
        //echo $tarentLi->innerHtml . "\n\r";
        $dom->loadStr($tarentLi->innerHtml, []);
        $imgUrl = $dom->find('img')->getAttribute('src');
        $imgUrl = str_replace('/small', '', $imgUrl);
        $imgUrl = str_replace('.jpg', '/1.jpg', $imgUrl);
        //echo $imgUrl . "\n\r";exit();
        if (!is_dir(public_path(Girl::$downloadDir))) {
            mkdir(public_path(Girl::$downloadDir));
            chmod(public_path(Girl::$downloadDir), 0777);
        }
        $fileName = uniqid() . '.' . pathinfo($imgUrl)['extension'];
        //$path = public_path($fileName);
        $path = public_path(Girl::$downloadDir) . '/' . $fileName;
        $resource = fopen($path, 'w');
        $stream = \GuzzleHttp\Psr7\stream_for($resource);
        $client->request('GET', $imgUrl, [
            'save_to' => $stream,
            'headers' => [
                'Referer' => 'http://www.mmjpg.com'
            ]
        ]);
        return $path;
//        $image = file_get_contents($imgUrl);
//        file_put_contents(public_path('girls/' . uniqid() . '.' . pathinfo($imgUrl)['extension']), $image);
    }

}
