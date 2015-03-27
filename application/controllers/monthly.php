<?php
/**
 * Author: cai.q@foxmail.com
 * QQ:     370361272
 * Date:   2015/3/10
 * Time:   9:18
 */

class Monthly extends CI_Controller {

    public function index()
    {
        $this->overview("www", date("Ym"));
    }

    public function overview($site, $month)
    {
        if(!isset($site)) $site = $_GET['site'];
        if(!isset($month)) $month = $_GET['month'];
        $web_list = $this->config->item('web_list');

        $this->config->item($site);
        $this->load->library('Webanalyse');
        $data = array();
        $data['info'] = json_encode($this->webanalyse->monthly_overview($site, $month));
        $data['site'] = $site;
        $data['site_name'] = $web_list[$site]['name'];
        $data['site_url'] = $web_list[$site]['url'];
        $data['tomonth'] = $month;
        $data['tomonth_string'] = substr($month, 0, 4) . "年" . substr($month, 4) . "月"  ;

        $data['trueday'] = date('Ymd');
        $data['web_list'] = $web_list;

//        $this->output->cache(60);
        $this->load->view('monthly_overview', $data);
    }
}