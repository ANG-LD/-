<?php

namespace Api\Controller;
use Qiniu\Pili\Mac;
use Think\Controller;
use JPush\Client as JPush;
use Think\Upload;
class IndexController extends CommonController {


    public function erweimai(){
        $url = "weixin://wxpay/bizpayurl?pr=Zrr5Fyh";
        $qrcode_path = "./Public/" . time() . rand(100, 999) . '_qrcode.png';
        qrcode($url, $qrcode_path, 3, 4);
    }


    public function weixin(){
        header("Content-type: text/html; charset=utf-8");
        if(!isset($_GET['code'])){
            $APPID='wx46e268c7797cae0b';
            $REDIRECT_URI='http://www.duluozb.com/api.php/Index/weixin';
            $scope='snsapi_base';
            $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$scope.'&state=wx'.'#wechat_redirect';
            header("Location:".$url);
        }else{
            $appid = "wx46e268c7797cae0b";
            $secret = "f9f95e49f91333257696b50257a2535d";
            $code = $_GET["code"];
            $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$get_token_url);
            curl_setopt($ch,CURLOPT_HEADER,0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $res = curl_exec($ch);
            curl_close($ch);
            $json_obj = json_decode($res,true);
            //根据openid和access_token查询用户信息
            $access_token = $json_obj['access_token'];
            $openid = $json_obj['openid'];
            $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$get_user_info_url);
            curl_setopt($ch,CURLOPT_HEADER,0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $res = curl_exec($ch);
            curl_close($ch);

            //解析json
            $user_obj = json_decode($res,true);
            $_SESSION['user'] = $user_obj;
            print_r($user_obj);
        }
    }

    /**
     * @获取access_token
     * @获取用户列表
     */
    public function get_access_token(){
        $appid = "wx46e268c7797cae0b";
        $secret = "f9f95e49f91333257696b50257a2535d";
        //$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
        $get_token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret.'';
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_token_url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        $json_obj = json_decode($res,true);
        $list = $this->get_list($json_obj['access_token']);
        foreach ($list as $k=>$v){
            set_time_limit(0);
            M('Withdraw_wechat')->add(['openid'=>$v['openid'],'nickname'=>$v['nickname'],'sex'=>$v['sex'],'province'=>$v['province'],'city'=>$v['city'],'hearimgurl'=>$v['headimgurl'],'unionid'=>$v['unionid'],'intime'=>time()]);
        }
        print_r($list);
    }

    public function get_list($access_token){
        $next_openid = '';
        $get_token_url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$access_token.'&next_openid='.$next_openid.'';
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_token_url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        $json_obj = json_decode($res,true);
        $arr = $json_obj['data']['openid'];
        $info = [];
        foreach ($arr as $k=>$v){
            $get_token_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$v.'&lang=zh_CN';
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$get_token_url);
            curl_setopt($ch,CURLOPT_HEADER,0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $res = curl_exec($ch);
            curl_close($ch);
            $jsons = json_decode($res,true);
            $info[] = $jsons;
        }
        return $info;
    }












    public function test(){
        echo get_number();die;
//        $content = "您关注的11212开播啦，一起来，涨姿势，享健康~";
//        $alias = ['vae3xs3iermy','34l5b4zp2nka'];
//        push5(1,$content,$b['alias'],json_encode($live[$k]));
    }
    /**
     * @给聊天室发消息
     */
    public function send(){
//        $get_gradeinfo = get_gradeinfo(1);
//        $ext = [
//            'user_id'=>80,
//            'username'=>'爱你的玫瑰',
//            'userimg'=>C('IMG_PREFIX').'/Public/admin/Uploads/touxiang/eae4a64b9d3fa2f216487e1ceef1769f.jpg',
//            'intoroom'=>'16169533767681',
//            'usergrade'=>'1',
//            'authName'=>'111'
//        ];
//        //hx_send($fans[0]['hx_username'],$v['room_id'],"进入了直播间",$ext);   //给聊天室发消息
//       print_r(hx_send($fans[0]['hx_username'],$v['room_id'],"进入了直播间",$ext));
    }
    /**
     * @添加聊天室成员
     */
    public function addcreatroom(){
        $roomid = "14009903415299";
        $username = "dhuu2dqw7slx";
        $arr = adduserChatRoom($username,$roomid);
        print_r($arr);
    }


    /**
     * @支付宝
     */
    public function zhifu(){
        import('Vendor.aop.AopClient');
        $c = new \AopClient;
        $c->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $c->appId = "2017032306364899";
        $c->rsaPrivateKey = "MIIEowIBAAKCAQEAnblAtY601gwGLxnjOvH+2T3QXyW7c6cOSXwzeCDdMZgMEpzSucJYlW0XDvDsu6jBvL868ptc0cTXZfs6pFrvhKZubP3n42xyXgxj9Tyvy9hE70HqU7d8CN1lPvPw2eZ5lYuHZOogDBjfpKZfE9XtVOdFhAbfp5A2L5yZMELGkMOxdAnJjPvYX/N6fX+1VNAfVobNS8nd63/2H7g+IBNhwKT6yNwx+y6S4xvbxVRnfYhUzYVxEkaJvJtbYzJ5LtqEh10gKAoekX6ngThFPOea2lb4neKu50QP0ySeU2vllmWd89PJHRyrOxPTMXYhRN+mEGItRLYQBMr9owKGhaiGQwIDAQABAoIBAQCYfnh2Edf96/o+qATvBPKmEAHbPJwUobxKiG8rAntaa+ugQ0fqmmETeDoHFCGO/wbx+uJ/ln8TwgGQdGqqZdGWq2OwtXzygfPBwrESdu5xZFSiD1UERC8EUyqEx05jH1cGo7TweP0rNRFmHAVpJDknEcRAdnTfiasaNYpXAvkBFO9fVkLemcAF3uInib1WEHTLfWUjMiqAzFSLmNxL98U1sT8kXNgsc4QPLBT1+ctyAaGeU2sYNi9W5CABbRPBsar6euQIJAWHaBl2D5gcKOAeZmS86FXYPvjdbWY+kZ7sRC/hTr+5LAWW7DE86CeyLUU3j/GKg16uIj5nr8AzZfqBAoGBAM2aDQhiS/lsQz9U1b1Yon5UZvqGGGA21gxo2/lM+oAInNXU5Jw1QPTdVvSZPZy2HiwtOB7eQvEcP6ci8bCMd3O6ArUmoik0DrGMSSfqXMXKQ+Fvh8+PAt7OyVNFz52xctQZm3h/O5izB7R/Qq0sS4lG4Yl6vd3Bal+yPJ86fy7TAoGBAMRiv3LREvDgI3S+1CDOujbuLFyF1FezPw28jzcuhHVCW0mIubhaAkz/pMqXi72XvJAmsHoOhCv1TOvvI372j8/FdZLkzTbJD3I5GF0gHgWYkq6TjFx5UOEBGWQ/Zt0xx4rJqTFIGYriN11xeOCCDgvtogXsHPkY2usNE+UdiATRAoGADkb/VrC0w+pPtSsn+JEaH5mu2QDM2omUTaJOfD5rOs7eTT8ObQaJrzdbnXqyu5ot+DVfq00V6vZiOTIFBJSMiv3EyF2ZbzENQCkUv2/X52hHVHVRQgmVUnGbq2uyoim7Zp0sna6ALfdotpELyUjFKhBBAzIB009mGqUe7pO6Vz8CgYBzAIiwSfLXj8nzkdRTKfwuMqdTvyMsAFGKT8NmQx1aNXOTyjdH153FTWdJJBm2+uZ3W9C4iUupGkrjVvToYqawuycw0D/EN1WORBUaY7Zw/HnZNZpahMhyGtytsdsg6Qe7JICp1Vjv1a0qUUrB3icvoQbihPQqgv2f69acswB2IQKBgGzRqeaHi6Ip64YN2I2mLm4hlDYLMXpacZEFW4tW+9eecme5R4ylssnnuxTqjEibZmkyLZAprrKKx57P6FiToF44Ldx6rikk/IkEMMy9t4HKyMiU61BLXsQ6PqdaJ5Ddb9j6XlCC+VAwbZs+FbEAu4pgvoU6cILHh9iDyjIPsmpS" ;
        $c->signType= "RSA2";
        $c->alipayrsaPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnblAtY601gwGLxnjOvH+2T3QXyW7c6cOSXwzeCDdMZgMEpzSucJYlW0XDvDsu6jBvL868ptc0cTXZfs6pFrvhKZubP3n42xyXgxj9Tyvy9hE70HqU7d8CN1lPvPw2eZ5lYuHZOogDBjfpKZfE9XtVOdFhAbfp5A2L5yZMELGkMOxdAnJjPvYX/N6fX+1VNAfVobNS8nd63/2H7g+IBNhwKT6yNwx+y6S4xvbxVRnfYhUzYVxEkaJvJtbYzJ5LtqEh10gKAoekX6ngThFPOea2lb4neKu50QP0ySeU2vllmWd89PJHRyrOxPTMXYhRN+mEGItRLYQBMr9owKGhaiGQwIDAQAB";

        $request= new \AlipaySystemOauthTokenRequest();
        $request->setCode("e574614297fa44dfa562d2806c77VX28");
        $request->setGrantType("authorization_code");
        $response= $c->execute($request);
        $rs = json_decode($response,true);
        //echo $rs['alipay_system_oauth_token_response']['access_token'];
        //dump($rs);die;
        $request2= new \AlipayUserUserinfoShareRequest();
        $response2= $c->execute($request2,"kuaijieBf8bbee1dc6cf4364ab3e9d8ae9738C16");
        $rss = json_decode($response2,true);
        dump($rss);die;
    }

    /**
     * @等比例压缩
     */
    public function yasuo(){
        $filename = "./Public/admin/touxiang.png";
        $image = new \Think\Image();
        $image->open($filename);
       // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
        $path = './Public/'.time().'.jpg';
        $image->thumb(50, 50)->save($path);

    }


    /**
     * @七牛创建房间
     */
    public function creatroom(){
        $qiniu_room_id = time().rand(100, 999);
        $qiniu_room_name = GetfourStr(6);
        //$create_room = creatroom("901",$qiniu_room_name);    //七牛创建房间
        $system = M('System')->where(['id'=>1])->find();
        import('Vendor.Qiniu.Pili');
        $ak = $system['ak'];
        $sk = $system['sk'];
        //创建hub
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\RoomClient($mac);
        try {
            $resp = $client->createRoom($qiniu_room_id, $qiniu_room_name);

            $resp = $client->getRoom($qiniu_room_name);
            print_r($resp);
            die();

//            $resp = $client->deleteRoom($qiniu_room_name);
//             dump($resp);

//            //鉴权的有效时间: 1个小时.
            $resp = $client->roomToken($qiniu_room_name, $qiniu_room_id, 'admin', (time()+3600*24*30));
            echo ($resp);die;
        } catch (\Exception $e) {
           // echo "Error:", $e, "\n";
        }
        $result = push_address();

        dump($result);

    }


