<?php
namespace App\Services;

use Sunra\PhpSimple\HtmlDomParser;


class CrawlerFromWebsiteService
{
    private $url;

    public function __construct($link)
    {
        $this->url = $link;
    }

    private function getDom($link)
    {
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);
        $dom = HtmlDomParser::str_get_html($content);

        return $dom;
    }

    public function getList()
    {
        $dom = $this->getDom($this->url);
        $count = 16;
        foreach ($dom->find('//*[@id="wallpapers"]/div/article[*]/figure/a/img') as $link) {
            $userImage = 'abstract_'. $count;
            $path = '/home/likewise-open/FRAMGIA/nguyen.quang.duy/Google_driver/Abstract/';
            $link_image = str_replace(" ","%20", preg_replace( "/-\d+x\d+/", "", $link->src));
            $ch = curl_init($link_image);
            $fp = fopen($path . $userImage, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $result = curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            $count++;
        }
    }
}
