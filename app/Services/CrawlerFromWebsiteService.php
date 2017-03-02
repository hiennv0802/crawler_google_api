<?php
namespace App\Services;

use Sunra\PhpSimple\HtmlDomParser;

class CrawlerFromWebsiteService
{
    private $url;

    public function __construct()
    {
        $this->url = "";
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
        $count = 0;
        for($i = 8; $i > 0; $i--) {
            $dom = $this->getDom("http://iphonewalls.net/celebrities-girls/page/".strval($i)."/");
            foreach ($dom->find('//*[@id="wallpapers"]/div/article[*]/figure/a/img') as $link) {
                $tmp = explode('.', $link->src);
                $format = end($tmp);
                $userImage = 'girls_'. $count .'.' .$format;
                $path = '/home/likewise-open/FRAMGIA/nguyen.quang.duy/Google_driver/Women/';
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
}