    /**
     * @随机注册环信账号
     */
    public function hx_zhuce(){
        $chars = "abcdefghijklmnopqrstuvwxyz123456789";
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < 12; $i++){
            $str .= $chars[mt_rand(0, $lc)];
        }
        $hx_password="123456";
        huanxin_zhuce($str,$hx_password); //环信注册
        $result = ['hx_username'=>$str,'hx_password'=>$hx_password];
        success($result);
    }
    /**
     * @根据环信用户名，返回用户信息
     */
    public function get_user_info(){
        $hx_username = I('hx_username');
        if (empty($hx_username)) {
            error("不存在");
        } else {
            $user = M('User')->field('user_id,username,company,duty,img,sex,hx_username,ID,grade,intime,autograph')->where(['hx_username' => $hx_username])->find();
            if(!$user){
                error("不存在");
            }
            $user['img'] = C('IMG_PREFIX') . $user['img'];
            $get_gradeinfo = get_gradeinfo($user['grade']);
            $user['grade_img'] = $get_gradeinfo['img'];
            $user['name'] = $get_gradeinfo['name'];
            $user['intime'] = date("Y-m-d H:i:s",$user['intime']);
            success($user);
        }
    }
    /**
     * @判断是否认证
     */
    public function is_user(){
        $user = checklogin();
        success($user['is_authen']);
    }
    /**
     * @开启直播
     */
    public function start_live(){
        $u = checklogin();
        $log = I('log');  $lag = I('lag');
        $title = I('title');
        //(empty($title)) ? error('参数错误!') : true;
        if(empty($title))       $title = $u['username'].'的直播间';
        if ($u['is_banned']!=1){error('已被禁播');}

        if ($log && $lag){
            $gwd = $lag.','.$log;
            $baidu_apikey = M('System')->getFieldById(1,'baidu_apikey');
            $file_contents = file_get_contents('http://api.map.baidu.com/geocoder/v2/?ak='.$baidu_apikey.'&location='.$gwd.'&output=json');
            $rs = json_decode($file_contents,true);
            $sheng = $rs['result']['addressComponent']['province'];
            $shi = $rs['result']['addressComponent']['city'];
            $qu = $rs['result']['addressComponent']['district'];
            $address = $rs['result']['formatted_address'];
        }

        $config = [
            'maxSize' => 30 * 3145728,
            'rootPath' => './Uploads/image/playimg/',
            'savePath' => '',
            'saveName' => ['uniqid', ''],
            'exts' => ['png', 'jpg', 'jpeg', 'git', 'gif'],
            'autoSub' => true,
            'subName' => '',
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
        if ($info) {
            foreach ($info as $file) {
                $img = '/Uploads/image/playimg/' . $file["savename"];
            }
        }
        if ($img){
            $play_img = $img;
        }else{
            $play_img = $u['img'];
        }
        $u['username'] ? $name = $u['username'] : $name = "直播间" . rand(100, 999);
        $options = [
            'name' => $name,
            'description' => $name,
            'maxusers' => 3000,
            'owner' => $u['hx_username']
        ];
        $create = createChatRoom($options);
        $create['error'] ? error('创建聊天室失败!') : true;
        $play_address = push_address(); //七牛生成流地址
        $qiniu_room_id = time().rand(100, 999);
        $qiniu_room_name = GetfourStr(6);
        $create_room = creatroom($qiniu_room_id,$qiniu_room_name);    //七牛创建房间
        $time = time();
        $data = [
            'user_id'=>$u['user_id'],
            'play_img'=>$play_img,
            'title'=>$title,
            'push_flow_address'=>$play_address['url'],
            'play_address'=>$play_address['url2'],
            'play_address_m3u8'=>$play_address['m3u8'],
            'start_time'=>$time,
            'stream_key'=>$play_address['streamKey'],
            'live_status'=>1,
            'live_time'=>0,
            'room_id'=>$create['data']['id'],
            'intime'=>$time,
            'date'=>date('Y-m-d',$time),
//            'log'=>$log,
//            'lag'=>$lag,
//            'sheng'=>$sheng,
//            'shi'=>$shi,
//            'qu'=>$qu,
//            'address'=>$address,
            'qiniu_room_id'=>$create_room['room_id'],
            'qiniu_room_name'=>$create_room['room_name'],
            'qiniu_token'=>$create_room['token']
        ];
        M('Live')->where(['user_id'=>$u['user_id']])->save(['live_status'=>2]);
        if ($live_id=M('Live')->add($data)) {
            //二维码
            $url = C('IMG_PREFIX')."/api.php/Index/share_live/live_id/" . base64_encode($live_id);
            $qrcode_path = "./Public/upload/qrcode/" . time() . rand(100, 999) . '_qrcode.png';
            qrcode($url, $qrcode_path, 3, 4);
            M('Live')->where(['live_id'=>$live_id])->save(['qrcode_path' => $qrcode_path,'uptime' => time()]);

            $get_gradeinfo = get_gradeinfo($u['grade']);

            $result = ['nums' => '0','push_flow_address' => $play_address['url'],'play_address'=>$play_address['url2'], 'room_id' => $create['data']['id'], 'ID' => $u['id'], 'money' => $u['get_money'], 'start_time' =>(string)$time, 'url' => $url,'qrcode_path'=>C('IMG_PREFIX').$qrcode_path,'live_id'=>$live_id,'time'=>date('Y.m.d',$time),'qiniu_room_id'=>$create_room['room_id'],'qiniu_room_name'=>$create_room['room_name'],
                'qiniu_token'=>$create_room['token'],'is_wheat'=>$u['is_wheat'],'grade_img'=>$get_gradeinfo['img'],'name'=>$get_gradeinfo['name']];
            success($result);
        } else {
            error('开启失败!');
        }

    }

    /**
     * @获取用户是否开启连麦功能
     */
    public function is_wheat(){
        $user = checklogin();
        $user_id = I('user_id');
        $is_wheat = M('User')->where(['user_id'=>$user_id])->getField('is_wheat');
        success($is_wheat);
    }

    /**
     * @开启连麦(关闭连麦)
     * @type  1:开启   2:关闭
     */
    public function save_wheat(){
        $user = checklogin();
        $type = I('type');
        empty($type) ? error('参数错误!') : true;  ($type==1 || $type==2) ? true : error('传值错误!');
        if (M('User')->where(['user_id'=>$user['user_id']])->save(['is_wheat'=>$type,'uptime'=>time()])){
            success('成功!');
        }else{
            error('失败!');
        }
    }

    /**
     * @分享直播界面
     */
    public function share_live(){
        $live_id = base64_decode(I('live_id'));
        $live = M('Live')
            ->alias('a')
            ->field('a.live_id,a.play_img,a.title,a.play_address_m3u8,a.live_status,b.img,b.username,b.company,b.duty,b.ID')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['a.live_id'=>$live_id,'a.live_status'=>1])
            ->find();
        if (!$live){
            $live = M('Live_store')
                ->alias('a')
                ->field('a.live_id,a.play_img,a.title,a.url,b.img,b.username,b.company,b.duty,b.ID')
                ->join('__USER__ b on a.user_id=b.user_id')
                ->where(['live_id'=>$live_id])
                ->find();
            $live['play_address_m3u8'] = $live['url'];
        }
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            $url = 'https://itunes.apple.com/cn/app/id1250688772?mt=8';
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
            $url = 'http://www.duluozb.com/download/duluo.apk';
        }
        $live['down_url'] = $url;
        $this->assign('live',$live);
        $this->display();
    }

    /**
     * @主播非正常退出,再次进入,判断
     */
    public function check_to_live(){
        $user = checklogin();
        $live = M('Live')->where(['user_id'=>$user['user_id'],'live_status'=>1])->find();
        if ($live){
            $result = ['state'=>1,'nums' => $live['nums'],'push_flow_address' => $live['push_flow_address'],'play_address'=>$live['play_address'], 'room_id' => $live['room_id'], 'ID'=>$user['id'], 'money' => $user['money'], 'start_time' =>$live['start_time'], 'url' => $user['url'],'live_id'=>$live['live_id'],'time'=>date('Y.m.d',$live['start_time']),'qiniu_room_id'=>$live['qiniu_room_id'],'qiniu_room_name'=>$live['qiniu_room_name'],'qiniu_token'=>$live['qiniu_token'],'is_wheat'=>$user['is_wheat']];
        }else{
            $result = ['state'=>2];
        }
        success($result);
    }
    /**
     * @如果不再继续直播,关闭直播,删掉流
     */
    public function shut_down_live(){
        $user = checklogin();
        $live = M('Live')->where(['user_id'=>$user['user_id'],'live_status'=>1])->find();
        if ($live){
            //强制下线
            import('Vendor.Qiniu.Pili');
            $system = M('System')->where(['id' => 1])->find();
            $ak = $system['ak'];
            $sk = $system['sk'];
            $hubName = $system['hubname'];
            $mac = new \Qiniu\Pili\Mac($ak, $sk);
            $client = new \Qiniu\Pili\Client($mac);
            $hub = $client->hub($hubName);
            //获取stream
            $streamKey = $live['stream_key'];
            $stream = $hub->stream($streamKey);
            $result = $stream->disable();
            M('Live')->where(['live_id'=>$live['live_id']])->save(['end_time'=>time(),'live_status'=>2]);
        }
        success('成功!');
    }

    /**
     * @直播列表
     * @type  1:热门  2:关注  3:附近   4:官方
     */
    public function live_list(){
        $user = checklogin();
        $type = I('type'); (empty($type))? error('参数错误') : true;
        $page = I('page'); $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;

        $version_number = I('version_number');
        $ios_version = M('System')->where(['id'=>1])->getField('ios_version');

        import('Vendor.Qiniu.Pili');
        $system = M('System')->where(['id' => 1])->find();
        $ak = $system['ak'];
        $sk = $system['sk'];
        $hubName = $system['hubname'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        $hub = $client->hub($hubName);
        $resp = $hub->listLiveStreams("php-sdk-test", 100000, "");
        $resp = $resp[keys];
        if ($resp){
            $rs = M('Live')->where(['stream_key'=>['in',$resp],'is_normal_exit'=>2])->select();
            foreach ($rs as $k=>$v){
                if ($v['live_status']==2){
                    M('Live')->where(['live_id'=>$v['live_id']])->save(['live_status'=>1,'uptime'=>2]);
                }
            }
        }


        switch ($type){
            case 1:
                $regular = M('System')->where(['id'=>1])->getField('regular');
                $data = [
                    'a.live_status'=>1,
                    'b.type'=>1,
                    'b.is_del'=>1,
                    'a.is_hot'=>1,
                      'a.is_tuijian'=>2,
                ];
                $date = [
                    'a.live_status'=>1,
                    'b.type'=>1,
                    'b.is_del'=>1,
                    'a.is_hot'=>1,
                    'a.is_tuijian'=>1,
                ];
                $sex = I('sex');  $province = I('province');
                if($sex){
                    $data['b.sex'] = intval($sex);
                    $date['b.sex'] = intval($sex);
                }
                if($province){
                    $data['a.sheng'] = $province;
                    $date['a.sheng'] = $province;
                }

                $list1 =  M('Live')
                    ->alias('a')
                    ->field('a.*,b.phone,b.img,b.sex,b.username,b.ID,b.hx_username,b.grade,b.province,b.city,b.zan,b.money,b.get_money,b.url,b.type')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->where($data)
                    ->order('location asc')
                    ->select();
                switch ($regular){
                    case 1:
                        $order = 'a.intime desc';
                        break;
                    case 2:
                        $order = 'a.watch_nums desc';
                        break;
                    case 3:
                        $order = 'b.get_money desc';
                        break;
                }

                $list2 = M('Live')
                    ->alias('a')
                    ->field('a.*,b.phone,b.img,b.sex,b.username,b.ID,b.hx_username,b.grade,b.province,b.city,b.zan,b.money,b.get_money,b.url,b.type')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->where($date)
                    ->order($order)
                    ->select();
                if ($list1 && $list2){
                    $list = array_merge($list1,$list2);
                }elseif($list1 && !$list2){
                    $list = $list1;
                }elseif (!$list1 && $list2){
                    $list = $list2;
                }
                if ($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                        $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                        $list[$k]['url'] = C('IMG_PREFIX')."/api.php/Index/share_live/live_id/" . base64_encode($v['live_id']);
                        $list[$k]['qrcode_path'] = C('IMG_PREFIX').$v['qrcode_path'];
                        $follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                        $follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";
                        $list[$k]['is_type'] = "1";

                        $get_gradeinfo = get_gradeinfo($v['grade']);
                        $list[$k]['grade_img'] = $get_gradeinfo['img'];
                        $list[$k]['name'] = $get_gradeinfo['name'];
                    }
                    $list = array_slice($list,($page-1)*$pageSize,$pageSize);
                }else{$list=[];}


                if ($version_number==$ios_version){
                    $dat = [
                        'a.is_del'=>1,
                        'b.is_del'=>1,
                    ];
                    $live_store = M('Live_store')
                        ->alias('a')
                        ->field('a.*,b.phone,b.img,b.sex,b.username,b.ID,b.hx_username,b.grade,b.province,b.city,b.money,b.get_money,c.watch_nums,c.sheng,c.shi,c.qu,b.type')
                        ->join('__USER__ b on a.user_id=b.user_id')
                        ->join('__LIVE__ c on a.live_id=c.live_id')
                        ->where($dat)
                        ->order('rand()')
                        ->limit(10)
                        ->select();
                    if ($live_store){
                        foreach ($live_store as $k=>$v){
                            $live_store[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                            $live_store[$k]['img'] = C('IMG_PREFIX').$v['img'];
                            $live_store[$k]['lebel'] = explode(',',$v['lebel']);
                            $live_store[$k]['is_type'] = "2";

                            $is_follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                            $is_follow ? $live_store[$k]['is_follow'] = "2" : $live_store[$k]['is_follow'] = "1";
                        }
                    }else{$live_store=[];}
                    $list = array_merge($list,$live_store);
                }


                success($list);
                break;
            case 2:
                $follow = M('Follow')->where(['user_id'=>$user['user_id']])->select();
                if ($follow){
                    $ids = array_map(function($v){ return $v['user_id2'];},$follow);
                    array_push($ids, $user['user_id']);
                    $dat = [
                        'a.user_id'=>['not in',$ids],
                        'a.is_fans'=>1,
                        'a.is_del'=>1,
                        'b.live_status'=>1
                    ];
                    $tuijian = M('User')
                        ->alias('a')
                        ->join('__LIVE__ b on a.user_id=b.user_id')
                        ->field('a.user_id,phone,img,sex,username,autograph,grade,ID,hx_username,province,city,money,get_money,url,b.sheng,type')
                        ->where($dat)
                        ->order('rand()')
                        ->limit(5)
                        ->select();
                    if ($tuijian){
                        foreach ($tuijian as $k=>$v){
                            $tuijian[$k]['img'] = C('IMG_PREFIX').$v['img'];
                            $is_follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                            $is_follow ? $tuijian[$k]['is_follow'] = "2" : $tuijian[$k]['is_follow'] = "1";

                            $get_gradeinfo = get_gradeinfo($v['grade']);
                            $tuijian[$k]['grade_img'] = $get_gradeinfo['img'];
                            $tuijian[$k]['name'] = $get_gradeinfo['name'];
                        }
                    }else{$tuijian = [];}
                }else{
                    $dat = [
                        'a.is_fans'=>1,
                        'a.is_del'=>1,
                        'b.live_status'=>1,
                        'a.user_id'=>['neq',$user['user_id']]
                    ];
                    $tuijian = M('User')
                        ->alias('a')
                        ->join('__LIVE__ b on a.user_id=b.user_id')
                        ->field('a.user_id,phone,img,sex,username,autograph,grade,ID,hx_username,province,city,money,get_money,url,b.sheng,type')
                        ->where($dat)
                        ->order('rand()')
                        ->limit(5)
                        ->select();
                    if ($tuijian){
                        foreach ($tuijian as $k=>$v){
                            $tuijian[$k]['img'] = C('IMG_PREFIX').$v['img'];
                            $is_follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                            $is_follow ? $tuijian[$k]['is_follow'] = "2" : $tuijian[$k]['is_follow'] = "1";

                            $get_gradeinfo = get_gradeinfo($v['grade']);
                            $tuijian[$k]['grade_img'] = $get_gradeinfo['img'];
                            $tuijian[$k]['name'] = $get_gradeinfo['name'];
                        }
                    }else{$tuijian = [];}
                }
                $list['tuijian'] = $tuijian;
                //直播
                $live = M('Follow')
                    ->alias('a')
                    ->field('b.*,c.phone,c.img,c.sex,c.username,c.ID,c.hx_username,c.grade,c.province,c.city,c.money,c.get_money,c.type')
                    ->join('__LIVE__ b on a.user_id2=b.user_id')
                    ->join('__USER__ c on c.user_id=b.user_id')
                    ->where(['a.user_id'=>$user['user_id'],'b.live_status'=>1,'c.is_del'=>1])
                    ->order('b.watch_nums desc')
                    ->select();
                if ($live){
                    foreach ($live as $k=>$v){
                        $live[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                        $live[$k]['img'] = C('IMG_PREFIX').$v['img'];
                        $live[$k]['url'] = C('IMG_PREFIX')."/api.php/Index/share_live/live_id/" . base64_encode($v['live_id']);
                        $live[$k]['qrcode_path'] = C('IMG_PREFIX').$v['qrcode_path'];
                        $live[$k]['is_type'] = "1";
                        $follow2 = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                        $follow2 ? $live[$k]['is_follow'] = "2" : $live[$k]['is_follow'] = "1";

                        $get_gradeinfo = get_gradeinfo($v['grade']);
                        $live[$k]['grade_img'] = $get_gradeinfo['img'];
                        $live[$k]['name'] = $get_gradeinfo['name'];
                    }
                }else{$live=[];}
                //录播
                $live_store = M('Follow')
                    ->alias('a')
                    ->field('b.*,c.phone,c.img,c.sex,c.username,c.ID,c.hx_username,c.grade,c.province,c.city,c.money,c.get_money,d.watch_nums,d.sheng,d.shi,d.qu,c.type')
                    ->join('__LIVE_STORE__ b on a.user_id2=b.user_id')
                    ->join('__USER__ c on b.user_id=c.user_id')
                    ->join('__LIVE__ d on d.live_id=b.live_id')
                    ->where(['a.user_id'=>$user['user_id'],'c.is_del'=>1,'b.is_del'=>1])
                    ->select();
                if ($live_store){
                    foreach ($live_store as $k=>$v){
                        $live_store[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                        $live_store[$k]['img'] = C('IMG_PREFIX').$v['img'];
                        $live_store[$k]['is_type'] = "2";
                        $follow2 = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                        $follow2 ? $live_store[$k]['is_follow'] = "2" : $live_store[$k]['is_follow'] = "1";

                        $get_gradeinfo = get_gradeinfo($v['grade']);
                        $live_store[$k]['grade_img'] = $get_gradeinfo['img'];
                        $live_store[$k]['name'] = $get_gradeinfo['name'];
                    }
                }else{$live_store=[];}
                $lista = array_merge($live,$live_store);
                $list['list'] = array_slice($lista,($page-1)*$pageSize,$pageSize);
                success($list);
                break;
            case 3:
                $log = I('log'); $lag = I('lag');  $sex = I('sex');
                $data = [
                    'a.live_status'=>1,
                    'b.type'=>1,
                    'b.is_del'=>1,
                ];
                !empty($sex) ? $data['b.sex'] = $sex : true;
                $list = M('Live')
                    ->alias('a')
                    ->field('a.*,b.phone,b.img,b.sex,b.username,b.ID,b.hx_username,b.grade,b.province,b.city,b.zan,b.money,b.get_money,b.url,b.type')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->where($data)
                    ->order('a.watch_nums desc')
                    ->page($page,$pageSize)
                    ->select();
                if ($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                        $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                        $list[$k]['url'] = C('IMG_PREFIX')."/api.php/Index/share_live/live_id/" . base64_encode($v['live_id']);
                        $list[$k]['qrcode_path'] = C('IMG_PREFIX').$v['qrcode_path'];
                        $follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                        $follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";

                        $get_gradeinfo = get_gradeinfo($v['grade']);
                        $list[$k]['grade_img'] = $get_gradeinfo['img'];
                        $list[$k]['name'] = $get_gradeinfo['name'];
                    }
                }else{$list=[];}
                if ($log && $lag){
                    foreach ($list as $k=>$v){
                        $list[$k]['distance'] = (string)round(getDistance($lag,$log,$v['lag'],$v['log'])/1000,2);   //算出距离（公里），保留两位小数点(四舍五入)
                    }
                    foreach ($list as $k=>$v){
                        if ($v['distance']>200){
                            unset($list[$k]);
                        }
                        if ($v['distance']<2){
                            $list[$k]['distance'] = "2";
                        }
                    }
                }else{
                    foreach ($list as $k=>$v){
                        $list[$k]['distance'] = $v['shi'];
                    }
                }
                $list = array_values($list);
                success($list);
                break;
            case 4:
                $data = [
                    'a.live_status'=>1,
                    'b.type'=>2,
                    'b.is_del'=>1,
                ];
                $list = M('Live')
                    ->alias('a')
                    ->field('a.*,b.phone,b.img,b.sex,b.username,b.ID,b.hx_username,b.grade,b.province,b.city,b.zan,b.money,b.get_money,b.url,b.type')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->where($data)
                    ->order('a.watch_nums desc')
                    ->page($page,$pageSize)
                    ->select();
                if ($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                        $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                        $list[$k]['url'] = C('IMG_PREFIX')."/api.php/Index/share_live/live_id/" . base64_encode($v['live_id']);
                        $list[$k]['qrcode_path'] = C('IMG_PREFIX').$v['qrcode_path'];
                        $follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                        $follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";

                        $list[$k]['is_type'] = "1";

                        $get_gradeinfo = get_gradeinfo($v['grade']);
                        $list[$k]['grade_img'] = $get_gradeinfo['img'];
                        $list[$k]['name'] = $get_gradeinfo['name'];
                    }
                }else{$list=[];}
                if ($version_number==$ios_version){
                    $dat = [
                        'a.is_del'=>1,
                        'b.is_del'=>1,
                    ];
                    $live_store = M('Live_store')
                        ->alias('a')
                        ->field('a.*,b.phone,b.img,b.sex,b.username,b.ID,b.hx_username,b.grade,b.province,b.city,b.money,b.get_money,c.watch_nums,c.sheng,c.shi,c.qu,b.type')
                        ->join('__USER__ b on a.user_id=b.user_id')
                        ->join('__LIVE__ c on a.live_id=c.live_id')
                        ->where($dat)
                        ->order('rand()')
                        ->limit(10)
                        ->select();
                    if ($live_store){
                        foreach ($live_store as $k=>$v){
                            $live_store[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                            $live_store[$k]['img'] = C('IMG_PREFIX').$v['img'];
                            $live_store[$k]['lebel'] = explode(',',$v['lebel']);
                            $live_store[$k]['is_type'] = "2";

                            $is_follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                            $is_follow ? $live_store[$k]['is_follow'] = "2" : $live_store[$k]['is_follow'] = "1";
                        }
                    }else{$live_store=[];}
                    $list = array_merge($list,$live_store);
                }
                success($list);
                break;
        }
    }


    /**
     * @音乐列表
     */
    public function music_list(){
        $keyworks = I('keyworks');
        $page = I('page'); $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        if ($keyworks){
            $data = [
                'song_name|singer'=>['like','%'.$keyworks.'%']
            ];
        }
        $list = M('Music')->where($data)->order('intime desc')->page($page,$pageSize)->select();
        if($list){
            foreach ($list as $k=>$v){
                $list[$k]['path'] = C('IMG_PREFIX').$v['path'];
            }
        }else{$list=[];}
        success($list);
    }


    /**
     * @地区列表
     */
    public function area_list(){
        $page = I('page'); $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        $list = M('Live')
            ->field('sheng,count(sheng) as count')
            ->group('sheng')
            ->where(['live_status'=>1])
            ->order('count desc')
            ->page($page,$pageSize)
            ->select();
        $all['sheng'] = "热门";
        $all['count'] = M('Live')->where(['live_status'=>1])->count();
        array_unshift($list, $all);//向数组插入元素(放在第一位)
        $list ? $list = $list : $list = [];
        success($list);
    }

    /**
     *@进入直播间前判断是否有资格
     */
    public function  check_into_live(){
        $user = checklogin();
        $live_id = I('live_id');
        if(!$live_id)                   error("参数错误");
        $live = M('Live')->where(['live_id'=>$live_id])->find();
        if(!$live)                      error("参数错误");
        $user_id  = $live['user_id'];
        $record = M('UpgradeRecord')
            ->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id])
            ->order("id desc")->find();         //查询是否缴费
        $system = M('System')->where(['id'=>1])->find();
        if($record){
            $class = M('TutorLiveClass')->where(['tutor_id'=>$user_id,'is_del'=>1])
                ->limit(1)->order("intime desc")->find();
            if(strtotime($class['end_time'])>time()){
                if($record['state'] == 1){
                    $action = '当月观看该导师直播数已用完';
                    $num = $system['gao_watch_num'];
                }else{
                    $action = '当月观看该导师直播数已用完';
                    $num = $system['zuan_watch_num'];
                }
            }else{
                $action = '你尚未成为该导师的会员';
                $num = $system['pu_watch_num'];
            }
        }else{
            $action = '你尚未成为该导师的会员';
            $num = $system['pu_watch_num'];
        }

        $month = $month     = date("Y-m-01",time());     //当月时间
        $where['user_id']   = $user['user_id'];
        $where['user_id2']  = $user_id;
        $where['intime']    = ['gt',$month];
        $count = M('live_watch_record')->where($where)->count();
        if($count<$num){
            success('ok');
        }else{
            error($action);
        }
    }



    /**
     * @进入直播间
     */
    public function into_live(){
        $user = checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $check = M('live')->where(['live_id'=>$live_id])->find();
        M('Live_kicking')->where(['live_id'=>$live_id,'user_id'=>$user_id])->find() ? error('被踢出,无法进入!') : true;
        //M('Shield')->where(['user_id'=>M('Live')->getFieldByLive_id($live_id,'user_id'),'user_id2'=>$user_id])->find() ? error('被拉黑,无法进入!') : true;

        //进入直播间,把进入的其他正在直播的记录删除
        $live = M('Live')->alias('a')
            ->field('a.live_id,b.user_id2')
            ->join('right join __LIVE_NUMBER__ b on a.live_id=b.live_id')
            ->where(['a.live_status'=>1])
            ->select();
        if ($live){
            $ids = array_map(function($v){ return $v['user_id2'];},$live);
            if (in_array($user_id,$ids)){
                $live_number = M('Live_number')->where(['live_id'=>$live_id,'user_id2'=>$user_id])->find();
                if ($live_number){M('Live_number')->where(['live_number_id'=>$live_number['live_number_id']])->delete();}
            }
        }
        $user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        if (M('Live_number')->add(['live_id'=>$live_id,'user_id'=>$user_id2,'user_id2'=>$user_id,'intime'=>time()])){
            M('Live')->comment('观看总人数加1')->where(['live_id'=>$live_id])->setInc('nums');
            M('Live')->comment('观看人数加1')->where(['live_id'=>$live_id])->setInc('watch_nums');
            $is_follow = M('Follow')->where(['user_id' => $user_id, 'user_id2' => $user_id2])->find();
            $is_follow ? $is_follow = "2" : $is_follow = "1";
            $lignt_up = M('Live_light_up')->where(['live_id'=>$live_id,'user_id'=>$user_id,'user_id2'=>$user_id2])->find();
            $lignt_up ? $is_lignt_up = "2" : $is_lignt_up = "1";
            $prompt = M('System')->where(['id'=>1])->getField('prompt');

            $get_gradeinfo = get_gradeinfo($user['grade']);

            $result = ['is_follow'=>$is_follow,'is_lignt_up'=>$is_lignt_up,'prompt'=>$prompt,'grade_img'=>$get_gradeinfo['img'],'name'=>$get_gradeinfo['name']];

            $day = date("Y-m-d",time());
            $check2 = M('live_watch_record')->where(['user_id'=>$user_id,'user_id2'=>$check['user_id'],'intime'=>['gt',$day]])->find();
            if(!$check2){
                $data['user_id']    = $user_id;
                $data['user_id2']   = $check['user_id'];
                $data['live_id']    =  $live_id;
                $data['intime']     = date("Y-m-d H:i:s");
                M('live_watch_record')->add($data);
            }

        }else{
            error('失败!');
        }
        success($result);
    }
    /**
     * @获取主播信息
     */
    public function get_live_info(){
        checklogin();
        $uid = I('uid');
        $user_id = I('user_id');
        empty($user_id) ? error('参数错误!') : true;
        $info = M('User')->where(['user_id'=>$user_id])->find();
        $city = M('Live')->where(['user_id'=>$user_id,'live_status'=>1])->getField('shi');
        if ($city){
            $info['city'] = $city;
        }elseif($info['city']){
            $info['city'] = $info['city'];
        }else{
            $info['city'] = "火星";
        }
        $info['img'] = C('IMG_PREFIX').$info['img'];
        $info['money'] = FormatMoney($info['money']);
        $info['follow_count'] = FormatMoney(M('Follow')->comment('关注数')->where(['user_id' =>$user_id])->count());
        $info['fans_count']  = FormatMoney(M('Follow')->comment('粉丝数')->where(['user_id2' =>$user_id])->count());
        $give_count = M('Give_gift')->comment('送礼数')->where(['user_id'=>$user_id])->sum('jewel');
        $give_count ? $info['give_count'] = FormatMoney($give_count) : $info['give_count'] = "0";
        //$info['give_count'] = FormatMoney(M('Give_gift')->comment('送礼数')->where(['user_id'=>$user_id])->count());
        $get_money = M('Give_gift')->where(['user_id2'=>$user_id])->sum('jewel');
        $get_money ? $info['get_money'] = FormatMoney($get_money) : $info['get_money'] = "0";
        $is_follow = M('Follow')->where(['user_id'=>$uid,'user_id2'=>$user_id])->find();
        $is_follow ? $info['is_follow'] = "2" : $info['is_follow'] = "1";



        $get_gradeinfo = get_gradeinfo($info['grade']);
        $info['grade_img'] = $get_gradeinfo['img'];
        $info['name'] = $get_gradeinfo['name'];
        success($info);
    }

    /**
     * @根据用户名获取用户信息
     */
    public function get_username_info(){
        checklogin();
        $uid = I('uid');
        $username = I('username');
        empty($username) ? error('参数错误!') : true;
        $user_id = M('User')->where(['username'=>$username])->getField('user_id');
        $info = M('User')->where(['user_id'=>$user_id])->find();
        $city = M('Live')->where(['user_id'=>$user_id,'live_status'=>1])->getField('shi');
        if ($city){
            $info['city'] = $city;
        }elseif($info['city']){
            $info['city'] = $info['city'];
        }else{
            $info['city'] = "火星";
        }
        $info['img'] = C('IMG_PREFIX').$info['img'];
        $info['money'] = FormatMoney($info['money']);
        $info['follow_count'] = FormatMoney(M('Follow')->comment('关注数')->where(['user_id' =>$user_id])->count());
        $info['fans_count']  = FormatMoney(M('Follow')->comment('粉丝数')->where(['user_id2' =>$user_id])->count());
        $info['give_count'] = FormatMoney(M('Give_gift')->comment('送礼数')->where(['user_id'=>$user_id])->count());
        $get_money = M('Give_gift')->where(['user_id2'=>$user_id])->sum('jewel');
        $get_money ? $info['get_money'] = $get_money : $info['get_money'] = "0";
        $is_follow = M('Follow')->where(['user_id'=>$uid,'user_id2'=>$user_id])->find();
        $is_follow ? $info['is_follow'] = "2" : $info['is_follow'] = "1";



        $get_gradeinfo = get_gradeinfo($info['grade']);
        $info['grade_img'] = $get_gradeinfo['img'];
        $info['name'] = $get_gradeinfo['name'];
        success($info);
    }

    /**
     * @直播间用户列表
     */
    public function show_viewer(){
        checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        $page = I('page'); $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        empty($live_id) ? error('参数错误!') : true;
        $list = M('Live_number')
            ->alias('a')
            ->field('a.user_id2,b.user_id,b.img,b.username,b.grade,b.autograph,b.ID,b.hx_username,b.hx_password,b.money,b.get_money,b.sex,b.province,b.city,b.type')
            ->join('__USER__ b on a.user_id2=b.user_id')
            ->where(['a.live_id'=>$live_id])
            ->order('b.type desc,b.grade desc,b.is_fans asc')
            ->page($page,$pageSize)
            ->select();
        $count = M('Live_number')
            ->alias('a')
            ->join('__USER__ b on a.user_id2=b.user_id')
            ->where(['a.live_id'=>$live_id])
            ->count();
        if ($list){
            foreach ($list as $k=>$v) {
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $list[$k]['follow_count'] = FormatMoney(M('Follow')->comment('关注数')->where(['user_id' =>$v['user_id2']])->count());
                $list[$k]['fans_count']  = FormatMoney(M('Follow')->comment('粉丝数')->where(['user_id2' =>$v['user_id2']])->count());
                $is_follow = M('Follow')->where(['user_id'=>$user_id,'user_id2'=>$v['user_id2']])->find();
                $is_follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";
                //查询是否被禁言
                //$is_banned = M('Banned')->where(['live_id'=>$live_id,'user_id2'=>$v['user_id2']])->find();
                $is_banned = M('Banned')->where(['user_id'=>$user_id,'user_id2'=>$v['user_id2']])->find();
                $is_banned ? $list[$k]['is_banned'] = "2" : $list[$k]['is_banned'] = "1";
                //查询是否是管理
                $is_management = M('Live_management')->where(['user_id'=>$v['user_id'],'user_id'=>$v['user_id2']])->find();
                $is_management ? $list[$k]['is_management'] = "2" : $list[$k]['is_management'] = "1";

                $give_count = M('Give_gift')->where(['user_id'=>$v['user_id']])->sum('jewel');
                $give_count ? $list[$k]['give_count'] = FormatMoney($give_count) : $list[$k]['give_count'] ="0";
                //$list[$k]['give_count'] = FormatMoney(M('Give_gift')->comment('送礼数')->where(['user_id'=>$v['user_id']])->count());

                $get_gradeinfo = get_gradeinfo($v['grade']);
                $list[$k]['grade_img'] = $get_gradeinfo['img'];
                $list[$k]['name'] = $get_gradeinfo['name'];

                $get_money = M('Give_gift')->where(['user_id2'=>$v['user_id']])->sum('jewel');
                $get_money ? $list[$k]['get_money'] = FormatMoney($get_money) : $list[$k]['get_money'] = "0";

                if($v['city']){
                    $list[$k]['city'] = $v['city'];
                }else{
                    $list[$k]['city'] = "火星";
                }
            }
        }else{$list=[];}
        $result = ['count'=>$count,'list'=>$list];
        success($result);
    }

    /**
     * @踢人
     */
    public function kicking(){
        checklogin();
        $live_id = I('live_id');  $user_id = I('user_id');
        (empty($live_id) || empty($user_id)) ? error('参数错误!') : true;
        $table_live_kicking = M('Live_kicking'); $table_live_number = M('Live_number');
        $table_live_kicking->startTrans();  //开启事物
        if ($table_live_kicking->add(['live_id'=>$live_id,'user_id'=>$user_id,'intime'=>time(),'date'=>date('Y-m-d',time())])){
            $live_number = $table_live_number->where(['live_id'=>$live_id,'user_id2'=>$user_id])->delete();
            if (!$live_number){
                $table_live_kicking->rollback();
                error('失败!');
            }
            $table_live_kicking->commit();
            success('成功!');
        }else{
            $table_live_kicking->rollback();
            error('失败!');
        }
    }

    /**
     * @禁言(取消禁言)
     */
    public function banned(){
        $user = checklogin();
        $user_id2 = I('user_id');
        (empty($user_id2)) ? error('参数错误!') : true;
        $re = M('Banned')->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id2])->find();
        if($re){
            if (M('Banned')->where(['banned_id'=>$re['banned_id']])->delete()){
                success('1!');
            }else{
                error('失败!');
            }
        }else{
            if (M('Banned')->add(['user_id'=>$user['user_id'],'user_id2'=>$user_id2,'intime'=>time()])){
                success('2');
            }else{
                error('失败!');
            }
        }
    }

    /**
     * @判断是否被禁言
     */
    public function get_banned(){
        $user = checklogin();
        $user_id = I('user_id');
        //$live_id = I('live_id');
        //empty($live_id) ? error('参数错误!') : true;
        //$user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        $re = M('Banned')->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id])->find();
        if ($re){
            error("对方在你的禁言名单中");
        }else{
            if(M('Banned')->where(['user_id'=>$user_id,'user_id2'=>$user['user_id']])->find()){
                error("你在对方的禁言名单中");
            }
            success("ok");
        }
    }

    /**
     * @设为管理(取消管理)
     * @type  1:设为管理   2:取消管理
     */
    public function live_manag(){
        checklogin();
        $user_id = I('uid'); $user_id2 = I('user_id'); $type = I('type');
        (empty($user_id2) || empty($type)) ? error('参数错误!') : true;
        ($type==1 || $type==2) ? true : error('传值错误!');
        $man = M('Live_management')->where(['user_id'=>$user_id,'user_id2'=>$user_id2])->find();
        switch ($type){
            case 1:
                if ($man){
                    error('已经设为管理!');
                }else{
                    if (M('Live_management')->add(['user_id'=>$user_id,'user_id2'=>$user_id2,'intime'=>time()])){
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }
                break;
            case 2:
                if (!$man){
                    error('还未设为管理!');
                }else{
                    if (M('Live_management')->where(['live_management_id'=>$man['live_management_id']])->delete()){
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }
                break;
        }
    }
    /**
     * @管理列表
     */
    public function management_list(){
        $user = checklogin();
        $live_id = I('live_id');
        $page = I('page');
        $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        $live = M('Live')->find($live_id);
        empty($live_id) ? error('参数错误!') : true;
        $list = M('Live_management')
            ->alias('a')
            ->field('b.user_id,b.username,b.img,b.ID,b.hx_username,b.grade')
            ->join('__USER__ b on a.user_id2=b.user_id')
            ->where(['a.user_id'=>$live['user_id']])
            ->page($page,$pageSize)
            ->order('a.intime desc')
            ->select();
        if ($list){
            foreach ($list as $k=>$v) {
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                $follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";

                $get_gradeinfo = get_gradeinfo($v['grade']);
                $list[$k]['grade_img'] = $get_gradeinfo['img'];
                $list[$k]['name'] = $get_gradeinfo['name'];
            }
        }else{$list=[];}
        success($list);
    }


    /**
     * @判断是否是管理
     */
    public function is_mangement(){
        checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        if (M('Live_management')->where(['user_id'=>$user_id2,'user_id2'=>$user_id])->find()){
            $result = "2";
        }else{
            $result = "1";
        }
        success($result);
    }

    /**
     * @退出直播间
     */
    public function out_live(){
        checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        $count = M('Live')->getFieldByLive_id($live_id,'watch_nums');
        if ($count==0){
            M('Live_number')->comment('删除记录')->where(['live_id'=>$live_id,'user_id'=>$user_id2,'user_id2'=>$user_id])->delete();
            success('成功!');
        }
        if (M('Live')->where(['live_id' =>$live_id])->setDec('watch_nums')){
            M('Live_number')->comment('删除记录')->where(['live_id'=>$live_id,'user_id'=>$user_id2,'user_id2'=>$user_id])->delete();
            success('成功!');
        }else{
            error('失败!');
        }
    }

    /**
     * @点亮
     */
    public function lignt_up(){
        checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        $lignt_up = M('Live_light_up')->where(['live_id'=>$live_id,'user_id'=>$user_id,'user_id2'=>$user_id2])->find();
        if($lignt_up){
            error('已点亮!');
        }else{
            if (M('Live_light_up')->add(['live_id'=>$live_id,'user_id'=>$user_id,'user_id2'=>$user_id2,'intime'=>time()])){
                M('Live')->comment('点亮数加1')->where(['live_id'=>$live_id])->setInc('light_up_count');
                success('成功!');
            }else{
                error('失败!');
            }
        }
    }


    /**
     * @日榜、周榜、总榜
     * $type  1:日榜   2:周榜  3:总榜
     */
    public function total_list(){
        $user = checklogin();
        $type = I('type'); $user_id = I('user_id'); //主播id
        (empty($type) || empty($user_id)) ? error('参数错误!') : true;
        $page = I('page');
        $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        switch ($type){
            case 1:
                $where = [
                    'a.user_id2'=>$user_id,
                    'a.date'=>date('Y-m-d',time())
                ];
                break;
            case 2:
                $where = [
                    'a.user_id2'=>$user_id,
                    'a.intime'=>['gt',time()-(7*24*60*60)]
                ];
                break;
            case 3:
                $where = [
                    'a.user_id2'=>$user_id,
                ];
                break;
        }
        $list = M('Give_gift')
            ->alias('a')
            ->field('b.user_id,b.img,b.username,b.hx_username,sum(jewel) as count,b.grade')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->group('a.user_id')
            ->where($where)
            ->order('count desc')
            ->page($page,$pageSize)
            ->select();
        if ($list){
            $count = count($list);
            foreach ($list as $k=>$v){
                if ($count>=2){     //第一位置和第二位置调换
                    $a=$list[0];
                    $list[0]=$list[1];
                    $list[1]=$a;
                }
            }
            foreach ($list as $k=>$v){
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $is_follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id])->find();
                $is_follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";

                $get_gradeinfo = get_gradeinfo($v['grade']);
                $list[$k]['grade_img'] = $get_gradeinfo['img'];
                $list[$k]['name'] = $get_gradeinfo['name'];
            }
        }else{$list=[];}
        success($list);
    }



    /**
     * @举报类型列表(原因)
     * @type   1:直播间举报主播   2:举报用户
     */
    public function report_why(){
        $type = I('type');
        empty($type) ? error('参数错误!') : true;
        $list = M('Report_why')->select();
        //$list = M('Report_why')->where(['type'=>$type])->select();
        if (!$list){$list=[];}
        success($list);
    }


    /**
     * @举报(直播间举报)
     */
    public function report(){
        $user = checklogin();
        $live_id = I('live_id'); $user_id = I('user_id'); //主播id
        $why = I('why');
        (empty($why) || empty($user_id)) ? error('参数错误!') : true;
        if (M('Report')->add(['user_id'=>$user['user_id'],'live_id'=>$live_id,'user_id2'=>$user_id,'why'=>$why,'intime'=>time(),'type'=>1])){
            success('成功!');
        }else{
            error('失败!');
        }
    }







    /**
     * @关注（取消关注）
     * @type  1:关注   2：取消关注
     */
    public function follow(){
        checklogin();
        $user_id = I('uid');
        $user_id2 = I('user_id2');
        $type = I('type');
        (empty($type) || empty($user_id2)) ? error('参数错误!') : true;
        ($type == 1 || $type == 2) ? true : error('传值错误!');
        if ($user_id == $user_id2) error("传值错误");
        $check = M('Follow')->where(['user_id' => $user_id, 'user_id2' => $user_id2])->find();
        if ($type == 1) {
            if ($check) error("已关注");
            if (M('Shield')->where(['user_id'=>$user_id2,'user_id2'=>$user_id])->find()){success('成功!');} //如果被拉黑,返回成功,但是不是真正的成功
            if (M('Follow')->add(['user_id' => $user_id, 'user_id2' => $user_id2, 'intime' => time()])) {
                success('成功!');
            } else {
                error('失败!');
            }
        } else {
            if (!$check) error("未关注");
            if (M('Follow')->where(['user_id' => $user_id, 'user_id2' => $user_id2])->delete()) {
                success('成功!');
            } else {
                error('失败!');
            }
        }
    }

    /**
     * 结束直播(主播端)
     */
    public function end_live(){
        checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        if (M('Live')->where(['live_id'=>$live_id])->save(['end_time'=>time(),'live_status'=>2,'is_normal_exit'=>1])){
            $live = M('Live')->alias('a')
                ->field('a.live_id,a.user_id,a.play_img,a.title,a.start_time,a.end_time,a.watch_nums,a.share,a.stream_key,a.room_id,b.img,b.username,b.company,b.duty,b.autograph,b.ID')
                ->join('__USER__ b on a.user_id=b.user_id')
                ->where(['live_id'=>$live_id])
                ->find();
            if (time()-$live['start_time']>120) {
                //保存视频
                import('Vendor.Qiniu.Pili');
                $system = M('System')->where(['id' => 1])->find();
                $ak = $system['ak'];
                $sk = $system['sk'];
                $hubName = $system['hubname'];
                $mac = new \Qiniu\Pili\Mac($ak, $sk);
                $client = new \Qiniu\Pili\Client($mac);
                $hub = $client->hub($hubName);
                //获取stream
                $streamKey = $live['stream_key'];
                $stream = $hub->stream($streamKey);
                //保存直播数据
                $fname = $stream->save(0, 0);
                if ($fname['fname']) {
                    $url = 'http://oc3pwoyhb.bkt.clouddn.com/' . $fname['fname'];
                    $data = [
                        'live_id' => $live_id,
                        'user_id' => $live['user_id'],
                        'play_img' => $live['play_img'],
                        'title' => $live['title'],
                        'url' => $url,
                        'intime' => time(),
                        'room_id' => $live['room_id'],
                        'date' => date('Y-m-d', time())
                    ];
                    M('Live_store')->add($data);
                }
            }
            $url ? $live['url'] = $url : $live['url'] = "";
            $list = M('Live')->where(['date'=>date('Y-m-d',time())])->order('nums desc')->select();
            foreach ($list as $k=>$v){
                if ($v['live_id']==$live_id){
                    $aa = $k;
                }
            }
            $cout = count($list);
            $live['beat'] = (string)(floor((($cout-$aa)/$cout)*100));

            $live['play_img'] = C('IMG_PREFIX').$live['play_img'];
            $live['img'] = C('IMG_PREFIX').$live['img'];
            $timediff = $live['end_time']-$live['start_time'];
            //计算小时数
            $remain = $timediff%86400;
            $hours = intval($remain/3600);
            //计算分钟数
            $remain = $remain%3600;
            $mins = intval($remain/60);
            //计算秒数
            $secs = $remain%60;
            $live['time'] = $hours.":".$mins.":".$secs;
            $get_fire = M('Give_gift')->where(['live_id'=>$live_id])->sum('jewel');
            $get_fire ? $live['get_fire'] = $get_fire : $live['get_fire'] = "0";


            success($live);
        }else{
            error('失败!');
        }
    }

    /**
     * @强制下线
     */
    public function forced_offline(){
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $live = M('Live')->find($live_id);
        //保存视频
        import('Vendor.Qiniu.Pili');
        $system = M('System')->where(['id' => 1])->find();
        $ak = $system['ak'];
        $sk = $system['sk'];
        $hubName = $system['hubname'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        $hub = $client->hub($hubName);
        //获取stream
        $streamKey = $live['stream_key'];
        $stream = $hub->stream($streamKey);
        $result = $stream->disable();
        M('Live')->where(['live_id'=>$live_id])->save(['end_time'=>time(),'live_status'=>2,'is_normal_exit'=>2]);
        success('成功!');
    }

    /**
     * @结束直播(观看端)
     */
    public function live_end(){
        //$user = checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $live = M('Live')->alias('a')
            ->field('a.live_id,a.user_id,a.play_img,a.title,a.start_time,a.end_time,a.watch_nums,b.img,b.username,b.company,b.duty,b.autograph')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['live_id'=>$live_id])
            ->find();
        if(!$live)          error("参数错误");
        $live['play_img'] = C('IMG_PREFIX').$live['play_img'];
        $live['img'] = C('IMG_PREFIX').$live['img'];
        $timediff = $live['end_time']-$live['start_time'];
        //计算小时数
        $remain = $timediff%86400;
        $hours = intval($remain/3600);
        //计算分钟数
        $remain = $remain%3600;
        $mins = intval($remain/60);
        //计算秒数
        $secs = $remain%60;
        $live['time'] = $hours.":".$mins.":".$secs;
        if (empty($user_id)){
            $live['is_follow'] = "1";
        }else{
            $is_follow = M('Follow')->where(['user_id'=>$user_id,'user_id2'=>$live['user_id']])->find();
            $is_follow ? $live['is_follow'] = "2" : $live['is_follow'] = "1";
        }
        success($live);
    }

    /**
     * @礼物列表
     */
    public function gift_list(){
        checklogin();
        $list = M('Gift')->order('intime desc')->select();
        if ($list){
            foreach($list as $k=>$v){
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
            }
        }else{$list=[];}
        success($list);
    }
    /**
     * @送礼
     */
    public function give_gift(){
        $user = checklogin();
        $live_id = I('live_id'); $gift_id = I('gift_id');
        (empty($live_id) || empty($gift_id)) ? error('参数错误!') : true;
        $user_table = M('User');  $gift_table = M('Gift'); $live_table = M('Live'); $give_gift_table = M('Give_gift');
        $user_table->startTrans();  //开启事物
        $gift = $gift_table->where(['gift_id' => $gift_id])->find();
        $user['money'] - $gift['price'] < 0 ? error('余额不足!') : true;

        $user_money = $user['money']-$gift['price'];       //用户扣除之后的金额
        $user_experience = $user['experience'] + $gift['experience'];   //计算用户经验

        $up_myinfo = $user_table->where(['user_id' =>$user['user_id']])->save(['money' => $user_money,'experience'=>$user_experience,'uptime' => time()]);

        if ($up_myinfo) {

            $user_id2 = $live_table->getFieldByLive_id($live_id,'user_id');    //主播id
            $get_money = $user_table->getFieldByUser_id($user_id2, 'get_money') + $gift['price'];   //主播获得之后的金额
            $up_live_info = $user_table->where(['user_id' => $user_id2])->save(['get_money' => $get_money, 'uptime' => time()]);
            if (!$up_live_info){
                $user_table->rollback();
                error('点击太快!');
            }

            $data = [
                'user_id' => $user['user_id'],
                'live_id'=>$live_id,
                'user_id2' => $user_id2,
                'gift_id' => $gift_id,
                'intime' => time(),
                'date' => date('Y-m-d', time()),
                'jewel' => $gift['price'],
                'experience' => $gift['experience']
            ];
            $add_give_gift = $give_gift_table->add($data);
            if (!$add_give_gift){
                $user_table->rollback();
                error('点击太快!');
            }

            //等级提升
            ascension_grade($user['user_id'],$user_experience);


            $user_table->commit();
            success('成功!');
        } else {
            $user_table->rollback();
            error('失败!');
        }
    }

    /**
     * @私信送礼
     */
    public function direct_give_gift(){
        $user = checklogin();
        $user_id2 = I('user_id');  $gift_id = intval(I('gift_id'));
        (empty($user_id2) || empty($gift_id)) ? error('参数错误!') : true;
        $user_table = M('User');  $gift_table = M('Gift'); $give_gift_table = M('Give_gift');
        $user_table->startTrans();  //开启事物
        $gift = $gift_table->where(['gift_id' => $gift_id])->find();
        $user['money'] - $gift['price'] < 0 ? error('余额不足!') : true;
        $user_money = $user['money']-$gift['price'];       //用户扣除之后的金额
        $user_experience = $user['experience'] + $gift['experience'];   //计算用户经验
        $up_myinfo = $user_table->where(['user_id' =>$user['user_id']])->save(['money' => $user_money,'experience'=>$user_experience,'uptime' => time()]);
        if ($up_myinfo) {
            $get_money = $user_table->getFieldByUser_id($user_id2, 'get_money') + $gift['price'];   //主播获得之后的金额
            $up_live_info = $user_table->where(['user_id' => $user_id2])->save(['get_money' => $get_money, 'uptime' => time()]);
            if (!$up_live_info){
                $user_table->rollback();
                error('点击太快!');
            }$data = [
                'user_id' => $user['user_id'],
                'user_id2' => $user_id2,
                'gift_id' => $gift_id,
                'intime' => time(),
                'date' => date('Y-m-d', time()),
                'jewel' => $gift['price'],
                'experience' => $gift['experience'],
                'type'=>2
            ];
            $add_give_gift = $give_gift_table->add($data);
            if (!$add_give_gift){
                $user_table->rollback();
                error('点击太快!');
            }

            //等级提升
            ascension_grade($user['user_id'],$user_experience);


            $user_table->commit();
            success('成功!');
        } else {
            $user_table->rollback();
            error('失败!');
        }
    }

    /**
     * @弹幕扣钱
     */
    public function screen_price(){
        $user = checklogin();
        $live_id = I('live_id');  empty($live_id) ? error('参数错误!') : true;
        $system_table = M('System'); $user_table = M('User');  $live_table = M('Live'); $screen_table = M('Screen');
        $user_table->startTrans();  //开启事物
        $price = $system_table->where(['id'=>1])->field('screen_price,experience')->find();
        $user['money']-$price['screen_price']<0 ? error('余额不足!') : true;
        $user_money = $user['money']-$price['screen_price'];
        $user_experience = $user['experience'] + $price['experience'];   //计算用户经验
        $up_myinfo = $user_table->where(['user_id'=>$user['user_id']])->save(['money'=>$user_money,'experience'=>$user_experience,'uptime'=>time()]);

        if ($up_myinfo){
            $live_user_id = $live_table->where(['live_id'=>$live_id])->getField('user_id'); //主播id
            $u_money = $user_table->where(['user_id'=>$live_user_id])->getField('get_money')+$price['screen_price'];
            $up_live_info = $user_table->where(['user_id'=>$live_user_id])->save(['get_money'=>$u_money,'uptime'=>time()]);
            if (!$up_live_info){
                $user_table->rollback();
                error('失败!');
            }

            $add_screen = $screen_table->add(['live_id'=>$live_id,'user_id'=>$live_user_id,'user_id2'=>$user['user_id'],'intime'=>time()]);
            if (!$add_screen){
                $user_table->rollback();
                error('失败!');
            }

            //等级提升
            ascension_grade($user['user_id'],$user_experience);

            $user_table->commit();
            success('成功!');
        }else{
            $user_table->rollback();
            error('失败!');
        }
    }

    /**
     * @按home键
     * $type   1:退出   2:进入
     */
    public function home_live(){
        checklogin();
        $user_id = I('uid');
        $type = I('type');
        empty($type) ? error('参数错误!') : true;   ($type==1 || $type==2) ? true : error('传值错误!');
        $live = M('Live')->where(['user_id'=>$user_id,'live_status'=>1])->find();
        switch ($type){
            case 1:
                if ($live){
                    M('Live')->where(['live_id'=>$live['live_id']])->save(['live_time'=>time(),'uptime'=>time()]);
                }
                success('成功!');
                break;
            case 2:
                if ($live){
                    if (!empty($live['live_time'])){
                        if (time() - $live['live_time'] > 3 * 60) {
                            M('Live')->where(['live_id'=>$live['live_id']])->save(['live_status'=>2,'end_time'=>time(),'uptime'=>time()]);
                            //保存视频
                            import('Vendor.Qiniu.Pili');
                            $system = M('System')->where(['id' => 1])->find();
                            $ak = $system['ak'];
                            $sk = $system['sk'];
                            $hubName = $system['hubname'];
                            $mac = new \Qiniu\Pili\Mac($ak, $sk);
                            $client = new \Qiniu\Pili\Client($mac);
                            $hub = $client->hub($hubName);
                            //获取stream
                            $streamKey = $live['stream_key'];
                            $stream = $hub->stream($streamKey);
                            //保存直播数据
                            $fname = $stream->save(0, 0);
                            if ($fname['fname']) {
                                $data = [
                                    'live_id' => $live['live_id'],
                                    'user_id' => $live['user_id'],
                                    'play_img' => $live['play_img'],
                                    'title' => $live['title'],
                                    'url' => 'http://oc3pwoyhb.bkt.clouddn.com/' . $fname['fname'],
                                    'intime' => time(),
                                    'room_id' => $live['room_id'],
                                    'date'=>date('Y-m-d',time())
                                ];
                                $stream->disable();
                                M('Live_store')->add($data);
                            }
                            error("直播结束");
                        }else{
                            $result = ['nums' => $live['watch_nums'], 'push_flow_address' => $live['push_flow_address'], 'room_id' => $live['room_id'], 'ID' => M('User')->getFieldByUser_id($user_id,'ID'), 'money' => M('User')->getFieldByUser_id($user_id,'get_money'), 'start_time' =>$live['start_time'], 'url' => M('User')->getFieldByUser_id($user_id,'url')];
                        }
                    }else{
                        M('Live')->save(['live_status'=>1,'live_time'=>0,'uptime'=>time()]);
                        $result = ['nums' => $live['watch_nums'], 'push_flow_address' => $live['push_flow_address'], 'room_id' => $live['room_id'], 'ID' => M('User')->getFieldByUser_id($user_id,'ID'), 'money' => M('User')->getFieldByUser_id($user_id,'get_money'), 'start_time' =>$live['start_time'], 'url' => M('User')->getFieldByUser_id($user_id,'url')];
                    }
                    success($result);
                }else{
                    error('没有直播!');
                }
                break;
        }
    }
    /**
     *判断主播是否下线
     */
    public function check_anchor_state(){
            checklogin();
            $anchor_id = I('user_id');
            $live = M('Live')->where(['user_id'=>$anchor_id,'live_status'=>1])->find();
            if(!$live){
                error("主播已下线");
            }else{
                success("ok");
            }
    }


