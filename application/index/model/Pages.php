<?php namespace model;
/** * 自定义分页类,主要用于产生分页试图 * Class Common * @package App\Library */
class Pages
{
    /** * @param $text * @return string */
    public static function getActivePageWrapper($text)
    {
        return '<li><span>' . $text . '</span></li>';
    }

    /** * 获取当前页按钮的页面样式 * @param $url * @param $page * @return string */
    public static function getActivePageLinkWrapper($url, $page)
    {
        return '<li  class="active page"><a data-page="' . $url . '">' . $page . '</a></li>';
    }

    /** * 获取非当前页按钮的页面样式 * @param $url * @param $page * @return string */
    public static function getPageLinkWrapper($url, $page)
    {
        return '<li class="page"><a data-page="' . $url . '">' . $page . '</a></li>';
    }

//    /** * 获取整个的分页样式 * @param $nowPage 当前页 * @param $totalPage 共多少页面 * @param $baseUrl 当前url * @param $search 搜索 * @return string */
//    public static function getSelfPageView($nowPage, $totalPage, $baseUrl, $search,$total,$perpage)
//    {
//        if (!$perpage){
//            $perpage=10;
//        }
//        $pagePre = '<ul  class=" pagination pull-right ">';
//        $pageEnd = '</ul>';
//        $pageLastStr = '';
//        $pageNextStr = '';
//        if ($nowPage <= 1) {
//            $nowPage = 1;
//            $pageLastStr = '<li class="disabled"><span>上一页</span></li>';
//            //«
//        }
//        if ($nowPage >= $totalPage) {
//            $nowPage = $totalPage;
//            $pageNextStr = '<li class="disabled"><span>下一页</span></li>';
//            //»
//        }
//        $search['totalPage'] = $totalPage;
//        if (empty($pageLastStr)) {
//            $lastPage = $nowPage - 1;
//            $search['nowPage'] = $lastPage;
//            $lastSearchStr = self::arrayToSearchStr($search);
//            $url = $baseUrl . '?' . $lastSearchStr;
//            $pageLastStr = self::getPageLinkWrapper($lastPage, '上一页');
//        }
//        if (empty($pageNextStr)) {
//            $pageNext = $nowPage + 1;
//
//            $search['nowPage'] = $pageNext;
//            $lastSearchStr = self::arrayToSearchStr($search);
//            $url = $baseUrl . '?' . $lastSearchStr;
//            //$url=$baseUrl;
//            //dd($url);
//            //TODO 这里下一页为 “»”;
//            $pageNextStr = self::getPageLinkWrapper($pageNext, '下一页');
//        }
//        $pageTemp = '';
//        $pageRange = self::getPageRange($nowPage, $totalPage);
//        // dd($pageRange);
//        $pageTemp .= $pageLastStr;
//
//        foreach ($pageRange as $page) {
//            $search['nowPage'] = $page;
//
//            $searchStr = self::arrayToSearchStr($search);
//            $url = $baseUrl . '?' . $searchStr;
//
//            if ($page == $nowPage) {
//                $pageTemp .= self::getActivePageLinkWrapper($page, $page);
//
//            } else {
//                $pageTemp .= self::getPageLinkWrapper($page, $page);
//            }
//        }
//        $pageTemp .= $pageNextStr;
//        $select='selected="selected" ';
//        $select1='';
//        $select2='';
//        $select3='';
//
//        switch ($perpage){
//            case 10:
//                $select1=$select;
//                break;
//            case 20:
//                $select2=$select;
//                break;
//            case 30:
//                $select3=$select;
//                break;
//        }
//        $totalnum='<ul class="user-main-page-total clearfix">
//                        <li>
//                            <a title="">共'.$total.'条</a>
//                        </li>
//                        <li>
//                            <a title="">共'.$totalPage.'页</a>
//                        </li>
//                    </ul>';
//        $display='<ul class="user-main-page-show clearfix">
//                        <li>
//                            <a title="">每页显示</a>
//                        </li>
//                        <li>
//                            <a class="user-main-page-show-sel">
//                                <select name="perpage" id="perpage">
//                                    <option '.$select1.' value="10">10</option>
//                                    <option '.$select2.' value="20">20</option>
//                                    <option '.$select3.' value="30">30</option>
//                                </select>
//                            </a>
//                        </li>
//                        <li>
//                            <a title="">条</a>
//                        </li>
//                    </ul>';
//
//        $pageView = $pagePre . $totalnum.$pageTemp . $pageEnd;
//
//        return  $pageView;
//    }

