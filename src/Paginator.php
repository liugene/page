<?php

namespace linkphp\page;

use Config;

class Paginator
{

    /**
     * @param int $_page GET获取分页ID
     */
    protected $page; //获取分页ID

    /**
     * @param int $_total 计算分页总数
     */
    protected $total; //获取分页数据总数

    /**
     * @param int $_showpage 可选参数默认为10 每页显示的条数
     */
    protected $show_page; //每页显示数目

    /**
     * @param int $_total_pages 计算当前数据总共分页 ceil($_total/$_showpage) 分页总数除分页条数
     */
    protected $total_pages; //分页数

    /**
     * @param string $uri 自动获取当前地址
     */
    protected $uri; //自动获取URL

    /** @var array 一些配置 */
    protected $options = [
        'var_page' => 'page',
        'path'     => '/',
        'query'    => [],
        'fragment' => '',
    ];

    function __construct($total, $show_page = 10, $query = "")
    {
        $this->total = $total;
        $this->show_page = $show_page;
        $this->total_pages = ceil($this->total / $this->show_page);
        $this->page = $this->getCurrentPage() ? $this->getCurrentPage() : '1';
        $this->uri = $this->getUri($query);
    }

    private function getCurrentPage()
    {
        $page = (int) input($this->options['var_page']);
        if (filter_var($page, FILTER_VALIDATE_INT) !== false && $page >= 1) {
            return $page;
        }

        return false;
    }

    //获取url
    private function getUri($query)
    {
        //获取当前请求URL
        $request_uri = request()->server('REQUEST_URI');
        //匹配当前URL内是否存在? 不存在添加?
        $url = strstr($request_uri, '?') ? $request_uri : $request_uri . '?';
        //判断传入的参数是否为数组
        if (is_array($query)) //是数组
            $url .= http_build_query($query);
        else
            if ($query != "")
                $url .= "&" . trim($query, "?&");
        $arr = parse_url($url);
        if (isset($arr["query"])) {
            parse_str($arr["query"], $arrs);
            unset($arrs["page"]);
            $url = $arr["path"] . '?' . http_build_query($arrs);
        }
        if (strstr($url, '?')) {
            if (substr($url, -1) != '?')
                $url = $url . '&';
        } else {
            $url = $url . '?';
        }
        return $url;
    }
    //设置limit
    public function limit()
    {
        return ($this->page - 1) * $this->show_page . ',' . ($this->show_page);
    }

    //显示首页
    protected function first()
    {
        if ($this->page > 1) {
            $pagebanner = "&nbsp;&nbsp;<a href=" . $this->uri . $this->options['var_page'] . '=' . 1 . ">首页</a>&nbsp;&nbsp;";
            $pagebanner .= "&nbsp;&nbsp;<a href=" . $this->uri . $this->options['var_page'] . '=' . ($this->page - 1) . ">上一页</a>&nbsp;&nbsp;";
            return $pagebanner;
        } else {
            $pagebanner = '&nbsp;&nbsp;首页&nbsp;&nbsp;';
            $pagebanner .= '&nbsp;&nbsp;上一页&nbsp;&nbsp;';
            return $pagebanner;
        }
    }
    //显示末页
    protected function end()
    {
        if ($this->page != $this->total_pages) {
            $pagebanner = "&nbsp;<a href=" . $this->uri . $this->options['var_page'] . '=' . ($this->page + 1) . ">下一页</a>&nbsp;&nbsp;";
            $pagebanner .= "&nbsp;<a href=" . $this->uri . $this->options['var_page'] . '=' . ($this->total_pages) . ">末页</a>&nbsp;&nbsp;";
            return $pagebanner;
        } else {
            $pagebanner = '&nbsp;&nbsp;下一页&nbsp;&nbsp;';
            $pagebanner .= '&nbsp;&nbsp;末页&nbsp;&nbsp;';
            return $pagebanner;
        }
    }
    //显示分页
    public function render()
    {
        $page_banner = "总记录数为:{$this->total} 页数:{$this->total_pages} " . $this->first() . $this->end();
        return $page_banner;
    }

}