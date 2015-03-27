<?php
/**
 * Author: cai.q@foxmail.com
 * QQ:     370361272
 * Date:   2015/3/9
 * Time:   8:08
 */

class Webanalyse {

    public function period($site, $start, $end)
    {
        $data = $this->get_period_data_nums($site, $start, $end);
        $result = array();

        $labels = array();
        $datasets = array();
        $data_nums = array();

        $prev = NULL;
        foreach ($data as $key => $value)
        {
            $labels[] = $key;
            $data_nums[] = $value;
        }

        $datasets[] = array(
            'label' => "更新量趋势",
            'fillColor' => 'rgba(220, 220, 220, 0.5)',
            'strokeColor' => 'rgba(192, 57, 43, 1)',
            'pointColor' => 'rgba(220, 220, 220, 1)',
            'pointStrokeColor' => '#fff',
            'data' => $data_nums,
        );

        $result['labels'] = $labels;
        $result['datasets'] = $datasets;

        return $result;
    }

    public function monthly_overview($site, $month)
    {
        $data = $this->get_monthly_data_nums($site, $month);
        $result = array();

        $labels = array();
        $datasets = array();
        $data_nums = array();

        $prev = NULL;
        foreach ($data as $key => $value)
        {
            $labels[] = $key;
            $data_nums[] = $value;
        }

        $datasets[] = array(
            'label' => "更新量趋势",
            'fillColor' => 'rgba(220, 220, 220, 0.5)',
            'strokeColor' => 'rgba(192, 57, 43, 1)',
            'pointColor' => 'rgba(220, 220, 220, 1)',
            'pointStrokeColor' => '#fff',
            'data' => $data_nums,
        );

        $result['labels'] = $labels;
        $result['datasets'] = $datasets;

        return $result;
    }

    public function daily_overview($site, $day)
    {
        $data = $this->get_daily_data($site, $day);
        $result = array();

        $labels = array();
        $datasets = array();
        $data_nums = array();

        $prev = NULL;
        foreach ($data as $key => $value)
        {
            $current = $value;
            if(!$prev)
            {
                $prev = $current;
                continue;
            }
            $key = substr($key, 0, 2) . ':' . substr($key, 2);
            $labels[] = $key;

            $data_nums[] = $this->compare_data_new_num($prev, $current);

            $prev = $current;
        }

        $datasets[] = array(
            'label' => "更新量趋势",
            'fillColor' => 'rgba(200, 200, 200, 0.5)',
            'strokeColor' => 'rgba(192, 57, 43, 1)',
            'pointColor' => 'rgba(220, 220, 220, 1)',
            'pointStrokeColor' => '#000',
            'data' => $data_nums,
        );

        $result['labels'] = $labels;
        $result['datasets'] = $datasets;

        return $result;
    }

    private function compare_data_new_num($prev, $curr)
    {
        $count = 0;
        foreach ($curr as $key => $value)
        {
            if (!array_key_exists($key, $prev))
            {
                $count ++;
            }
        }

        return $count;
    }

    private function compare_data_new($prev, $curr)
    {
        return array_diff_key($curr, $prev);
    }

    public function get_daily_data($site, $day)
    {
        $day_list = scandir(FCPATH . '/results/' . $site . '/anchors');
        if (!in_array($day, $day_list))
        {
            return '指定日期数据不存在。';
        }

        for ($i = 0; $i < count($day_list); $i++)
        {
            if ($day_list[$i] == $day)
            {
                $result_list = array();
                $yesterday_last_snapshot = array();
                $today_snapshot_list = scandir(FCPATH . '/results/' . $site . '/anchors/' . $day_list[$i]);

                if (!strstr($day_list[$i - 1], '.'))
                {
                    $yesterday_snapshot_list = scandir(FCPATH . '/results/' . $site . '/anchors/' . $day_list[$i - 1]);
                    if (strstr(end($yesterday_snapshot_list), '.json'))
                    {
                        $yesterday_last_snapshot[] = end($yesterday_snapshot_list);
                        $result_list['-1'] = json_decode(file_get_contents(FCPATH . '/results/' . $site . '/anchors/' . $day_list[$i - 1] . '/' . end($yesterday_snapshot_list)), TRUE);
                    }
                }

                for ($j = 0; $j < count($today_snapshot_list); $j++)
                {
                    if (strstr($today_snapshot_list[$j], '.json'))
                    {
                        $key = str_replace('.json', '', $today_snapshot_list[$j]);
                        $result_list[$key] = json_decode(file_get_contents(FCPATH . '/results/' . $site . '/anchors/' . $day_list[$i] . '/' . $today_snapshot_list[$j]), TRUE);
                    }
                }
                return $result_list;
            }
        }
    }

    public function get_monthly_data_nums($site, $month)
    {
        $result_list = array();

        $dir_list = scandir(FCPATH . '/results/' . $site . '/anchors/');

        foreach ($dir_list as $dir)
        {
            if(strstr($dir, $month))
            {
                $daily_list = $this->daily_overview($site, $dir);
                $data_nums = $daily_list['datasets'][0]['data'];

                $result_list[$dir] = array_sum($data_nums);
            }
        }

        return $result_list;
    }

    public function get_period_data_nums($site, $start, $end)
    {
        $result_list = array();

        $dir_list = scandir(FCPATH . '/results/' . $site . '/anchors/');

        foreach ($dir_list as $dir)
        {
            if(strcmp($dir, $start) >= 0 && strcmp($dir, $end) <= 0)
            {
                $daily_list = $this->daily_overview($site, $dir);
                $data_nums = $daily_list['datasets'][0]['data'];

                $result_list[$dir] = array_sum($data_nums);
            }
        }

        return $result_list;
    }

    public function get_append_articles($site, $day, $time)
    {
        $data_list = $this->get_daily_data($site, $day);

        $prev = '';

        foreach ($data_list as $key => $value)
        {
            $curr = $key;

            if($key == $time)
            break;

            $prev = $curr;
        }

        $result = array_diff_key($data_list[$curr], $data_list[$prev]);

        return $result;

    }
}