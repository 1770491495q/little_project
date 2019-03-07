<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019/2/28
 * Time: 14:23
 */

namespace app\signin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Signin extends Controller
{
    public function Index()
    {

    }
    public function doSign(Request $request)
    {
        //接收传递的参数
        $params = $request -> param();

        $return = [
            'code' =>2000,
            'msg' => '签到成功',
            'data' => []
        ];

        if (!isset($params['u_id']) || empty($params['u_id'])){
            $return =[
                'code' =>4001,
                'msg' =>'用户ID不能为空',
                'data' => []
            ];
            return json($return);
        }
        $u_id = $params['u_id'];
        //获取今天的日期
        $today = date('Y-m-d');
        //根据当前用户ID和当天的日期去数据库中查询数据
        $sign1 = Db::query('select * from record where u_id = ? and create_at = ?',[$u_id,$today]);

        if (!empty($sign1)&& $sign1[0]['create_at']==$today){
            $return =[
                'code' =>4002,
                'msg' =>'嘿兄嘚儿。不要贪心哦',
                'data' => []
            ];
            return json($return);
        }
        //根据用户ID查询签到信息
        $sign2 = Db::query('select * from record where u_id = ?',[$u_id]);

        if (empty($sign2)){//第一次签到的时候

            Db::query('insert into record (u_id,c_day,totel_scores,totel_days,create_at) values(?,?,?,?,?)',[$u_id,1,1,1,$today]);

            $return['data']['score'] = 1;
            return json($return);
        }else{
            //昨天的日期
            $last_day = date('Y-m-d',time()-3600*24);
//            var_dump($sign2[0]['create_at']);die;
            if ($last_day == $sign2[0]['create_at']){//连续签到
                //连续签到天数
                $c_days = $sign2[0]['c_day'] + 1;
            }else{
                $c_days = 1;
            }
//            var_dump($c_days);die;
            $total_scores = $sign2[0]['totel_scores'] + $c_days;
            $total_days = $sign2[0]['totel_days'] +1;

            Db::query('update record set c_day = ?, totel_scores =? ,totel_days =?,create_at = ? where u_id =?',[$c_days,$total_scores,$total_days,$today,$u_id]);

            $return['data']['score'] = $c_days;

            return json($return);
        }
    }

    //签名列表
    public function getList()
    {
        $sign =Db::query('select * from sign_info');

        $return = [
            'code' =>2000,
            'msg' => '签到成功',
            'data' => $sign
        ];
        return json($return);
    }
}