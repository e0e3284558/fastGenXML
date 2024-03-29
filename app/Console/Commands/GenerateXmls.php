<?php

namespace App\Console\Commands;

use App\Models\Medical\Doctor;
use App\Models\Medical\DoctorVideo;
use App\Services\GenerateRecordXml;
use App\Services\GenerateSiteMap;
use App\Services\GenerateXml;
use Carbon\Carbon;
use Illuminate\Console\Command;
use phpDocumentor\Reflection\DocBlock\Description;

class GenerateXmls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:xml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成XML';

    /**
     * sitemap存储位置
     * @var string
     */
    protected $indexPath;

    /**
     * 生成文件存储位置
     * @var string
     */
    protected $filePath;

    /**
     * 生成文件存储位置
     * @var string
     */
    protected $recordPath;

    /**
     * 访问url
     * @var
     */
    protected $url;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $timestamp = Carbon::now()->timestamp;
        $this->filePath = storage_path("xml/test_{$timestamp}.xml");
        $this->indexPath = storage_path('xml/sitemap.xml');
        $this->recordPath = storage_path('xml/record.xml');
        $this->url = 'https://www.jiankang.com/';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 读取上次生成的xml last_id
        $last_id = (new GenerateRecordXml())->getLastRecord($this->recordPath);
        $doctorVideo = new DoctorVideo();
        $data = $doctorVideo->getVideoData($last_id, 1000);
        $list = [];
        foreach ($data as $key => $val) {
            $list[$key]['url']['loc'] = 'www.jiankang.com/111';
            $list[$key]['url']['lastmod'] = Carbon::now()->toDateTimeString();
            $list[$key]['url']['changefreq'] = 'always';
            $list[$key]['url']['priority'] = '1.0';
            $list[$key]['url']['data']['dispaly']['headline'] = $val['title'];
            $list[$key]['url']['data']['dispaly']['waplink'] = 'www.jiankang.com/111';
            $list[$key]['url']['data']['dispaly']['pclink'] = 'www.jiankang.com/11111';
            $list[$key]['url']['data']['dispaly']['summary'] = $val['info']['content'];
            $list[$key]['url']['data']['dispaly']['vedio_pic'] = $val['info']['doc_img'];
            $list[$key]['url']['data']['dispaly']['vedio_time'] = $val['info']['videolong'];
            $list[$key]['url']['data']['dispaly']['doctor'] = $val['doctor']['realname'];
            $list[$key]['url']['data']['dispaly']['hospital'] = $val['hospital']['name'];
            $list[$key]['url']['data']['dispaly']['doctor_title'] = $val['doctor']['config']['name'];
            $list[$key]['url']['data']['dispaly']['disease'] = $val['jibin_name'];
            $list[$key]['url']['data']['dispaly']['pv'] = $val['watch_num'];
            $list[$key]['url']['data']['dispaly']['tag'] = $val['jibin_name'];
            $list[$key]['url']['data']['dispaly']['agree_cnt'] = $val['good_num'];
            $list[$key]['url']['data']['dispaly']['discuss_cnt'] = '评论数';
            $list[$key]['url']['data']['dispaly']['label'] = '治疗;病因;诊断;饮食';
            $list[$key]['url']['data']['dispaly']['from'] = '百姓健康网';
            $list[$key]['url']['data']['dispaly']['time'] = Carbon::createFromTimestamp($val['add_time'])->toDateTimeString();
            $list[$key]['url']['data']['dispaly']['time'] = Carbon::createFromTimestamp($val['upd_time'])->toDateTimeString();
            $list[$key]['url']['data']['dispaly']['asd']['showurl'] = 'https://asdasd.com';
            $list[$key]['url']['data']['dispaly']['asd']['mipurl'] = 'https://asdasd.com';
        }

        $attribute = [
            'urlset' => [
                'key' => 'content_method',
                'value' => 'full'
            ],
        ];
        $topLable = 'urlset';
        $xml = new GenerateXml();
        $xml->generate($this->filePath, $list, $topLable, $attribute);

        // 生成sitemap
        $map = [];
        $map['sitemap']['loc'] = $this->url . $this->filePath;
        $map['sitemap']['lastmod'] = Carbon::now()->toDateString();
        $map['sitemap']['count'] = count($data);

        $mapXml = new GenerateSiteMap();
        $topLable = 'sitemapindex';
        $mapXml->generate($this->indexPath, $map, $topLable, $attribute);


        // 生成记录
        $record = [];
        $record['record'] = [
            'path' => $this->filePath,
            'loc' => $this->url . $this->filePath,
            'create_time' => Carbon::now()->toDateTimeString(),
            'count' => count($data),
            'last_id' => last($data)['id'],
            'last_element_create_time' => Carbon::createFromTimestamp(last($data)['add_time'])->toDateTimeString()
        ];
        $recordXml = new GenerateRecordXml();
        $recordXml->generate($this->recordPath, $record, count($data));
    }
}
