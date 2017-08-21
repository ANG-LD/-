<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/10
 * Time: 16:23
 */

namespace Admin\Controller;

class ChartsController extends BaseController
{
    public function index(){
        $year = date("Y");
//        $a = number_format(M('TradeRecord')->sum('amount'));  //交易总额
//        $b = number_format(M('Withdraw')->where(['status'=>3])->sum('amount'));      //提现
//        $c = number_format(M('TradeRecord')->where(['type'=>1])->sum('amount'));     //商城
//        $d = number_format(M('TradeRecord')->where(['type'=>2])->sum('amount'));    //充值
//        $e = number_format(M('TradeRecord')->where(['type'=>['not in',['1','2']]])->sum('amount')); //名师
//        $this->assign(['year'=>$year,'a'=>$a,'b'=>$b,'c'=>$c,'d'=>$d,'e'=>$e]);
        $this->assign(['year'=>$year]);
        $this->display();
    }

    public function code(){
        $a = [];//交易总额；
        $b = [];//提现；
        $c = [];//商城交易；
        $d = [];//充值；
        $e = [];//名师；
        $year = date("Y");
        $year_day = strtotime($year.'0101');
        for($i=0;$i<12;$i++){
            $start = strtotime("+{$i} month",$year_day);
            $end = $i+1;
            $end = strtotime("+{$end} month",$year_day);
            $a1 = M('TradeRecord')->where(['intime'=>['between',[$start,$end]]])->sum('amount');  //充值
            $a1 = $a1/10000;
            array_push($a,$a1);
            $b1 = M('Withdraw')->where(['status'=>3,'intime'=>['between',[$start,$end]]])->sum('amount');
            $b1 = $b1/10000;
            array_push($b,$b1);
            $c1 = M('TradeRecord')->where(['type'=>1,'intime'=>['between',[$start,$end]]])->sum('amount');
            $c1 = $c1/10000;
            array_push($c,$c1);
            $d1 = M('TradeRecord')->where(['type'=>2,'intime'=>['between',[$start,$end]]])->sum('amount');
            $d1 = $d1/10000;
            array_push($d,$d1);
            $e1 = M('TradeRecord')->where(['type'=>['not in',['1','2']],'intime'=>['between',[$start,$end]]])->sum('amount');
            $e1 = $e1/10000;
            array_push($e,$e1);
        }
        success(['a'=>$a,'b'=>$b,'c'=>$c,'d'=>$d,'e'=>$e]);
    }


    /**
     *@月活跃
     */
    public function month(){
        $month = date("Y-m");
        $this->assign(['month'=>$month]);
        $this->display();
    }

    /**
     *@月活跃
     */
    public function month_code(){
        !empty($_GET['code'])   ?  $code = strtotime(I('code')) : $code = time();
        $month = date("Y-m",$code);
        $stamp1 = strtotime($month);
        $stamp2 = strtotime("+1 month",$stamp1);
        $date_count = ($stamp2 - $stamp1)/24/3600;
        $a = [];        //活跃数据
        $first = date("d",$stamp1);
        $b = [$first];        //日期数据
        for($i=0;$i<$date_count;$i++){
            $start = strtotime("+{$i} day",$stamp1);
            $end = $i+1;
            $end = strtotime("+{$end} day",$stamp1);
//            $a1 = M('IntoApp')->where(['intime'=>['between',[$start,$end]]])
//                ->group('user_id')->count();   //某天活跃度
            $a1 = M('IntoApp')->query("select count(*) as a1 from (select count(*)  from `tk_into_app` where `intime`
                between {$start} and {$end} group by `user_id`) a ") ;
            $a1 = $a1[0]['a1'];
            $a1 = (int)($a1);
            array_push($a,$a1);
            if($i+1<$date_count){
                $next = date("d",$end);
                array_push($b,$next);
            }
        }

//        $c = M('IntoApp')->where(['intime'=>['between',[$stamp1,$stamp2]]])
//            ->group('user_id')->count(); //当月总活跃
        $c = M('IntoApp')->query("select count(*) as c from (select count(*) from `tk_into_app` where `intime`
             between $stamp1 and $stamp2 group by `user_id`) a ");
        $c = $c[0]['c'];
        $c = (int)($c);
        success(['a'=>$a,'b'=>$b,'c'=>$c]);
    }

    /**
     *@日活跃
     */
    public function day(){
        $month = date("Y-m-d");
        $this->assign(['month'=>$month]);
        $this->display();
    }

    /**
     *@日活跃
     */
    public function day_code(){
        !empty($_GET['code'])   ?  $code = strtotime(I('code')) : $code = time();
        $day = date("Y-m-d",$code);

        $stamp1 = strtotime($day);
        $stamp2 = strtotime("+1 day",$stamp1);
        $a = [];        //活跃数据
        $first = date("H:i",$stamp1);
        $b = [$first];        //日期数据
        for($i=0;$i<24;$i++){
            $start = strtotime("+{$i} hour",$stamp1);
            $end = $i+1;
            $end = strtotime("+{$end} hour",$stamp1);
            $a1 = M('IntoApp')->where(['intime'=>['between',[$start,$end]],'date'=>$day])->count();
            $a1 = (int)$a1;
            array_push($a,$a1);
            $next = date("H:i",$end);
            array_push($b,$next);
        }

/*        $c = M('IntoApp')->where(['intime'=>['between',[$stamp1,$stamp2]]])->count(); //当月总活跃*/
        $c = M('IntoApp')->query("select count(*) as c from (select count(*) from `tk_into_app` where `intime`
             between $stamp1 and $stamp2 group by `user_id`) a ");
        $c = $c[0]['c'];
        $c = (int)($c);
        success(['a'=>$a,'b'=>$b,'c'=>$c]);
    }
}