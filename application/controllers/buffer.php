<?php
/**
 * Author: cai.q@foxmail.com
 * QQ:     370361272
 * Date:   2015/3/5
 * Time:   9:40
 */

class Buffer extends CI_Controller {

    public function index()
    {
        $web_list = $this->config->item('web_list');
        $this->load->library("Webbuffer");
        $this->webbuffer->buffer_all($web_list);
    }

    public function all()
    {
        $web_list = $this->config->item('web_list');
        $this->load->library("Webbuffer");
        $this->webbuffer->buffer_all($web_list);
    }
}