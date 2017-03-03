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
        $listLink = config('image.list_link');
        $totalPage = [];
        foreach ($listLink as $key => $value)
        {
            $domConvert = $this->getDom($value);

            $a = $domConvert->find('//*[@id="pagination"]/ul/li/a');
            $pagination = [];
            foreach ($a as $value)
            {
                $pagination[] = $value->innertext;
            }
            $lastElement = end($pagination);
            $lastIndex = array_search($lastElement, $pagination);

            $totalPage[$key] = $pagination[$lastIndex - 1];
        }

        foreach ($listLink as $key => $value)
        {
            for($i = $totalPage[$key]; $i > 1; $i--)
            {
                $dom = $this->getDom($value."page/".strval($i)."/");
                foreach ($dom->find('//*[@id="wallpapers"]/div/article[*]/figure/a/img') as $link)
                {
                    $tmp = explode('.', $link->src);
                    $format = end($tmp);
                    $userImage = $key.'_'. $count .'.' .$format;
                    $path = '/home/likewise-open/FRAMGIA/'.env('LOCAL_COMPUTER').'/Google_driver/'.$key.'/';
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
}
