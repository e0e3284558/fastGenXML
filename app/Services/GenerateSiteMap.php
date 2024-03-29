<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GenerateSiteMap
{
    /**
     * 根据array生成XML
     * @param $xmlData
     */
    public function generate($filePath = '', $xmlData = [], $topLable = '', $attribute = [])
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
        $doc->appendChild($sitemap);
        $doc->save($filePath);
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
            $content = $doc->createTextNode('');
            $grandFather->appendChild($content);
            $doc->appendChild($grandFather);
            $doc->save($filePath);
        }
        return true;
    }
}
