<?php

namespace App\Services;

use Illuminate\Support\Arr;
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
        $newsitemap = $doc->createElement('sitemap');
        $sitemap = $doc->getElementsByTagName($topLable)->item(0);  //找到文件追加的位置
        $comment = $doc->createTextNode('asa');
        $newsitemap->appendChild($comment);
        $sitemap->appendChild($newsitemap);
        $doc->appendChild($sitemap);
        $doc->save($filePath);
    }


    public function fileExists($filePath, $topLable)
    {
        if (!Storage::exists($filePath)) {
            $doc = new \DOMDocument('1.0', 'utf-8');//引入类并且规定版本编码
            $grandFather = $doc->createElement($topLable);//创建节点
            $content = $doc->createTextNode('');//创建节点
            $grandFather->appendChild($content);//创建节点
            $doc->appendChild($grandFather);//创建顶级节点
            $doc->save($filePath);//直接存储路径
        }
    }


    /**
     * 递归处理数组类型的xmls
     * @param array $list
     * @param $dom
     * @param $father
     * @return mixed
     */
    public function recursion($list = [], &$dom, &$father)
    {
        foreach ($list as $key => $item) {
            $son = $dom->createElement($key);
//            if (Arr::has($attribute, $key)) {
//                $grandFather->setAttribute($attribute[$key]['key'],$attribute[$key]['value']);//给Grandfather增加ID属性
//            }
            $father->appendChild($son);
            if (is_array($item)) {
                $item = $this->recursion($item, $dom, $son);
                $son->appendChild($item);
                $father->appendChild($son);
            } else {
                $item = $dom->createTextNode($item);
                $son->appendChild($item);
                $father->appendChild($son);
            }
        }
        return $son;
    }
}
