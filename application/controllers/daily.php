<?php
/**
 * Author: cai.q@foxmail.com
 * QQ:     370361272
 * Date:   2015/3/6
 * Time:   15:40
 */

class Daily extends CI_Controller{

    public function overview()
    {
        $site = $_GET['site'];
        $day = $_GET['day'];

        $web_list = $this->config->item('web_list');

        if (!array_key_exists($site, $web_list))
        {
            echo '站点不存在';
            return;
        }
        else
        {
            $this->load->library('Webanalyse');
            $data = array();
            $data['info'] = json_encode($this->webanalyse->daily_overview($site, $day));
            $data['site'] = $site;
            $data['site_name'] = $web_list[$site]['name'];
            $data['site_url'] = $web_list[$site]['url'];
            $data['today'] = $day;
            $data['trueday'] = date('Ymd');
            $data['today_string'] = substr($day, 0, 4) . "年" . substr($day, 4, 2) . "月" . substr($day, 6) . "日";
            $data['tomonth'] = substr($day, 0, 6);
            $data['web_list'] = $web_list;

//            $this->output->cache(10);
            $this->load->view('daily_overview', $data);
        }
    }

    public function get_append()
    {
        $site = $_GET['site'];
        $day = $_GET['day'];
        $time = $_GET['time'];


        $this->load->library('Webanalyse');
        echo json_encode($this->webanalyse->get_append_articles($site, $day, $time));
    }

}