<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GenerateRecordXml
{
    /**
     * 根据array生成XML
     * @param $xmlData
     */
    public function generate($filePath = '', $xmlData = [], $count = 0, $topLable = 'record')
    {
        $this->fileExists($filePath, $topLable);
        $doc = new \DOMDocument();
        $doc->load($filePath);
        $sitemap = $doc->getElementsByTagName($topLable)->item(0);  //找到文件追加的位置
        foreach ($xmlData as $key => $value) {
            $newsitemap = $doc->createElement($key);
            foreach ($value as $k => $v) {
                $elementName = $doc->createElement($k);
                $textValue = $doc->createTextNode($v);
                $elementName->appendChild($textValue);
                $newsitemap->appendChild($elementName);
            }
            $comment = $doc->createTextNode('');
            $newsitemap->appendChild($comment);
        }
        $sitemap->appendChild($newsitemap);
        $monthCount = $doc->getElementsByTagName('_' . Carbon::now()->isoFormat('YY_MM'))->item(0);
        if ($monthCount) {
            $monthCount->nodeValue = intval($monthCount->nodeValue) + $count;
        } else {
            $monthCount = $doc->getElementsByTagName('monthCount');
            $monthCountElement = $doc->createElement('_' . Carbon::now()->isoFormat('YY_MM'));
            $monthCountValue = $doc->createTextNode($count);
            $monthCountElement->appendChild($monthCountValue);
            $monthCount->item(0)->appendChild($monthCountElement);
        }
        $doc->appendChild($sitemap);
        $doc->save($filePath);
    }


    public function getLastRecord($filePath, $type = 'id', $sort = 'asc')
    {
        $xmlRecord = simplexml_load_file($filePath);
        $recordArr = json_decode(json_encode($xmlRecord), TRUE);
        $last = end($recordArr);
        if (isset($last['last_id'])) {
            $id = $last['last_id'];
        } else {
            $last1 = end($last);
            $id = $last1['last_id'] ? $last1['last_id'] : 0;
        }
        return $id;
    }


    /**
     * 判断文件是否存在，如果不存在则创建xml文件，并创建根节点
     * @param $filePath
     * @param $topLable
     * @return bool
     */
    public function fileExists($filePath, $topLable)
    {
        if (!File::exists($filePath)) {
            $doc = new \DOMDocument('1.0', 'utf-8');
            $grandFather = $doc->createElement($topLable);
            $month = $doc->createElement('monthCount');
            $grandFather->appendChild($month);
            $doc->appendChild($grandFather);
            $doc->save($filePath);
        }
        return true;
    }
}
