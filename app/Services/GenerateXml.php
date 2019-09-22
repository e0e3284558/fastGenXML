<?php

namespace App\Services;


use Illuminate\Support\Arr;

class GenerateXml
{
    /**
     * 根据array生成XML
     * @param $xmlData
     */
    public function generate($filePath = '', $xmlData = [], $topLable = '', $attribute = [])
    {

        $doc = new \DOMDocument('1.0', 'utf-8');//引入类并且规定版本编码
        $grandFather = $doc->createElement($topLable);//创建节点
        if (Arr::has($attribute, $topLable)) {
            $grandFather->setAttribute($attribute[$topLable]['key'], $attribute[$topLable]['value']);//给Grandfather增加ID属性
        }
        foreach ($xmlData as $data) {
            foreach ($data as $key => $val) {
                $father = $doc->createElement($key);//创建节点
                if (Arr::has($attribute, $key)) {
                    $grandFather->setAttribute($attribute[$key]['key'], $attribute[$key]['value']);//给Grandfather增加ID属性
                }
                $grandFather->appendChild($father);//讲Father放到Grandfather下
                if (is_array($val)) {
                    // 如果是数组，递归去拆解添加对象
                    $val = $this->recursion($val, $doc, $father);
                    $father->appendChild($val);//将标签内容赋给标签
                } else {
                    $content = $doc->createTextNode($val);//设置标签内容
                    $father->appendChild($content);//将标签内容赋给标签
                }
            }
        }
        $doc->appendChild($grandFather);//创建顶级节点
        $doc->save($filePath);//保存代码
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