//    /**
//     * @拉黑
//     */
//    public function shield(){
//        checklogin();
//        $user_id = I('uid');
//        $user_id2 = I('user_id'); $live_id = I('live_id');
//        $uid = M('Live')->getFieldByLive_id($live_id,'user_id');
//        (empty($user_id2) || empty($live_id)) ? error('参数错误!') : true;
//        $shield = M('Shield')->where(['user_id'=>$uid,'user_id2'=>$user_id2])->find();
//        if ($shield){
//            error('已拉黑!');
//        }else{
//            if (M('Shield')->add(['live_id'=>$live_id,'user_id'=>$uid,'user_id2'=>$user_id2,'intime'=>time()])){
//                //环信拉黑
//                $hx_username = M('User')->getFieldByUser_id($user_id,'hx_username');
//                $hx_username2 = M('User')->getFieldByUser_id($user_id2,'hx_username');
//                addUserForBlacklist($hx_username,$hx_username2);
//
//                //把拉黑的从直播间踢出去
//                M('Live_number')->where(['live_id'=>$live_id,'user_id'=>$uid,'user_id2'=>$user_id2])->delete();
//
//                success($user_id2);
//            }else{
//                error('失败!');
//            }
//        }
//    }

    /**
     * @拉黑
     * @type  1:拉黑   2:取消拉黑
     */
    public function shield(){
        $user = checklogin();
        $live_id = I('live_id');
        $user_id2 = I('user_id');
        if ($live_id){
            $user_id = M('Live')->where(['live_id'=>$live_id])->getField('user_id');
        }else{
            $user_id = $user['user_id'];
        }
        (empty($user_id2)) ? error('参数错误!') : true;
        $shield = M('Shield')->where(['user_id'=>$user_id,'user_id2'=>$user_id2])->find();
        if ($shield) {
            if (M('Shield')->where(['shield_id'=>$shield['shield_id']])->delete()){
                $hx_username = M('User')->getFieldByUser_id($user_id,'hx_username');
                $hx_username2 = M('User')->getFieldByUser_id($user_id2,'hx_username');
                deleteUserFromBlacklist($hx_username,$hx_username2);  //环信移除黑名单
                success("1");
            }else{
                error('失败!');
            }
        }else {
            if (M('Shield')->add(['user_id'=>$user_id,'user_id2'=>$user_id2,'intime'=>time()])){
                //环信拉黑
                $hx_username = M('User')->getFieldByUser_id($user_id,'hx_username');
                $hx_username2 = M('User')->getFieldByUser_id($user_id2,'hx_username');
                addUserForBlacklist($hx_username,$hx_username2);

                //关注关系取消
                $follow = M('Follow')->where(['user_id'=>$user_id,'user_id2'=>$user_id2])->find();
                if ($follow){M('Follow')->where(['follow_id'=>$follow['follow_id']])->delete();}
                $follow2 = M('Follow')->where(['user_id'=>$user_id2,'user_id2'=>$user_id])->find();
                if ($follow2){M('Follow')->where(['follow_id'=>$follow2['follow_id']])->delete();}
                success("2");
            }else{
                error('失败!');
            }
        }

    }

    /**
     * @判断直播间中用户是否被拉黑、禁言、是否是管理
     */
    public function check_user(){
        $user = checklogin();
        $live_id = I('live_id');   $user_id = I('user_id');
        empty($live_id) ? error('参数错误!') : true;
        $live = M('Live')->find($live_id);
        $shield = M('Shield')->where(['user_id'=>$live['user_id'],'user_id2'=>$user_id])->find();    //是否被拉黑
        $shield ? $is_shield = "2" : $is_shield = "1";
        $banned = M('Banned')->where(['live_id'=>$live_id,'user_id2'=>$user_id])->find();      //是否被禁言
        $banned ? $is_banned = "2" : $is_banned = "1";
        $management = M('Live_management')->where(['user_id'=>$live['user_id'],'user_id2'=>$user_id])->find();  //是否是管理
        $management ? $is_management = "2" : $is_management = "1";
        $result = ['is_shield'=>$is_shield,'is_banned'=>$is_banned,'is_management'=>$is_management];
        success($result);
    }

    /**
     * @当前登录用户的余额
     */
    public function get_money(){
        $user = checklogin();
        $result = ['money'=>$user['money'],'grade'=>$user['grade']];
        success($result);
    }

    /**
     * @获取用户火力
     */
    public function get_get_money(){
//        $user = checklogin();
//        $user_id = I('user_id');
//        if (empty($user_id)){
//            $money = $user['get_money'];
//        }else{
//            $money = M('User')->getFieldByUser_id($user_id,'get_money');
//        }
        $user = checklogin();
        $user_id = I('user_id');
        if (empty($user_id)){
            $money = M('Give_gift')->where(['user_id2'=>$user['user_id']])->sum('jewel');
            $money ? $money = $money : $money = "0";
        }else{
            $money = M('Give_gift')->where(['user_id2'=>$user_id])->sum('jewel');
            $money ? $money = $money : $money = "0";
        }
        success($money);
    }


    /**
     * @每分钟更新一下,
     */
    public function is_receive(){
        $live_id = I('live_id');
        $user_id = I('user_id');
        (empty($live_id) || empty($user_id)) ? error('参数错误!') : true;
        $re = M('Receive')->where(['live_id'=>$live_id,'user_id'=>$user_id])->find();
        if ($re){
            M('Receive')->where(['receive_id'=>$re['receive_id']])->save(['intime'=>time()]);
        }else{
            M('Receive')->add(['live_id'=>$live_id,'user_id'=>$user_id,'intime'=>time()]);
        }
        success('成功!');
    }

    /**
     * @充值价格列表
     */
    public function price_list(){
        checklogin();
        $list = M('Price')->select();
        if (!$list){$list=[];}
        success($list);
    }



    /**
     * @轮播图
     */
    public function banner_list(){
        $list = M('Banner')->order('remark desc')->select();
        if ($list){
            foreach ($list as $k=>$v){
                $list[$k]['b_img'] = C('IMG_PREFIX').$v['b_img'];
                $list[$k]['url'] = C('IMG_PREFIX')."/api.php/Index/banner/b_id/" . base64_encode($v['b_id']);
                $list[$k]['content'] = "";
            }
        }else{$list=[];}
        success($list);
    }
    /**
     * @轮播图内容
     */
    public function banner(){
        $b_id = base64_decode(I('b_id'));
        $content = M('Banner')->where(['b_id'=>$b_id])->getField('content');
        $this->assign('content',htmlspecialchars_decode($content));
        $this->display();
    }




    /****************************************************关注********************************************************************/

    /**
     * @关注列表
     */
    public function follow_list(){
        $user = checklogin();
        $page = I('page');
        $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        //直播列表
        $list1 = M('Follow')
            ->alias('a')
            ->field('b.user_id,b.username,b.img,b.ID,b.get_money,b.hx_username,c.live_id,c.play_img,c.title,c.play_address,c.start_time,c.room_id,c.watch_nums,c.nums')
            ->join('__USER__ b on a.user_id2=b.user_id')
            ->join('__LIVE__ c on a.user_id2=c.user_id')
            ->where(['a.user_id'=>$user['user_id'],'c.live_status'=>1])
            ->order('a.intime desc')
            ->select();
        if ($list1){
            foreach ($list1 as $k=>$v){
                $list1[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $list1[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
            }
        }else{$list1=[];}
        //视频列表
        $list2 = M('Follow')
            ->alias('a')
            ->field('b.img,b.username,b.get_money,c.*')
            ->join('__USER__ b on a.user_id2=b.user_id')
            ->join('__VIDEO__ c on a.user_id2=c.user_id')
            ->where(['a.user_id'=>$user['user_id'],'c.is_del'=>1])
            ->order('a.intime desc')
            ->page($page,$pageSize)
            ->select();
        if ($list2){
            foreach ($list2 as $k=>$v){
                $list2[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $list2[$k]['url'] = C('IMG_PREFIX').$v['url'];
                $list2[$k]['video_img'] = C('IMG_PREFIX').$v['video_img'];
            }
        }else{$list2=[];}
        $list = ['list1'=>$list1,'list2'=>$list2];
        success($list);
    }





    /**********************************************视频**********************************************************/

    /**
     * @上传视频
     */
    public function add_video(){
        $user = checklogin();
        $title = I('title');
        $title ? $title = replace_string($title) : $title = "";
        $data = [
            'title'=>$title,
            'user_id'=>$user['user_id'],
            'intime'=>time(),
            'date'=>date('Y-m-d',time()),
        ];
        $config = [
            'maxSize'	=> 30*3145728,
            'rootPath'	=> './Public/admin/Uploads/video/',
            'savePath'	=> '',
            'saveName'	=> ['uniqid',''],
            'exts'		=> ['png','jpg','jpeg','git','gif','mp4','flv','avi','mov'],
            'autoSub'	=> true,
            'subName'	=> '',
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
        if ($info){
            foreach($info as $file){
                if ($file['key']=='video_img'){
                    $data['video_img'] = '/Public/admin/Uploads/video/'.$file["savename"];
                }
                if ($file['key']=='video'){
                    $data['url'] = '/Public/admin/Uploads/video/'.$file["savename"];
                }
            }
        }else {
            error($uploader->getError());
        }
        if (M('Video')->add($data)){
            success('成功!');
        }else{
            error('失败!');
        }
    }


    /**
     * @上传视频
     */
    public function up_video(){
        $config = [
            'maxSize'	=> 30*3145728,
            'rootPath'	=> './Public/admin/Uploads/video/',
            'savePath'	=> '',
            'saveName'	=> ['uniqid',''],
            'exts'		=> ['png','jpg','jpeg','git','gif','mp4','flv','avi','mov'],
            'autoSub'	=> true,
            'subName'	=> '',
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
        if ($info){
            foreach($info as $file){
               $a = '/Public/admin/Uploads/video/'.$file["savename"];
            }
            success(C('IMG_PREFIX').$a);
        }else {
            error($uploader->getError());
        }
    }



    /**
     * @视频列表
     */
    public function video_list(){
        $page = I('page');
        $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        $list = M('Video')
            ->alias('a')
            ->field('a.*,b.img,b.username,b.get_money')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['a.is_del'=>1,'a.is_shenhe'=>2])
            ->order('rand()')
            ->page($page,$pageSize)
            ->select();
        if ($list){
            foreach ($list as $k=>$v){
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $list[$k]['video_img'] = C('IMG_PREFIX').$v['video_img'];
                $list[$k]['url'] = C('IMG_PREFIX').$v['url'];
            }
        }else{$list=[];}
        success($list);
    }

    /**
     * @点击进入视频
     */
    public function video_details(){
        $user_id = I('user_id');
        $video_id = I('video_id');
        empty($video_id) ? error('参数错误!') : true;
        M('Video')->comment('观看数加1')->where(['video_id'=>$video_id])->setInc('watch_nums');
        $details = M('Video')
            ->alias('a')
            ->field('a.*,b.img,b.username,b.hx_username')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['a.video_id'=>$video_id])
            ->find();
        $details['video_img'] = C('IMG_PREFIX').$details['video_img'];
        $details['url'] = C('IMG_PREFIX').$details['url'];
        $details['img'] = C('IMG_PREFIX').$details['img'];
        if ($user_id){
            $is_zan = M('Video_zan')->where(['video_id'=>$video_id,'user_id'=>$user_id])->find();
            $is_zan ? $details['is_zan'] = "2" : $details['is_zan'] = "1";
            $is_follow = M('Follow')->where(['user_id'=>$user_id,'user_id2'=>$details['user_id']])->find();
            $is_follow ? $details['is_follow'] = "2" : $details['is_follow'] = "1";
        }else{
            $details['is_zan'] = "1";
            $details['is_follow'] = "1";
        }
        success($details);
    }

    /**
     * @视频点赞(取消点赞)
     * @state   1:点赞   2:取消点赞
     */
    public function video_zan(){
        $user = checklogin();
        $state = I('state');  $video_id = I('video_id');
        (empty($video_id) || empty($state)) ? error('参数错误!') : true;  ($state==1 || $state==2) ? true : error('传值错误!');
        $zan = M('Video_zan')->where(['video_id'=>$video_id,'user_id'=>$user['user_id']])->find();
        switch ($state){
            case 1:
                if ($zan){
                    error('已点过赞');
                }else{
                    if (M('Video_zan')->add(['video_id'=>$video_id,'user_id'=>$user['user_id'],'intime'=>time()])){
                        M('Video')->comment('赞加1')->where(['video_id'=>$video_id])->setInc('zan');
                        //添加消息
                        $d = [
                            'type'=>3,
                            'user_id'=>$user['user_id'],
                            'user_id2'=>M('Video')->where(['video_id'=>$video_id])->getField('user_id'),
                            'intime'=>time(),
                            'date'=>date('Y-m-d',time()),
                            'video_id'=>$video_id
                        ];
                        M('Message')->add($d);
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }
                break;
            case 2:
                if ($zan){
                    if (M('Video_zan')->where(['video_zan_id'=>$zan['video_zan_id']])->delete()) {
                        M('Video')->comment('赞减1')->where(['video_id' => $video_id])->setDec('zan');
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }else{
                    error('未点过赞!');
                }
                break;
        }
    }


    /**
     * @评论视频
     * @type   1:评论动态   2:评论评论
     */
    public function comment_video(){
        $user = checklogin();
        $type = I('type');
        $video_id = I('video_id'); $video_comments_id = I('video_comments_id'); $content = I('content');
        (empty($video_id) || empty($type) || empty($content)) ? error('参数错误!') : true;  ($type==1 || $type==2) ? true : error('传值错误!');
        $content = replace_string($content);
        $data = [
            'video_id'=>$video_id,
            'user_id'=>$user['user_id'],
            'content'=>$content,
            'intime'=>time(),
            'type'=>$type,
        ];
        switch ($type){
            case 1:
                $data['fid'] = "0";
                break;
            case 2:
                empty($video_comments_id) ? error('参数错误!') : true;
                $data['fid'] = $video_comments_id;
                break;
        }
        $v = M('Video_comments')->add($data);
        if ($v){
            M('Video')->comment('评论数加1')->where(['video_id'=>$video_id])->setInc('comments');
            $m =[
                'type'=>2,
                'user_id'=>$user['user_id'],
                'user_id2'=>M('Video')->where(['video_id'=>$video_id])->getField('user_id'),
                'content'=>$content,
                'intime'=>time(),
                'date'=>date('Y-m-d',time()),
                'video_id'=>$video_id,
            ];
            if ($type==1){
                $m['status'] = 1;
            }else{
                $m['video_comments_id'] = $v;
                $m['status'] = 2;
            }
            M('Message')->add($m);
            success('成功!');
        }else{
            error('失败!');
        }
    }

    /**
     * @评论列表
     */
    public function comment_list(){
        $user_id = I('user_id');
        $video_id = I('video_id');
        $page = I('page');
        $pageSize = I('pagesize');
        $page ? $page : $page = 1;
        $pageSize ? $pageSize : $pageSize = 10;
        empty($video_id) ? error('参数错误!') : true;
        $list = M('Video_comments')
            ->alias('a')
            ->field('a.*,b.username,b.img,b.hx_username')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['a.video_id' => $video_id])
            ->page($page, $pageSize)
            ->order('a.intime desc')
            ->select();
        if ($list) {
            foreach ($list as $k => $v) {
                $list[$k]['img'] = C('IMG_PREFIX') . $v['img'];
                $list[$k]['intime'] = date('Y-m-d H:i', $v['intime']);
                $list[$k]['times'] = get_times($v['intime']);
                if ($user_id){
                    $is_zan = M('Video_comments_zan')->comment('查询是否点赞')->where(['video_id_comments_id' => $v['video_comments_id'], 'user_id' => $user_id])->find();
                    $is_zan ? $list[$k]['is_zan'] = "2" : $list[$k]['is_zan'] = "1";
                }else{
                    $list[$k]['is_zan'] = "1";
                }
                if ($v['type']==2){
                    $user_id2 = M('Video_comments')->where(['video_comments_id'=>$v['fid']])->getField('user_id');
                    $list[$k]['username2'] = M('User')->where(['user_id'=>$user_id2])->getField('username');
                }
            }
        } else {
            $list = [];
        }
        success($list);
    }

    /**
     * @评论点赞
     * @state 1:赞  2:取消赞
     */
    public function comment_zan(){
        $user = checklogin();
        $state = I('state');  $video_comments_id = I('video_comments_id');
        (empty($video_comments_id) || empty($state)) ? error('参数错误!') : true;
        ($state==1 || $state==2) ? true : error('传值错误!');
        $zan = M('Video_comments_zan')->where(['video_comments_id'=>$video_comments_id,'user_id'=>$user['user_id']])->find();
        switch ($state){
            case 1:
                if ($zan){
                    error('已赞过!');
                }else{
                    if (M('Video_comments_zan')->add(['video_comments_id'=>$video_comments_id,'user_id'=>$user['user_id'],'intime'=>time()])){
                        M('Video_comments')->comment('赞加1')->where(['video_comments_id'=>$video_comments_id])->setInc('zan');
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }
                break;
            case 2:
                if ($zan){
                    if (M('Video_comments_zan')->where(['video_comments_zan_id'=>$zan['video_comments_zan_id']])->delete()){
                        M('Video_comments')->comment('赞减1')->where(['video_comments_id'=>$video_comments_id])->setDec('zan');
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }else{
                    error('未赞过!');
                }
                break;
        }
    }

    /**
     * @视频分享
     */
    public function share(){
        $video_id = I('video_id');
        empty($video_id) ? error('参数错误!') : true;
        if (M('Video')->where(['video_id'=>$video_id])->setInc('share')){
            success('成功!');
        }else{
            error('失败!');
        }
    }





    /**
     * @搜索关键词
     */
    public function search(){
        $user = checklogin();
        $keywords = I('keywords');
        $page = I('page');   $pageSize = I('pagesize');
        $page ? $page : $page = 1;   $pageSize ? $pageSize : $pageSize = 10;
        empty($keywords) ? error('参数错误!') : true;
        $data['username|ID|autograph'] = ['like','%'.$keywords.'%'];
        $data['is_del'] = 1;
        $list = M('User')
            ->field('user_id,username,img,sex,autograph,hx_username,ID,grade')
            ->where($data)
            ->order('get_money desc')
            ->page($page,$pageSize)
            ->select();
        if ($list){
            foreach ($list as $k=>$v){
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $is_follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                $is_follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";

                $get_gradeinfo = get_gradeinfo($v['grade']);
                $list[$k]['grade_img'] = $get_gradeinfo['img'];
                $list[$k]['name'] = $get_gradeinfo['name'];
            }
        }else{$list=[];}
        success($list);
    }


    /**
     * @推荐主播
     */
    public function recommend_list(){
        $user = checklogin();
        $page = I('page');   $pageSize = I('pagesize');
        $page ? $page : $page = 1;   $pageSize ? $pageSize : $pageSize = 10;
        $data = [
            'a.live_status'=>1,
            'b.type'=>1,
            'b.is_del'=>1,
        ];
        $list = M('Live')
            ->alias('a')
            ->field('a.*,b.phone,b.img,b.sex,b.username,b.ID,b.hx_username,b.grade,b.province,b.city,b.zan,b.money,b.get_money,b.url')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where($data)
            ->order('a.watch_nums desc')
            ->page($page,$pageSize)
            ->select();
        if ($list){
            foreach ($list as $k=>$v){
                $list[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $list[$k]['url'] = C('IMG_PREFIX')."/api.php/Index/share_live/live_id/" . base64_encode($v['live_id']);
                $list[$k]['qrcode_path'] = C('IMG_PREFIX').$v['qrcode_path'];

                $get_gradeinfo = get_gradeinfo($v['grade']);
                $list[$k]['grade_img'] = $get_gradeinfo['img'];
                $list[$k]['name'] = $get_gradeinfo['name'];
            }
        }else{$list=[];}
        success($list);
    }

    /**
     * @他人主页
     */
    public function other_center(){
        $user = checklogin();
        $user_id = I('user_id');
        empty($user_id) ? error('参数错误!') : true;
        $u = M('User')->find($user_id);
        $u['img'] = C('IMG_PREFIX').$u['img'];
        $u['get_money'] = FormatMoney($u['get_money']);
        $follow = M('Follow')
            ->alias('a')
            ->join('__USER__ b on a.user_id2=b.user_id')
            ->where(['a.user_id'=>$user_id,'b.is_del'=>1])
            ->count();
        $u['follow'] = FormatMoney($follow);
        $follow_to = M('Follow')->alias('a')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['a.user_id2'=>$user_id,'b.is_del'=>1])
            ->count();
        $u['follow_to'] = FormatMoney($follow_to);
        $u['collection'] = M('Collection')->where(['user_id'=>$user_id])->count();
        $is_follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id])->find();
        $is_follow ? $u['is_follow'] = "2" : $u['is_follow'] = "1";
        //$u['video_count'] = M('Video')->where(['user_id'=>$user_id,'is_del'=>1])->count();    //视频数量
        //$u['imgs_count'] = FormatMoney(M('User_imgs')->where(['user_id'=>$user_id])->count());
        $give_count = M('Give_gift')->where(['user_id'=>$user_id])->sum('jewel');
        $give_count ? $u['give_count'] = FormatMoney($give_count) : $u['give_count'] = "0";
        $live_count = M('Live_store')
            ->alias('a')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['a.user_id'=>$user_id,'a.is_del'=>1])
            ->count();
        $u['live_count'] = FormatMoney($live_count);

        $get_gradeinfo = get_gradeinfo($u['grade']);
        $u['grade_img'] = $get_gradeinfo['img'];
        $u['name'] = $get_gradeinfo['name'];



        //判断是否正在直播
        $live = M('Live')
            ->alias('a')
            ->field('a.*,b.username,b.img,b.grade,b.hx_username,b.ID,b.sex,b.province,b.city,b.money,b.get_money,b.type')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['a.user_id'=>$user_id,'a.live_status'=>1])
            ->find();
        if ($live){
            $u['is_live'] = "2";
            $live['img'] = C('IMG_PREFIX').$live['img'];
            $live['play_img'] = C('IMG_PREFIX').$live['play_img'];
            $live['qrcode_path'] = C('IMG_PREFIX').$live['qrcode_path'];
            $live['url'] = C('IMG_PREFIX')."/api.php/Index/share_live/live_id/" . base64_encode($live['live_id']);

            $get_gradeinfo = get_gradeinfo($live['grade']);
            $live['grade_img'] = $get_gradeinfo['img'];
            $live['name'] = $get_gradeinfo['name'];

            $u['live'] = $live;
        }else{
            $u['is_live'] = "1";
        }
        if($u['type'] == 2){
            $sum = M('UserMark')->where(['user_id2'=>$u['user_id']])->sum('mark');
            $count = M('UserMark')->where(['user_id2'=>$u['user_id']])->count();
            $user['mark'] = sprintf("%.1f",(5 + $sum)/($count+1));
            $top = M('GiveGift')->alias('a')
                ->field("b.img,b.username,b.hx_username,b.hx_password,a.user_id,sum(a.jewel) as jewel")
                ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
                ->where(['a.user_id2'=>$u['user_id']])
                ->order("jewel desc")->limit(3)
                ->group("a.user_id")->select();
        }
        if($top){
            foreach($top as $k=>$v){
                $top[$k]['img'] = $this->url.$v['img'];
            }
            $u['top'] = $top;
        }else{
            $u['top'] = [];
        }
        success($u);
    }

    /**
     * @黑名单
     */
    public function shield_list(){
        $user = checklogin();
        $p = I('p');
        $pageSize = I('pagesize');
        $p ? $p : $p = 1;  $pageSize ? $pageSize : $pageSize = 10;
        $count = M('Shield')->alias('a')
            ->field('a.shield_id,a.intime,b.user_id,b.username,b.img,b.company,b.duty,b.ID,b.hx_username,b.sex,b.grade,b.autograph')
            ->join('__USER__ b on a.user_id2=b.user_id')
            ->where(['a.user_id'=>$user['user_id']])
            ->count();
        $list = M('Shield')->alias('a')
            ->field('a.shield_id,a.intime,b.user_id,b.username,b.img,b.company,b.duty,b.ID,b.hx_username,b.sex,b.grade,b.autograph')
            ->join('__USER__ b on a.user_id2=b.user_id')
            ->where(['a.user_id'=>$user['user_id']])
            ->limit(($p-1)*$pageSize,$pageSize)
            ->order('a.intime desc')
            ->select();
        $page = ceil($count/$pageSize);
        if ($list){
            foreach ($list as $k=>$v) {
                $list[$k]['intime'] = date('Y-m-d H:i:s',$v['intime']);
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];

                $get_gradeinfo = get_gradeinfo($v['grade']);
                $list[$k]['grade_img'] = $get_gradeinfo['img'];
                $list[$k]['name'] = $get_gradeinfo['name'];
            }
        }else{$list=[];}
        success(['page'=>$page,'list'=>$list]);
    }


    /**
     * @取消黑名单
     */
    public function del_shield(){
        $user = checklogin();
        $shield_id = I('shield_id');
        empty($shield_id) ? error('参数错误!') : true;
        $shield = M('Shield')->find($shield_id);
        if (M('Shield')->where(['shield_id'=>$shield_id])->delete()){
            $hx_username = M('User')->where(['user_id'=>$shield['user_id2']])->getField('hx_username');
            deleteUserFromBlacklist($user['hx_username'],$hx_username);  //环信移除黑名单
            success('成功!');
        }else{
            error('失败!');
        }
    }






































}