    /**
     * 获取分页模板
     *
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     * @param $nowPage          int 当前所在页数
     * @param $totalPage        int 数据总页数
     * @param $baseUrl          int 当前所在页数
     * @param $search           int 当前所在页数
     * @param $total            int 当前所在页数
     * @param $perpage          int 当前所在页数
     * @return mixed            int 当前所在页数
     */
    public function getSelfPageView($nowPage, $totalPage, $baseUrl, $search,$total,$perpage)
    {

        if (!$perpage){
            $perpage=10;
        }
        $pageStart = '<div class="eui-page" eui="text-right">';//分页模板开头
        $pageEnd = '</div>';//分页模板结尾
        $pagePrevStr = '';//上一页
        $pageNextStr = '';//下一页
        $pageStartStr = '<a class="eui-page-first" data-pages-num="1" href="javascript:;">首页</a>';//首页
        $pageEndStr = '<a class="eui-page-last" data-pages-num="'.$totalPage.'" href="javascript:;">末页</a>';//末页
        if ($nowPage <= 1) {
            $nowPage = 1;
        }
        else
        {
            $prevPage = $nowPage - 1;
            $search['nowPage'] = $prevPage;
            $lastSearchStr = self::arrayToSearchStr($search);
            $url = $baseUrl . '?' . $lastSearchStr;
            $pagePrevStr = self::get_other_page($prevPage, '上一页');
        }

        if ($nowPage >= $totalPage) {
            $nowPage = $totalPage;
        }
        else
        {
            $pageNext = $nowPage + 1;
            $search['nowPage'] = $pageNext;
            $lastSearchStr = self::arrayToSearchStr($search);
            $url = $baseUrl . '?' . $lastSearchStr;
            $pageNextStr = self::get_other_page($pageNext, '下一页');
        }

        $search['totalPage'] = $totalPage;
//        if (empty($pagePrevStr)) {
//            $prevPage = $nowPage - 1;
//            $search['nowPage'] = $prevPage;
//            $lastSearchStr = self::arrayToSearchStr($search);
//            $url = $baseUrl . '?' . $lastSearchStr;
//            $pagePrevStr = self::get_other_page($prevPage, '上一页');
//        }
//        if (empty($pageNextStr)) {
//            $pageNext = $nowPage + 1;
//            $search['nowPage'] = $pageNext;
//            $lastSearchStr = self::arrayToSearchStr($search);
//            $url = $baseUrl . '?' . $lastSearchStr;
//            $pageNextStr = self::get_other_page($pageNext, '下一页');
//        }
        $pageTemp = '';
        $pageRange = self::getPageRange($nowPage, $totalPage);
        $pageTemp .= $pagePrevStr.$pageStartStr;

        foreach ($pageRange as $page) {
            $search['nowPage'] = $page;
            $searchStr = self::arrayToSearchStr($search);
            $url = $baseUrl . '?' . $searchStr;
            if ($page == $nowPage)
                $pageTemp .= self::get_active_page($page, $page);
            else
                $pageTemp .= self::get_other_page($page, $page);
        }
        $pageTemp .= $pageEndStr.$pageNextStr;
        $arrPerPage = [15,30,50,100];//选择每页显示条数
        $selectPageOption = '';
        foreach ($arrPerPage as $value)
        {
            if($perpage == $value)
                $selectPageOption .= '<option selected="selected" value="'.$value.'">'.$value.'</option>';
            else
                $selectPageOption .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $selectPage = '<select class="eui-select">'.$selectPageOption.'</select>';

        $pageTotalStr = '<span class="z-page-total">共<i>'.$total.'</i>条数据</span><span class="z-page-total">共<i>'.$totalPage.'</i>页</span>';
        $pageDisplay='<div class="z-page-data" eui="pull-left">
                <span>每页显示</span>'
            .$selectPage.
            '<span>条</span>'
            .$pageTotalStr.
            '</div>';
        $pageView = $pageStart . $pageDisplay.$pageTemp . $pageEnd;
        return  $pageView;

    }

    /** * 获取实际显示页面范围的范围 * @param $nowPage * @param $totalPage * @return array */
    public static function getPageRange($nowPage, $totalPage)
    {
        $returnArray = [];
        if ($totalPage <= 5) {
            for ($i = 1; $i <= $totalPage; $i++) {
                $returnArray[] = $i;
            }
        } else {
            $lengthLeft = $nowPage - 1;
            $lengthRight = $totalPage - $nowPage;
            if (($lengthLeft < 2) && ($lengthRight < 2)) {
                $returnArray = [];
            } elseif (($lengthLeft < 2) && ($lengthRight > 2)) {
                for ($i = 1; $i <= 5; $i++) {
                    $returnArray[] = $i;
                }
            } elseif (($lengthLeft > 2) && ($lengthRight < 2)) {
                $start = $totalPage - 4;
                for ($i = $start; $i <= $totalPage; $i++) {
                    $returnArray[] = $i;
                }
            } else {
                for ($i = $nowPage - 2; $i <= $nowPage + 2; $i++) {
                    $returnArray[] = $i;
                }
            }
        }

        return $returnArray;
    }

    /** * 将搜索的数组拼接成为url * 注意：PHP的内置函数http_build_query，会自动将没有值的参数清除，导致blade模板报错 * @param $array * @return string */
    public static function arrayToSearchStr($array)
    {
        $fields_string = '';
        reset($array);
        end($array);
        $lastKey = key($array);
        reset($array);
        foreach ($array as $key => $value) {
            if ($key != $lastKey) {
                $fields_string .= $key . '=' . $value . '&';
            } else {
                $fields_string .= $key . '=' . $value;
            }
        }
        rtrim($fields_string, '&');
        return $fields_string;
    }

    /**
     * 获取当前页按钮的页面样式
     *
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     * @param $url
     * @param $page
     * @return string
     */
    public static function get_active_page($url, $page)
    {
        return '<a data-pages-num="'.$url.'" class="active" href="javascript:;">'.$page.'</a>';
    }

    /**
     * 获取非当前页按钮的页面样式
     *
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     * @param $url
     * @param $page
     * @return string
     */
    public static function get_other_page($url, $page)
    {
        return '<a data-pages-num="'.$url.'" href="javascript:;">'.$page.'</a>';
    }
    public static function get_other_page_prev($url, $page)
    {
        return '<a class="eui-page-prev" data-pages-num="'.$url.'" href="javascript:;">'.$page.'</a>';
    }
    public static function get_other_page_next($url, $page)
    {
        return '<a class="eui-page-next" data-pages-num="'.$url.'" href="javascript:;">'.$page.'</a>';
    }

    /**
     * 获取分页模板
     *
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     * @param $nowPage          int 当前所在页数
     * @param $totalPage        int 数据总页数
     * @param $baseUrl          int 当前所在页数
     * @param $search           int 当前所在页数
     * @param $total            int 当前所在页数
     * @param $perpage          int 当前所在页数
     * @return mixed            int 当前所在页数
     */
    public static function get_page_view($nowPage, $totalPage, $baseUrl, $search,$total,$perpage)
    {
        $nowPage = intval($nowPage);
        $totalPage = intval($totalPage);
        $total = intval($total);
        $perpage = intval($perpage);

        if (!$perpage){
            $perpage=10;
        }
        $pageStart = '<div class="eui-page" eui="text-right">';//分页模板开头
        $pageEnd = '</div>';//分页模板结尾
        $pagePrevStr = '';//上一页
        $pageNextStr = '';//下一页
        $pageStartStr = '<a class="eui-page-first" data-pages-num="1" href="javascript:;">首页</a>';//首页
        $pageEndStr = '<a class="eui-page-last" data-pages-num="'.$totalPage.'" href="javascript:;">末页</a>';//末页
        if ($nowPage <= 1) {
            $nowPage = 1;
        }
        else
        {
            $prevPage = $nowPage - 1;
            $search['nowPage'] = $prevPage;
            $lastSearchStr = self::arrayToSearchStr($search);
            $url = $baseUrl . '?' . $lastSearchStr;
            $pagePrevStr = self::get_other_page_prev($prevPage, '上一页');
        }

        if ($nowPage >= $totalPage) {
            $nowPage = $totalPage;
        }
        else
        {
            $pageNext = $nowPage + 1;
            $search['nowPage'] = $pageNext;
            $lastSearchStr = self::arrayToSearchStr($search);
            $url = $baseUrl . '?' . $lastSearchStr;
            $pageNextStr = self::get_other_page_next($pageNext, '下一页');
        }

        $search['totalPage'] = $totalPage;
//        if (empty($pagePrevStr)) {
//            $prevPage = $nowPage - 1;
//            $search['nowPage'] = $prevPage;
//            $lastSearchStr = self::arrayToSearchStr($search);
//            $url = $baseUrl . '?' . $lastSearchStr;
//            $pagePrevStr = self::get_other_page($prevPage, '上一页');
//        }
//        if (empty($pageNextStr)) {
//            $pageNext = $nowPage + 1;
//            $search['nowPage'] = $pageNext;
//            $lastSearchStr = self::arrayToSearchStr($search);
//            $url = $baseUrl . '?' . $lastSearchStr;
//            $pageNextStr = self::get_other_page($pageNext, '下一页');
//        }
        $pageTemp = '';
        $pageRange = self::getPageRange($nowPage, $totalPage);
        $pageTemp .= $pageStartStr.$pagePrevStr;

        foreach ($pageRange as $page) {
            $search['nowPage'] = $page;
            $searchStr = self::arrayToSearchStr($search);
            $url = $baseUrl . '?' . $searchStr;
            if ($page == $nowPage)
                $pageTemp .= self::get_active_page($page, $page);
            else
                $pageTemp .= self::get_other_page($page, $page);
        }
        $pageTemp .= $pageNextStr.$pageEndStr;
        $arrPerPage = [5,15,30,50,100];//选择每页显示条数
        $selectPageOption = '';
        foreach ($arrPerPage as $value)
        {
            if($perpage == $value)
                $selectPageOption .= '<option selected="selected" value="'.$value.'">'.$value.'</option>';
            else
                $selectPageOption .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $selectPage = '<select class="eui-select">'.$selectPageOption.'</select>';

        $pageTotalStr = '<span class="z-page-total">共<i>'.$total.'</i>条数据</span><span class="z-page-total">共<i>'.$totalPage.'</i>页</span>';
        $pageDisplay='<div class="z-page-data" eui="pull-left">
                <span>每页显示</span>'
            .$selectPage.
            '<span>条</span>'
            .$pageTotalStr.
            '</div>';
        $pageView = $pageStart . $pageDisplay.$pageTemp . $pageEnd;
        return  $pageView;

    }
}

