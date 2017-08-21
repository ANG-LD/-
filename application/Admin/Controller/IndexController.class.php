<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends BaseController {
    public function index(){
//        $str = '2016年07月26日 15时35分';
//        preg_match_all('/\d/',$str,$arr);
//        $timer=implode('',$arr[0]);
//        echo $timer;die;
//        $time = strtotime('201607261535');
//        echo $time;die;
//        $aa = array();
//
//        $list = M("Order")->table("qj_order as a")->join("qj_distance as b ON a.order_id=b.`order_id`")->where("((".time()."-a.`date`< 30 AND b.distance <6) OR  (".time()."-a.`date` < 60 AND b.distance <11)) OR (".time()."-a.`date`<90 and b.distance<16) or (".time()."-a.`date`<120 and b.distance<20) or (".time()."-a.`date`>120 and b.distance>20)  AND b.`user_id`=3")->field("a.*,b.*")->select();
//        //echo M("Order")->getLastSql();die;
//        dump($list);die;
//        foreach($list as $k=>$v){
//            $dis = M('Distance')->where(array('user_id'=>3,'order_id'=>$v['order_id']))->getField('distance');
//            $time = time()-$v['date'];
//            if($dis<6 && $time<31){
//                    $aa[] = $list[$k];
//            }else if($dis>5 && $dis<11 && $time<61){
//                    $aa[] = $list[$k];
//            }else if($dis>10 && $dis<16 && $time<91){
//                    $aa[] = $list[$k];
//            }else if($dis>15 && $dis<21 && $time<121){
//                    $aa[] = $list[$k];
//            }else if($dis>20 && $time>120){
//                    $aa[] = $list[$k];
//            }
//
//        }
//        dump($aa);die;
//        $shopping = array('2' => array('shop_id'=>2,'nums'=>3),'3' => array('shop_id'=>2,'nums'=>3),'4' => array('shop_id'=>2,'nums'=>3));
//        $aa = serialize($shopping);
//        $bb = unserialize($aa);
//        echo $aa;
//        dump($bb);die;
        //服务器信息
        if (function_exists('gd_info')) {
            $gd = gd_info();
            $gd = $gd['GD Version'];
        } else {
            $gd = "不支持";
        }
        $info = array(
            '操作系统' => PHP_OS,
            '主机名IP端口' => $_SERVER['SERVER_NAME'] . ' (' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . ')',
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            '程序目录' => WEB_ROOT,
            'MYSQL版本' => function_exists("mysql_close") ? mysql_get_client_info() : '不支持',
            'GD库版本' => $gd,
//            'MYSQL版本' => mysql_get_server_info(),
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . "秒",
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '采集函数检测' => ini_get('allow_url_fopen') ? '支持' : '不支持',
            'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF",
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? 'YES' : 'NO',
        );
        $this->assign(['server_info'=>$info]);
        $this->display();
    }
    function test(){

    $data = 'a:4:{i:0;a:3:{s:3:"url";s:19:"/Uploads/weigui.png";s:5:"width";s:3:"480";s:6:"height";s:3:"360";}i:1;a:3:{s:3:"url";s:19:"/Uploads/weigui.png";s:5:"width";s:3:"480";s:6:"height";s:3:"360";}i:2;a:3:{s:3:"url";s:19:"/Uploads/weigui.png";s:5:"width";s:3:"480";s:6:"height";s:3:"360";}i:3;a:3:{s:3:"url";s:19:"/Uploads/weigui.png";s:5:"width";s:3:"480";s:6:"height";s:3:"360";}}';
        echo json_encode(unserialize($data));
        die;
    }
    function text2(){
        import('Vendor.Qiniu.Pili');
        $ak = "pR_CsEkFcTn1Kgf8ZNIh2zUB_w8bzaeLYEgjBItT";
        $sk = "Vr2R_DMBvVHAtVmcwVGKF_C-ol6jDtCXqpiXlZZY";
        $hubName = "vxiu1";
        //创建hub
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        $hub = $client->hub($hubName);
       //获取stream
        $streamKey = "php-sdk-test" . time();
        $stream = $hub->stream($streamKey);

        try {
            //创建stream
            $resp = $hub->create($streamKey);
            //获取stream info
            $resp = $stream->info();
            //列出所有流
            $resp = $hub->listStreams("php-sdk-test", 1, "");
            //列出正在直播的流
            $resp = $hub->listLiveStreams("php-sdk-test", 1, "");
        } catch (\Exception $e) {
            echo "Error:", $e, "\n";
        }



        try {
            //启用流
            $stream->enable();
            $status = $stream->liveStatus();
        } catch (\Exception $e) {
            echo "Error:", $e, "\n";
        }
        try {
            //保存直播数据
            $fname = $stream->save(1463217523, 1463303923);
        } catch (\Exception $e) {
            echo "Error:", $e, "\n";
        }






//RTMP 推流地址
        $url = \Qiniu\Pili\RTMPPublishURL("qntest.tstmobile.com", $hubName, $streamKey, 3600, $ak, $sk);
        echo $url, "<br />";
//RTMP 直播放址
        $url2 = \Qiniu\Pili\RTMPPlayURL("pili-live-rtmp.tstmobile.com", $hubName, $streamKey);
        echo $url2, "\n";


    }

    public function test3(){
        $top = M('GiveGift')->alias('a')
            ->field("b.img,b.username,b.hx_username,b.hx_password,a.user_id,sum(a.jewel) as jewel")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where(['a.user_id2'=>'423'])
            ->group("a.user_id")
            ->order("jewel desc")->limit(3)
            ->select();
        success($top);
    }

    public function test4(){
        $qiniu_room_id = time().rand(100, 999);
        $qiniu_room_name = GetfourStr(6);
        $create_room = creatroom($qiniu_room_id,$qiniu_room_name);
        success($create_room);
    }

    public function test5(){
        $address = '112.124.108.16';
        $service_port = 5060;

        // 创建并返回一个套接字（通讯节点）
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        success($socket);
        if ($socket === false) {
            echo "socket_create() failed, reason: ".socket_strerror(socket_last_error())."\n";
        }

        echo "Attempting to connect to '$address' on port '$service_port'...";
        // 发起socket连接请求
        $result = socket_connect($socket, $address, $service_port);
        if($result === false) {
            echo "socket_connect() failed, reason: ".socket_strerror(socket_last_error($socket))."\n";
        } else {
            echo "Connect success. \n";
        }

        $input = "This is a message from client"."\n";

        // 向socket服务器发送消息
        socket_write($socket, $input, strlen($input));
        echo  "Client send success \n";

        echo "Reading response:\n";
        // 读取socket服务器发送的消息
        while ($out = socket_read($socket, 8192)) {
            echo $out;
        }
        echo PHP_EOL;
        socket_close($socket); // 关闭socket连接
    }

    public function test6(){
        // 设置脚本最大执行时间，单位为秒，0表示永不超时
        set_time_limit(0);

        $address = '127.0.0.1';
        $port = 10005;   // 端口可以是1到65535之间的任何数字，前提是未被占用

        // 创建并返回一个套接字（通讯节点），一个典型的网络连接由 2 个套接字构成，一个运行在服务器端，另一个运行在客户端
        if( ($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            echo "socket_create() failed, reason: " . socket_strerror(socket_last_error()) . "\n";
        }

        // 绑定socket到具体的主机端口
        if (socket_bind($sock, $address, $port) === false) {
            echo "socket_bind() failed, reason: " . socket_strerror(socket_last_error($sock)) . "\n";
        }

        // 监听socket服务器上的请求连接，等待接入
        if (socket_listen($sock, 5) === false) {
            echo "socket_listen() failed, reason: " . socket_strerror(socket_last_error($sock)) . "\n";
        }

        do {
// 确认客户端的连接请求，成功后，返回一个新的子socket句柄（子线程），用于通信
            if (($msgsock = socket_accept($sock)) === false) {
                echo "socket_accept() failed, reason: ".socket_strerror(socket_last_error($sock)) . "\n";
                break;
            }

            $msg = "Welcome to connect '$address'"."\n";
// 发送消息（数据）到客户端
            if (false === socket_write($msgsock, $msg, strlen($msg))){
                echo "socket_write() failed, reason: " . socket_strerror(socket_last_error($sock)) ."\n";
            }

            echo "Read client message \n";
// 读取客户端的数据
            $receivedData = socket_read($msgsock, 8192);
            echo "Received message: ".$receivedData."\n";;

// 将客户端发来的数据，进行处理，然后再发送数据给客户端
            $responseData = '[time:'.date('Y-m-d H:i:s').']'.PHP_EOL.'[data:'.trim($receivedData).']';
            if (false === socket_write($msgsock, $responseData, strlen($responseData))) {
                echo "socket_write() failed, reason: " . socket_strerror(socket_last_error($sock)) ."\n";
            }

// 关闭连接成功的子socket
            socket_close($msgsock);
        } while(true);

// 关闭等待接入的socket
        socket_close($sock);
    }

    public function test7(){
        header("Content-type:text/html;Charset=UTF-8");
        $result=$this->connSocket("getmaillist\r\n{'id':2}\r\n");
        echo $result;
    }

    private function connSocket($str){
        $IP='112.124.108.16';
        $port= 5060;
        $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        $res=@socket_connect($socket,$IP,$port);
        if(!$res){
            return;
        }
        socket_write($socket,$str);
        $result="";
        while($data = socket_read($socket,1024)){
            $result.=$data;
        }
        socket_close($socket);
        return $result;
    }

    public function Send_socket_connect() {
        $service_port = '5060';
        $address= '112.124.108.16';
        $in = '{"get":"rid"}';
        //创建 TCP/IP socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("could not create socket!");
        //设置超时时间
        $timeout = 30;
        $time = time();
        //设置非阻塞模式
        @socket_set_nonblock($socket);
        //超时判断
        while (!@socket_connect($socket, $address, $service_port)) {
            $err = socket_last_error($socket);
            // 连接成功，跳出循环
            if ($err === 10056) {
                break;
            }
            //连接失败，判断超时时间，停止
            if ((time() - $time) >= $timeout) {
                socket_close($socket);
                print('网络异常，请检查网络连接！！');
                exit();
            }
            // 刷新频率（250毫秒）
            usleep(250000);
        }
        //设置阻塞模式
        @socket_set_block($socket);
        //发送命令到设备
        socket_write($socket, $in, strlen($in));
        //关闭连接
        socket_close($socket);
    }

    public function Send_socket_xdcoder() {
        $service_port = '5060';
        $address= '112.124.108.16';
        $in = '{"get":"rid"}';
        //创建 TCP/IP socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("could not create socket!");
        //设置超时时间
        $timeout = 2;
        $time = time();
        //设置非阻塞模式
        @socket_set_nonblock($socket);
        //超时判断
        while (!@socket_connect($socket, $address, $service_port)) {
            $err = socket_last_error($socket);
            // 连接成功
            if ($err === 10056) {
                break;
            }
            //连接失败，判断超时时间，停止
            if ((time() - $time) >= $timeout) {
                socket_close($socket);
                echo "<script>alert('网络异常，请检查网络连接！！');</script>";
                exit();
            }
            // 刷新频率（250毫秒）
            usleep(250000);
        }
        //设置阻塞模式
        @socket_set_block($socket);
        //发送命令到设备
        socket_write($socket, $in, strlen($in));
        //接收设备命令返回数据
        $buffer = socket_read($socket, 1024, PHP_NORMAL_READ);
        //关闭连接
        socket_close($socket);
        //输出返回值
        return $buffer;
    }

    public function Send_socket_connect_udp() {
        $service_port = '5060';
        $address= '112.124.108.16';
        $in = '{"get":"rid"}';
        //采用php socket技术使用UDP协议连接设备
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        @socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 2, "usec" => 0));
        //发送命令
        @socket_sendto($socket, $in, strlen($in), 0, $address, $service_port);
        @socket_recvfrom($socket, $buffer, 1024, MSG_WAITALL, $address, $service_port);
        //关闭连接
        if (empty($buffer)) {
            echo "<script>alert('网络异常，请检查网络连接！！');</script>";
        }
    }

    public function Send_socket_xdcoder_udp() {
        $service_port = '5060';
        $address= '112.124.108.16';
        $in = '{"get":"rid"}';
        //采用php socket技术使用UDP协议连接设备
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        @socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 2, "usec" => 0));
        //发送命令
        @socket_sendto($socket, $in, strlen($in), 0, $address, $service_port);
        @socket_recvfrom($socket, $buffer, 1024, MSG_WAITALL, $address, $service_port);
        //关闭连接
        socket_close($socket);
        if (!empty($buffer)) {
            return $buffer;
        } else {
            echo "<script>alert('网络异常，请检查网络连接！！');</script>";
        }
    }

    public function test8(){
        function curl_upload($furl,$url){
            //  初始化
            $ch = curl_init();
            // 要上传的本地文件地址"@F:/xampp/php/php.ini"上传时候，上传路径前面要有@符号
            $post_data = array (
                "upload" => $furl
            );
            //print_r($post_data);
            //CURLOPT_URL 是指提交到哪里？相当于表单里的“action”指定的路径
            //$url = "http://localhost/DemoIndex/curl_pos/";
            //  设置变量
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//执行结果是否被返回，0是返回，1是不返回
            curl_setopt($ch, CURLOPT_HEADER, 0);//参数设置，是否显示头部信息，1为显示，0为不显示
            //伪造网页来源地址,伪造来自百度的表单提交
            //curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");
            //表单数据，是正规的表单设置值为非0
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 100);//设置curl执行超时时间最大是多少
            //使用数组提供post数据时，CURL组件大概是为了兼容@filename这种上传文件的写法，
            //默认把content_type设为了multipart/form-data。虽然对于大多数web服务器并
            //没有影响，但是还是有少部分服务器不兼容。本文得出的结论是，在没有需要上传文件的
            //情况下，尽量对post提交的数据进行http_build_query，然后发送出去，能实现更好的兼容性，更小的请求数据包。
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            //   执行并获取结果
            curl_exec($ch);
            if(curl_exec($ch) === FALSE)
            {
                return false;
            }
            //  释放cURL句柄
            curl_close($ch);
            return true;
        }
    }

    public function test9(){
        $kuaidi = '1233455';
        $url = 'http://m.kuaidi100.com/index_all.html?type=&postid='.$kuaidi.'&callbackurl=';
        $content = file_get_contents($url);
        json_encode($content);
        die;
    }

    public function test10(){
        $data = '{"content":["{\"goods_id\":\"1\",\"content\":\"\uff0c\u3002\u3002\u3002\u3002\u3002\u3002\",\"goods_mark\":5,\"img\":\"\"}","{\"goods_id\":\"1\",\"content\":\"\uff0c\u3002\u3002\u3002\u3002\u3002\u3002\",\"goods_mark\":5,\"img\":\"\"}"],"order_no":"20170604133632689595","token":"59635bea25bd7","uid":"429","user_id":"429"}';
        $data = json_decode($data,true);
        foreach($data['content'] as $v){
            $content[] = json_decode($v,true);
        }
        var_dump($content);
    }

}