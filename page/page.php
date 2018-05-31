<?php

namespace app\common\lib;


class Fun
{
    // //获取上传token
    // public static function uploadToken($url,$login_token,$data,$xApp)
    // {
    //     //  $login_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOjE0LCJhZ2VudCI6ImRlZmF1bHQiLCJ0aW1lIjoxNTI2MjgxNDI2LCJleHBpcmUiOjE1NTg2ODE0MjYsInNjb3BlIjoiYXBwIn0.bKDjnqI3XjPo1uv7WkWHC-TQM8OxucueafeJOMT7BAc';
    //     //  $api = 'http://upload.com/index.php/api/upload/token';
    //         $heaher = ['x-app'=>$xApp,'x-token'=>$login_token,'Content-Type'=>'application/json;charset=utf-8'];
    //     //  $http = Utils::brower()  ;
    //     //  $data = ['type'=>'image'];
    //         $rs = Utils::brower()->post($url,$heaher,json_encode($data));
    //     //  dump($rs->getContent());
    //     return $rs->getContent();
    // }

    /**
     * 分页
     * @param $pageSize 分页显示条数
     * @param $total    数据总数
     * @param $showPage    显示页码
     * @param $url    跳转地址
     * @param $page    页码
     * @return string
     */
    public static function page($url,$pageSize,$total,$page,$showPage=5){
        // $pageSize = 10; //显示条数
        // $total = xxx;   //数据总数
        $total_pages = ceil($total/$pageSize);   //总页数
        // $showPage = 5; //显示页码
        $page_banner = "<div class='page'>";
        //计算偏移量
        $pageoffset = ($showPage-1)/2;
        if($page > 1){
            $page_banner .= "<a href='".$url."?p=1'>首页</a>";
            $page_banner .= "<a href='".$url."?p=".($page-1)."'>上一页</a>";
        }else{
            $page_banner .= "<span class='disable'>首页</span>";
            $page_banner .= "<span class='disable'>上一页</span>";
        }
        //初始化数据
        $start = 1;
        $end = $total_pages;
        if($total_pages > $showPage){
            if($page > $pageoffset + 1){
                $page_banner .= '...';
            }
            if($page > $pageoffset){
                $start = $page - $pageoffset;
                $end = $total_pages > $page+$pageoffset ? $page+$pageoffset : $total_pages;
            }else{
                $start = 1;
                $end = $total_pages > $showPage ? $showPage : $total_pages;
            }
            if($page + $pageoffset > $total_pages){
                $start = $start - ($page + $pageoffset - $end);
            }
        }

        for($i = $start; $i <= $end; $i++){
            if($page == $i){
                $page_banner .= "<span class='current'>{$i}</span>";
            }else{
                $page_banner .= "<a href='".$url."?p=".$i."'>{$i}</a>";
            }
        }

        //尾部省略
        if($total_pages > $showPage && $total_pages > $page + $pageoffset){
            $page_banner .= '...';
        }
        if($page < $total_pages){
            $page_banner .= "<a href='".$url."?p=".($page+1)."'>下一页</a>";
            $page_banner .= "<a href='".$url."?p=".$total_pages."'>尾页</a>";
        }else{
            $page_banner .= "<span class='disable'>下一页</span>";
            $page_banner .= "<span class='disable'>上一页</span>";
        }
        $page_banner .= "共{$total_pages}页";
        $page_banner .= "<form action='' method='get'>";
        $page_banner .= "到第<input type='text' size='2' name='p'>页";
        $page_banner .= "<input type='submit' value='确定'>";
        $page_banner .= "</form></div>";
        return $page_banner;
    }
}