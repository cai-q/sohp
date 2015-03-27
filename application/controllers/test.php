<?php
/**
 * Author: cai.q@foxmail.com
 * QQ:     370361272
 * Date:   2015/3/9
 * Time:   12:00
 */

class test extends CI_Controller{

    public function index()
    {
        echo($_GET['s']);

        $a = [1,2,3,4,5];
        $b = [1,2,3,4,5,6];

        if ($a == $b)
        {echo 'ccccc;';
        }

        $p = NULL;
        foreach($a as $v)
        {
            $c = $v;
            if(!$p)
            {
                $p = $c;
                continue;
            }
            var_dump($v);
            $p = $c;
        }
    }

}