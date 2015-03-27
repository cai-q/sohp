<?php
/**
 * Author: cai.q@foxmail.com
 * QQ:     370361272
 * Date:   2015/3/17
 * Time:   15:23
 */

class Period extends CI_Controller{

    public function index()
    {
        $start = date('Ymd',strtotime("-30 day"));
        $end = date('Ymd');

        $this->between("www", $start, $end);
    }

    public function overview($site)
    {
        if(!isset($site)) $site = $_GET['site'];
        $start = date('Ymd',strtotime("-30 day"));
        $end = date('Ymd');

        $this->between($site, $start, $end);
    }

    public function between($site, $start, $end){
        if(!isset($site)) $site = $_GET['site'];
        if(!isset($start)) $start = $_GET['start'];
        if(!isset($end)) $end = $_GET['end'];

        $web_list = $this->config->item('web_list');
        $this->load->library('Webanalyse');
        $data = array();
        $data['info'] = json_encode($this->webanalyse->period($site, $start, $end));
        $data['site'] = $site;
        $data['site_name'] = $web_list[$site]['name'];
        $data['site_url'] = $web_list[$site]['url'];

        $data['trueday'] = date('Ymd');
        $data['trueday_string'] = substr($data['trueday'], 0, 4) . "年" . substr($data['trueday'], 4, 2) . "月" . substr($data['trueday'], 6) . "日";

        $data['tomonth'] = date('Ym');

        $data['startday'] = $start;
        $data['startday_string'] = substr($start, 0, 4) . "年" . substr($start, 4, 2) . "月" . substr($start, 6) . "日";

        $data['endday'] = $end;
        $data['endday_string'] = substr($end, 0, 4) . "年" . substr($end, 4, 2) . "月" . substr($end, 6) . "日";

        $data['web_list'] = $web_list;

//        $this->output->cache(60);
        $this->load->view('period', $data);
    }
}