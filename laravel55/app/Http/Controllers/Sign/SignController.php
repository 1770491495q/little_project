<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019/3/4
 * Time: 11:39
 */

namespace App\Http\Controllers\Sign;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SignController extends Controller
{
    public function Index()
    {
        return view('Sign/sign');
    }
    public function DoSign(Request $request)
    {
        $return = [
            'code'=>2000,
            'msg' =>'成功',
            'data'=>[]
        ];
        $params = $request->all();

        if(empty($params)){
            $return = [
                'code'=>4001,
                'msg' =>'参数不全',
                'data'=>[]
            ];
            return json_encode($return);
        }

        if (!isset($params['u_id']) || empty($params['u_id'])){
            $return = [
                'code'=>4002,
                'msg' =>'用户id不能为空',
                'data'=>[]
            ];
            return json_encode($return);
        }

        $u_id = $params['u_id'];
        $today = date('Y-m-d');
        //根据用户ID去查询当天的数据
        $rescure=DB::select('select * from record where  id= ?',[$u_id]);
        //判断今天是否签到过
        if (!empty($rescure) && $rescure[0]->create_at == $today){
            $return = [
                'code'=>4003,
                'msg' =>'嘿不要贪心奥',
                'data'=>[]
            ];
            return json_encode($return);
        }
        if(empty($rescure) || $rescure[0]->u_id != $u_id){
            //第一次签到
            DB::insert('insert into record (u_id,c_day,totel_scores,totel_days,create_at) VALUE (?,?,?,?,?)',[$u_id,1,1,1,$today]);
            $return['data']['score'] = 1;
            return json_encode($return);
        }else{
            //最后一次签到的时间
            $last_day = date('Y-m-d',time()-3600*24);
            //连续签到
            if ($last_day == $rescure[0]->create_at){
                $c_days = $rescure[0]->c_day+1;
            }else{
                $c_days = 1;
            }
        }

        $totel_scores = $rescure[0]->totel_scores+$c_days;
        $totel_days = $rescure[0]->totel_days +1;

        Db::update('update record set c_day = ?, totel_scores =? ,totel_days =?,create_at = ? where u_id =?',[$c_days,$totel_scores,$totel_days,$today,$u_id]);

        $return['data']['score'] = $c_days;

        return json_encode($return);

    }
}