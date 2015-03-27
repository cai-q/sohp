<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author: cai.q@foxmail.com
 * QQ:     370361272
 * Date:   2015/3/5
 * Time:   9:02
 */

class Webbuffer {


    /**
     * 缓存一个页面
     * 分为三个步骤
     * 1. 读页面
     * 2. 预处理，保存网页快照
     * 3. 解析页面，将页面上链接保存为json格式数据
     *
     * @param $url
     * @param $web_list
     *
     * @return bool
     */
    public function buffer($url, $site_dir)
    {
        if (!$site_dir)
            return FALSE;

        $html = file_get_contents($url);

        //marvelous regular expression
        $search = array(
            '/(src|href)=("|\')\./i',
            '/(src|href)=("|\')(?!(\.|http))/i'
        );
        $replace = array(
            '$1=$2'.$url.'/.',
            '$1=$2'.$url.'/'
        );
        $snapshot = preg_replace($search, $replace, $html);

        $prefix = "results/" . $site_dir . '/' . "snapshots";
        date_default_timezone_set('PRC');

        $folder_name = date('Ymd');
        $file_name = date('Hi');

        $folder_path = FCPATH . '/' . $prefix . '/' . $folder_name . '/';
        $dest_path = FCPATH . '/' . $prefix . '/' . $folder_name . '/' . $file_name . '.html';

        if(!file_exists($folder_path))
        {
            mkdir($folder_path, 0777, TRUE);
        }

        //神坑之------gb2312编码，遇繁体字生僻字GG，须先转成GBK编码
        $snapshot = str_replace('gb2312', 'gbk', $snapshot);

        file_put_contents($dest_path, $snapshot);

        $this->buffer_json_encode($snapshot, $site_dir, $folder_name, $file_name);
    }

    public function buffer_one($url, $web_list)
    {
        foreach ($web_list as $web)
        {
            if($web['url'] == $url)
            {
                $site_dir = $web['dir'];
                $this->buffer($url, $site_dir);
            }
        }
    }

    public function buffer_all($web_list)
    {
        foreach ($web_list as $web)
        {
            $this->buffer($web['url'], $web['dir']);
        }
    }

    /**
     * 该函数对页面进行数据采集，并将锚点链接保存为json格式
     *
     * @param $snapshot string 网页快照
     * @param $site_dir string 站点目录
     * @param $folder_name string 文件目录
     * @param $file_name string 文件名
     */
    public function buffer_json_encode($snapshot, $site_dir, $folder_name, $file_name)
    {
        $ja = array();//json array，最后的结果数组

        $dom = new DOMDocument();
        $dom->loadHTML($snapshot);


        $a_list = $dom->getElementsByTagName('a');

        foreach ($a_list as $a)
        {
            if (!$this->valid_anchor($a))
            {
                continue;
            }
            else
            {
                $href = $a->getAttribute('href');
                $md5 = md5($href);

                if (array_key_exists($md5, $ja))
                    continue;
                if (trim($a->nodeValue) == '')
                    continue;
                else
                {
                    $ca = array();
                    $ca['title'] = $a->nodeValue;
                    $ca['href'] = $href;
                    $ja[$md5] = $ca;
                }
            }
        }

        //存储锚点链接
        $prefix = "results/" . $site_dir . '/' . "anchors";

        $folder_path = FCPATH . '/' . $prefix . '/' . $folder_name . '/';
        $dest_path = FCPATH . '/' . $prefix . '/' . $folder_name . '/' . $file_name . '.json';
        if(!file_exists($folder_path))
        {
            mkdir($folder_path, 0777, TRUE);
        }
        file_put_contents($dest_path, json_encode($ja));
    }

    /**
     * 该函数判断指定锚点是否符合采集要求
     * @param $a 传入锚点链接地址
     *
     * @return bool
     */
    private function valid_anchor($a)
    {
        //无href属性，则略过该锚点
        if (!$a->hasAttribute('href'))
            return FALSE;

        //取锚点链接地址
        $href = $a->getAttribute('href');

        //若非站内地址，略过
        if (!strstr($href, 'cnhubei'))
            return FALSE;

        //取文件名
        $last_segment = substr($href, strrpos($href, '/') + 1);

        //若最后一段（文件名）不存在，或文件名不包含数字，则略过
        if (!$last_segment OR !preg_match('/\d+/',$last_segment))
            return FALSE;

        return TRUE;
    }
}

/* End of file Webbuffer.php */