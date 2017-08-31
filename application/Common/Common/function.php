<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/3 0003
 * Time: 下午 2:44
 */
function huanxin_get_client_id(){
    return M('System')->where(array('id'=>1))->getField('hx_client_id');//
}
function huanxin_get_client_secret(){
    return M('System')->where(array('id'=>1))->getField('hx_secret');//
}
function huanxin_get_org_name(){
    return M('System')->where(array('id'=>1))->getField('hx_appkey_1');//
}
function huanxin_get_app_name(){
    return M('System')->where(array('id'=>1))->getField('hx_appkey_2');//
}
function huanxin_zhuce($username,$password){
    $param = array (
        "username" => $username,
        "password" => $password
    );
    $url = "https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/users";
    $res = huanxin_curl_request($url, json_encode($param));
    $tokenResult =  json_decode($res, true);
    $tokenResult["password"]=$param["password"];
    $huanxin_uuid = $tokenResult["entities"][0]["uuid"];
    $huanxin_username = $tokenResult["entities"][0]["username"];
    $huanxin_password=$param["password"];
    if(!($huanxin_uuid&&$huanxin_username)){
        return false;
    }else{
        return true;
    }
}

function huanxin_curl_request($url, $body, $header = array(), $method = "POST") {
    array_push ( $header, 'Accept:application/json' );
    array_push ( $header, 'Content-Type:application/json' );
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 60 );
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    // curl_setopt($ch, $method, 1);

    switch (strtoupper($method)) {
        case "GET" :
            curl_setopt ( $ch, CURLOPT_HTTPGET, true );
            break;
        case "POST" :
            curl_setopt ( $ch, CURLOPT_POST, true );
            break;
        case "PUT" :
            curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "PUT" );
            break;
        case "DELETE" :
            curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
            break;
    }

    curl_setopt ( $ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0' );
    curl_setopt ( $ch, CURLOPT_ENCODING, 'gzip' );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 1 );
    if (isset ( $body {3} ) > 0) {
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $body );
    }
    if (count ( $header ) > 0) {
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
    }
    $ret = curl_exec ( $ch );
    $err = curl_error ( $ch );
    curl_close ( $ch );
    // clear_object($ch);
    // clear_object($body);
    // clear_object($header);
    if ($err) {
        return $err;
    }
    return $ret;
}
function huanxin_get_access_token($force = false) {
    if(!$force){
        $token = S("huanxin_access_token");
        if($token){
            return $token["access_token"];
        }
    }

    $param = array (
        "grant_type" => "client_credentials",
        "client_id" => huanxin_get_client_id(),
        "client_secret" => huanxin_get_client_secret()
    );
    $url = "https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/token";
    $res = $this->huanxin_curl_request ( $url, json_encode($param) );
    $tokenResult =  json_decode($res, true);
    S("huanxin_access_token",$tokenResult,$tokenResult["expires_in"]*0.9);
    return $tokenResult["access_token"] ;
}

function huanxin_xiugainicheng($nicheng,$huanxin_username)
{
    $access_token = huanxin_get_access_token();
    $param = array(
        "nickname" => $nicheng
    );

    $url = "https://a1.easemob.com/" . huanxin_get_org_name() . "/" . huanxin_get_app_name() . "/users/$huanxin_username";
    $header = "Authorization:Bearer " . $access_token;
    $r = huanxin_curl_request($url, json_encode($param), array($header), "PUT");
    return $r;
}

/**
 *获取token
 */
function getTokens()
{

    $options=array(
        "grant_type"=>"client_credentials",
        "client_id"=>huanxin_get_client_id(),
        "client_secret"=>huanxin_get_client_secret()
    );
    //json_encode()函数，可将PHP数组或对象转成json字符串，使用json_decode()函数，可以将json字符串转换为PHP数组或对象
    $body=json_encode($options);
    //使用 $GLOBALS 替代 global
    $url="https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/token";
    //$url=$base_url.'token';
    $tokenResult = postCurl($url,$body);
    //var_dump($tokenResult['expires_in']);
    //return $tokenResult;
    return "Authorization:Bearer ". $tokenResult["access_token"];


    //return "Authorization:Bearer YWMtG_u2OH1tEeWK7IWc3Nx2ygAAAVHjWllhTpavYYyhaI_WzIcHIQ9uitTvsmw";
}


function postCurl($url,$body,$header,$type="POST"){
    //1.创建一个curl资源
    $ch = curl_init();
    //2.设置URL和相应的选项
    curl_setopt($ch,CURLOPT_URL,$url);//设置url
    //1)设置请求头
    //array_push($header, 'Accept:application/json');
    //array_push($header,'Content-Type:application/json');
    //array_push($header, 'http:multipart/form-data');
    //设置为false,只会获得响应的正文(true的话会连响应头一并获取到)
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt ( $ch, CURLOPT_TIMEOUT,5); // 设置超时限制防止死循环
    //设置发起连接前的等待时间，如果设置为0，则无限等待。
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
    //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //2)设备请求体
    if (count($body)>0) {
        //$b=json_encode($body,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);//全部数据使用HTTP协议中的"POST"操作来发送。
    }
    //设置请求头
    if(count($header)>0){
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    }
    //上传文件相关设置
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);// 从证书中检查SSL加密算

    //3)设置提交方式
    switch($type){
        case "GET":
            curl_setopt($ch,CURLOPT_HTTPGET,true);
            break;
        case "POST":
            curl_setopt($ch,CURLOPT_POST,true);
            break;
        case "PUT"://使用一个自定义的请求信息来代替"GET"或"HEAD"作为HTTP请									                     求。这对于执行"DELETE" 或者其他更隐蔽的HTT
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PUT");
            break;
        case "DELETE":
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
            break;
    }


    //4)在HTTP请求中包含一个"User-Agent: "头的字符串。-----必设
    curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
    //5)


    //3.抓取URL并把它传递给浏览器
    $res=curl_exec($ch);
    $result=json_decode($res,true);
    //4.关闭curl资源，并且释放系统资源
    curl_close($ch);
    if(empty($result))
        return $res;
    else
        return $result;
}


/*
 创建聊天室
 */
function createChatRoom($options){
    $url="https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/chatrooms";
    $header=array(getTokens());
    $body=json_encode($options);
    $result=postCurl($url,$body,$header);
    if($result['error']){
        createChatRoom($options);
    }
    return $result;
}

/*
 获取单个用户
 */
function getUsers($username){
    $url="https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/users/$username";
    $header = array(getTokens());
    $result = postCurl($url,'',$header,"GET");
    return $result;
}

function getIP()
{
    global $ip;
    if(getenv("HTTP_CLIENT_IP")){
        $ip = getenv("HTTP_CLIENT_IP");
    }else if(getenv("HTTP_X_FORWARDED_FOR")){
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    }elseif(getenv("REMOTE_ADDR")){
        $ip = getenv("REMOTE_ADDR");
    }else{
        $ip = "Unknow";
    }
    return $ip;
}

/**
+----------------------------------------------------------
 * 原样输出print_r的内容
+----------------------------------------------------------
 * @param string    $content   待print_r的内容
+----------------------------------------------------------
 */
function pre($content) {
    echo "<pre>";
    print_r($content);
    echo "</pre>";
}

/**
 * 验证验证码
 * @param $code
 * @param string $id
 * @return bool
 */
function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}



/**
+----------------------------------------------------------
 * 加密密码
+----------------------------------------------------------
 * @param string    $data   待加密字符串
+----------------------------------------------------------
 * @return string 返回加密后的字符串
 */
function myencrypt($data) {
    return md5(C("AUTH_CODE") . md5($data));
}

/**
+----------------------------------------------------------
 * 将一个字符串转换成数组，支持中文
+----------------------------------------------------------
 * @param string    $string   待转换成数组的字符串
+----------------------------------------------------------
 * @return string   转换后的数组
+----------------------------------------------------------
 */
function strToArray($string) {
    $strlen = mb_strlen($string);
    while ($strlen) {
        $array[] = mb_substr($string, 0, 1, "utf8");
        $string = mb_substr($string, 1, $strlen, "utf8");
        $strlen = mb_strlen($string);
    }
    return $array;
}

/**
+----------------------------------------------------------
 * 生成随机字符串
+----------------------------------------------------------
 * @param int       $length  要生成的随机字符串长度
 * @param string    $type    随机码类型：0，数字+大写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；-1，数字+大小写字母+特殊字符
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function randCode($length = 5, $type = 0) {
    $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
    $code='';
    if ($type == 0) {
        array_pop($arr);
        $string = implode("", $arr);
    } else if ($type == "-1") {
        $string = implode("", $arr);
    } else {
        $string = $arr[$type];
    }
    $count = strlen($string) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str[$i] = $string[rand(0, $count)];
        $code .= $str[$i];
    }
    return $code;
}


/**
+----------------------------------------------------------
 * 将一个字符串部分字符用*替代隐藏
+----------------------------------------------------------
 * @param string    $string   待转换的字符串
 * @param int       $bengin   起始位置，从0开始计数，当$type=4时，表示左侧保留长度
 * @param int       $len      需要转换成*的字符个数，当$type=4时，表示右侧保留长度
 * @param int       $type     转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
 * @param string    $glue     分割符
+----------------------------------------------------------
 * @return string   处理后的字符串
+----------------------------------------------------------
 */
function hideStr($string, $bengin = 0, $len = 4, $type = 0, $glue = "@") {
    if (empty($string))
        return false;
    $array = array();
    if ($type == 0 || $type == 1 || $type == 4) {
        $strlen = $length = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, "utf8");
            $string = mb_substr($string, 1, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
    }
    switch ($type) {
        case 1:
            $array = array_reverse($array);
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i]))
                    $array[$i] = "*";
            }
            $string = implode("", array_reverse($array));
            break;
        case 2:
            $array = explode($glue, $string);
            $array[0] = hideStr($array[0], $bengin, $len, 1);
            $string = implode($glue, $array);
            break;
        case 3:
            $array = explode($glue, $string);
            $array[1] = hideStr($array[1], $bengin, $len, 0);
            $string = implode($glue, $array);
            break;
        case 4:
            $left = $bengin;
            $right = $len;
            $tem = array();
            for ($i = 0; $i < ($length - $right); $i++) {
                if (isset($array[$i]))
                    $tem[] = $i >= $left ? "*" : $array[$i];
            }
            $array = array_chunk(array_reverse($array), $right);
            $array = array_reverse($array[0]);
            for ($i = 0; $i < $right; $i++) {
                $tem[] = $array[$i];
            }
            $string = implode("", $tem);
            break;
        default:
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i]))
                    $array[$i] = "*";
            }
            $string = implode("", $array);
            break;
    }
    return $string;
}

/**
+----------------------------------------------------------
 * 功能：字符串截取指定长度
 * leo.li hengqin2008@qq.com
+----------------------------------------------------------
 * @param string    $string      待截取的字符串
 * @param int       $len         截取的长度
 * @param int       $start       从第几个字符开始截取
 * @param boolean   $suffix      是否在截取后的字符串后跟上省略号
+----------------------------------------------------------
 * @return string               返回截取后的字符串
+----------------------------------------------------------
 */
function cutStr($str, $len = 100, $start = 0, $suffix = 1) {
    $str = strip_tags(trim(strip_tags($str)));
    $str = str_replace(array("\n", "\t"), "", $str);
    $strlen = mb_strlen($str);
    while ($strlen) {
        $array[] = mb_substr($str, 0, 1, "utf8");
        $str = mb_substr($str, 1, $strlen, "utf8");
        $strlen = mb_strlen($str);
    }
    $end = $len + $start;
    $str = '';
    for ($i = $start; $i < $end; $i++) {
        $str.=$array[$i];
    }
    return count($array) > $len ? ($suffix == 1 ? $str . "&hellip;" : $str) : $str;
}

/**
+----------------------------------------------------------
 * 功能：检测一个目录是否存在，不存在则创建它
+----------------------------------------------------------
 * @param string    $path      待检测的目录
+----------------------------------------------------------
 * @return boolean
+----------------------------------------------------------
 */
function makeDir($path) {
    return is_dir($path) or (makeDir(dirname($path)) and @mkdir($path, 0777));
}

/**
+----------------------------------------------------------
 * 功能：检测一个字符串是否是邮件地址格式
+----------------------------------------------------------
 * @param string $value    待检测字符串
+----------------------------------------------------------
 * @return boolean
+----------------------------------------------------------
 */
function is_email($value) {
    return preg_match("/^[0-9a-zA-Z]+(?:[\_\.\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $value);
}

/**
+----------------------------------------------------------
 * 功能：系统邮件发送函数
+----------------------------------------------------------
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body    邮件内容
 * @param string $attachment 附件列表namespace Org\Util\PHPMailer;
+----------------------------------------------------------
 * @return boolean
+----------------------------------------------------------
 */
function send_mail($to, $name, $subject = '', $body = '', $attachment = null, $config = '') {
    $config = is_array($config) ? $config : C('SYSTEM_EMAIL');
    //import('PHPMailer.phpmailer', VENDOR_PATH);         //从PHPMailer目录导class.phpmailer.php类文件
    $mail = new \Org\Util\PHPMailer\PHPMailer();                           //PHPMailer对象
    $mail->CharSet = 'UTF-8';                         //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();                                   // 设定使用SMTP服务
//    $mail->IsHTML(true);
    $mail->SMTPDebug = 0;                             // 关闭SMTP调试功能 1 = errors and messages2 = messages only
    $mail->SMTPAuth = true;                           // 启用 SMTP 验证功能
    if ($config['smtp_port'] == 465)
        $mail->SMTPSecure = 'ssl';                    // 使用安全协议
    $mail->Host = $config['smtp_host'];                // SMTP 服务器
    $mail->Port = $config['smtp_port'];                // SMTP服务器的端口号
    $mail->Username = $config['smtp_user'];           // SMTP服务器用户名
    $mail->Password = $config['smtp_pass'];           // SMTP服务器密码
    $mail->SetFrom($config['from_email'], $config['from_name']);
    $replyEmail = $config['reply_email'] ? $config['reply_email'] : $config['reply_email'];
    $replyName = $config['reply_name'] ? $config['reply_name'] : $config['reply_name'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            if (is_array($file)) {
                is_file($file['path']) && $mail->AddAttachment($file['path'], $file['name']);
            } else {
                is_file($file) && $mail->AddAttachment($file);
            }
        }
    } else {
        is_file($attachment) && $mail->AddAttachment($attachment);
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}

/**
+----------------------------------------------------------
 * 功能：剔除危险的字符信息
+----------------------------------------------------------
 * @param string $val
+----------------------------------------------------------
 * @return string 返回处理后的字符串
+----------------------------------------------------------
 */
function remove_xss($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}

/**
+----------------------------------------------------------
 * 功能：计算文件大小
+----------------------------------------------------------
 * @param int $bytes
+----------------------------------------------------------
 * @return string 转换后的字符串
+----------------------------------------------------------
 */
function byteFormat($bytes) {
    $sizetext = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $sizetext[$i];
}

function checkCharset($string, $charset = "UTF-8") {
    if ($string == '')
        return;
    $check = preg_match('%^(?:
                                [\x09\x0A\x0D\x20-\x7E] # ASCII
                                | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
                                | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
                                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
                                | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
                                | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
                                | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
                                | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
                                )*$%xs', $string);

    return $charset == "UTF-8" ? ($check == 1 ? $string : iconv('gb2312', 'utf-8', $string)) : ($check == 0 ? $string : iconv('utf-8', 'gb2312', $string));
}



//模拟post提交
function doCurlPostRequest($url,$data)
{
    if(empty($url)||empty($data))
    {
        return false;
    }
    $ch=curl_init();//初始化
    curl_setopt($ch, CURLOPT_HEADER,false);
    curl_setopt($ch,CURLOPT_URL,$url);//设置选项
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    $output =curl_exec($ch);//执行
    curl_close($ch);//释放句柄
    return $output;

}
/**
+----------------------------------------------------------
 * 功能：检测一个字符串是否是手机地址格式
+----------------------------------------------------------
 * @param string $value    待检测字符串
+----------------------------------------------------------
 * @return boolean
+----------------------------------------------------------
 */
function is_mobile($value) {
    return preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}|^17[0-9]\d{8}$#', $value);
}

//成功处理函数
function success($arr){
    $d = [
        'status' 	=> 'ok',
        'data'		=> $arr
    ];
    $d = json_encode($d,JSON_UNESCAPED_UNICODE);
    $d = str_replace('null','""',$d);
    echo $d;
    exit;
}

function error($arr){
    echo  json_encode([
        'status'=> 'error',
        'data'=> $arr,
        'error'=>$arr
    ]);
    exit();
}

function pending($arr){
    echo  json_encode([
        'status'=> 'pending',
        'data'=> $arr
    ]);
    exit();
}

function checklogin(){
    $check["user_id"] = I("uid");
    $check["token"] = I("token");
    $user = M("User")->where($check)->find();
    if (!$user) {
        pending("请重新登录!");
    }
    if($user['is_del']==2){
        pending("账号被禁止!");
    }else{
        $day = date("Y-m-d");
        $check =M('IntoApp')->where(['user_id'=>$user['user_id'],'date'=>$day])->find();
        if(!$check){
            M('IntoApp')->add(['intime'=>time(),'user_id'=>$user['user_id'],'date'=>$day]);
        }else{
            M('IntoApp')->where(['into_app'=>$check['into_app']])->save(['intime'=>time()]);
        }
        return $user;
    }

}


/**
 * @param $data
 * @param string $signType
 * @return string
 * @获取等级名称、图标
 */
function get_gradeinfo($grade){
    $approve_rule = M('Approve_rule')->where(['grade_start'=>['elt',$grade],'grade_end'=>['egt',$grade]])->find();
    if ($approve_rule['grade_img']){
        $img = C('IMG_PREFIX').$approve_rule['grade_img'];
    }else{
        $img = "";
    }
    $name = $approve_rule['name'];
    $img ? $img = $img : $img = "";
    $name ? $name = $name : $name = "";
    $result = ['img'=>$img,'name'=>$name];
    return $result;
}

/**
 * @判断提交的字符串中是否有敏感词
 * @有敏感词就替换
 */
function is_sensitive_word($content){
    $str = M('System')->getFieldById(1,'sensitive_word');
    $rs = explode(',',$str);
    foreach ($rs as $k=>$v) {
        //$content = str_replace('**',$v,$content);
        if(strpos($content,$v) !== false){
            success('内容含有敏感文字'.$v.'，请更换');
        }
    }
    return $content;
}


/**
 * @param $money
 * @return string
 * @判断万以上数据
 */
function FormatMoney($money){
    if($money >= 10000){
        return sprintf("%.1f", $money/10000).'万';
    }else{
        return $money;
    }
}

function checklogin2(){
    $data["company_id"] = I("company_id");
    $data["token"] = I("token");
    $rel = M("Company")->where($data)->find();

    if (!$rel) pending("token failed");

    return $rel;
}

function logistics($type,$number){
    $apikey = 'af0132b43796f9cf';
    $url = "http://api.jisuapi.com/express/query?appkey=$apikey&type=$type&number=".$number;
    $xml = json_decode(file_get_contents($url),true);
    return $xml;
}


/**
 * @直播推流地址、播放地址
 */
function push_address(){
    $system = M('System')->where(['id'=>1])->find();
    import('Vendor.Qiniu.Pili');
    $ak = $system['ak'];
    $sk = $system['sk'];
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
       // echo "Error:", $e, "\n";
    }
    //RTMP 推流地址
    $url = \Qiniu\Pili\RTMPPublishURL("bszb.tstmobile.com", $hubName, $streamKey, 3600, $ak, $sk);
    //RTMP 直播放址
    $url2 = \Qiniu\Pili\RTMPPlayURL("bsliveplay.tstmobile.com", $hubName, $streamKey);
    $url3 = \Qiniu\Pili\HLSPlayURL("bsliveplay.tstmobile.com", $hubName, $streamKey);
    $url4 = \Qiniu\Pili\HDLPlayURL("bsliveplay.tstmobile.com", $hubName, $streamKey);
    $result = array('url'=>$url,'url2'=>$url2,'streamKey'=>$streamKey,'m3u8'=>$url3,'url4'=>$url4);
    return $result;
}

/**
 * @七牛创建房间
 */
function creatroom($id,$room_name){
    $system = M('System')->where(['id'=>1])->find();
    import('Vendor.Qiniu.Pili');
    $ak = $system['ak'];
    $sk = $system['sk'];
    //创建hub
    $mac = new \Qiniu\Pili\Mac($ak, $sk);
    $client = New \Qiniu\Pili\RoomClient($mac);
    try {
        $client->createRoom($id,$room_name);

        $room = $client->getRoom($room_name);
        //$resp = $client->deleteRoom("testroom");
        // dump($resp);

        //鉴权的有效时间: 24个小时.
        $token = $client->roomToken($room_name, $id, 'admin', (time()+3600*24));
    } catch (\Exception $e) {
//        echo "Error:", $e, "\n";
//        die;
    }
    $result = ['room_id'=>$room['owner_id'],'room_name'=>$room['room_name'],'token'=>$token];
    return $result;

}


//百度地图获取距离
function getDistance($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 6367000; //approximate radius of earth in meters
    /*
    Convert these degrees to radians
    to work with the formula
    */

    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;

    /*
    Using the
    Haversine formula

    http://en.wikipedia.org/wiki/Haversine_formula

    calculate the distance
    */

    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}
/**
 * @二维数组根据其中一个字段排序(正序)
 * @$array:数组    $orderby:需要重新排序的字段
 */
function wpjam_array_multisort($array, $orderby, $order = SORT_ASC, $sort_flags = SORT_NUMERIC){
    $refer = array();

    foreach ($array as $key => $value) {
        $refer[] = $value[$orderby];
    }

    array_multisort($refer, $order,$sort_flags, $array);

    return $array;
}
/**
 * @二维数组根据其中一个字段排序(倒序序)
 * @$array:数组    $orderby:需要重新排序的字段
 */
function wpjam_array_desc($array, $orderby, $order = SORT_DESC,$sort_flags = SORT_NUMERIC){
    $refer = array();

    foreach ($array as $key => $value) {
        $refer[] = $value[$orderby];
    }

    array_multisort($refer, $order,$sort_flags, $array);

    return $array;
}

/**
 * @根据时间，返回计算结果
 */
function get_times($intime){
    $time = time()-$intime;
    if($time<=60){
        $day = '刚刚';
    }elseif($time>60 && $time<3600){
        $day = floor($time/60).'分钟前';
    }elseif($time>=3600 && $time<86400){
        $day = floor($time/3600).'小时前';
    }elseif($time>=86400 && $time<2592000){
        $day = floor($time/86400).'天前';
    }elseif($time>=2592000 && $time<31104000){
        $day = floor($time/2592000).'个月前';
    }elseif(date('Y',time())-date('Y',$intime)>=1){
        $day = date('Y',time())-date('Y',$intime).'年前';
    }
    return $day;
}
/**
 * 生成验证码
 * @param int $length
 * @param bool $numeric 是否为数字字符串
 * @return string
 */
function random($length=6, $numeric=true)
{
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

/**
 * 把xml数据转成数组
 * @param $xml
 * @return mixed
 */
function xml_to_array($xml)
{
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if (preg_match_all($reg, $xml, $matches)) {
        $count = count($matches[0]);
        for ($i = 0; $i < $count; $i++) {
            $subxml = $matches[2][$i];
            $key = $matches[1][$i];
            if (preg_match($reg, $subxml)) {
                $arr[$key] = xml_to_array($subxml);
            } else {
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}

/**
 * @param $phone
 * @param $content 短信内容不超过70个字符
 * @return bool
 */
function sendSMS($phone, $content){
    $url ='http://192.168.43.10/smsservice/contents';
    $data = array('phoneNB'=>$phone, 'contents'=>$content);  //定义参数
    $data = @http_build_query($data);  //把参数转换成URL数据
    $aContext = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $data
        )
    );

    $cxContext  = stream_context_create($aContext);

    $rel = json_decode(@file_get_contents($url, false, $cxContext), true);

    if($rel && $rel['errorCode'] === 0) return true;

    return false;
}

/**
 * 发送验证码
 * @param $phone
 * @return bool
 */
function sendSMS_code($phone){
    $url = 'http://192.168.43.10/smsservice/verificationCodes?phoneNB=' . $phone;
    $rel = json_decode(@file_get_contents($url), true);

    if($rel){
        switch($rel['errorCode']){
            case 0:
                return true;
                break;
            case 102:
                error('当天发送次数超过限制!');
                break;
            case 103:
                error('提交频率过快!');
                break;
            case 102:
                error('当天发送次数超过限制!');
                break;
            default:
                error('发送失败!');
        }
    }

    return false;
}

/**
 * 校验验证码
 * @param $phone
 * @param $code
 * @return bool
 */
function check_code($phone, $code){
    $url ='http://192.168.43.10/smsservice/verificationCodes';
    $data = array('phoneNB'=>$phone, 'verificationCode'=>$code);  //定义参数
    $data = @http_build_query($data);  //把参数转换成URL数据
    $aContext = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $data
        )
    );

    $cxContext  = stream_context_create($aContext);

    $rel = json_decode(@file_get_contents($url, false, $cxContext), true);

    if($rel){
        switch($rel['errorCode']){
            case 0:
                return true;
                break;
            case 112:
                error('该验证码已过期!');
                break;
            case 113:
                error('验证码错误!');
                break;
            case 114:
                return false;
                break;
        }
    }

    return false;
}


/**
 * 获取二维数组中的某个字段形成一维数组
 * @param $arr
 * @param $key
 * @return array
 */
function get_linear_array($arr, $key){
    $data = array();
    foreach($arr as $v){
        $data[] = $v[$key];
    }

    return $data;
}

function check_planar_array($arr, $k1, $v1, $k2, $v2){
    foreach($arr as $v){
        if($v[$k1] == $v1 && $v[$k2] == $v2){
            return true;
            break;
        }
    }

    return false;
}

/**
 * 浮点数减一法
 * @param float $value 数字
 * @param int $places 保留小数
 * @param string $separator 分隔符
 * @return string|float
 */
function floor_down($value, $places=4, $separator = "."){
    $arr =  explode($separator, $value?:0);

    if(count($arr) != 2) {
        return $value;
    }

    $len = strlen($arr[1]);

    if ($len <= $places) {
        return $value;
    }

    if($places <= 0) return $arr[0];

    $str = $arr[0] . $separator;
    for ($i=0; $i<$places; $i++) {
        $str .= $arr[1][$i];
    }
    return $str;
}

function _cut($begin,$end,$str){
    $b = strpos($str,$begin) + strlen($begin);
    $e = strpos($str,$end) - $b;

    return substr($str,$b,$e);
}

/**
 * 等比缩放
 *
 * $srcImage   源图片路径
 * $toFile     目标图片路径
 * $max        最大值
 * $min        最小值
 * $get_max    是否获取宽高中的最大值
 * $del_oldimg 是否删除原图片
 * $size       不删除原图片时的新图片名称(原图片名称+'_S')
 * @return unknown
 */
function img_resize($old_src, $new_src, $max, $min, $get_max=true, $del_oldimg=true, $size='S'){
    $srcImage =rtrim(BASE_PATH, '/').$old_src;

    list($width, $height, $type, $attr) = getimagesize($srcImage);

    //根据最大值，算出另一个边的长度，得到缩放后的图片宽度和高度
    if($get_max){
        $old_max = $width > $height ? $width : $height;
        if ($old_max <= $max && $old_max >= $min) return $old_src;

        if($width > $height){
            $w = $width > $max ? $max : ($width < $min ? $min : $width);
            $h = $height*($w/$width);
        }else{
            $h = $height > $max ? $max : ($height < $min ? $min : $height);
            $w = $width*($h/$height);
        }
    }else{
        if ($width <= $max && $width >= $min) return $old_src;

        $w = $width > $max ? $max : ($width < $min ? $min : $width);
        $h = $height*($w/$width);
    }

    switch ($type) {
        case 1: $img = imagecreatefromgif($srcImage); break;
        case 2: $img = imagecreatefromjpeg($srcImage); break;
        case 3: $img = imagecreatefrompng($srcImage); break;
        default: return $old_src;
    }

    $newImg = imagecreatetruecolor($w, $h);

    imagecopyresampled($newImg, $img, 0, 0, 0, 0, $w, $h, $width, $height);

    $new_src = preg_replace("/(.gif|.jpg|.jpeg|.png)/i","",$new_src);
    if(!$del_oldimg) $new_src .= '_'.$size;
    $toFile = rtrim(BASE_PATH, '/').$new_src;

    if($del_oldimg) @unlink($srcImage);//删除原图片

    //生成新图片
    switch($type) {
        case 1:
            if(imagegif($newImg, "{$toFile}.gif")) return $new_src.'.gif';
            break;
        case 2:
            $src = "{$toFile}.jpg";
            if(imagejpeg($newImg, $src)) return $new_src.'.jpg';
            break;
        case 3:
            $src = "{$toFile}.png";
            if(imagepng($newImg, $src)) return $new_src.'.png';
            break;
        default:
            return $old_src;
    }

    //销毁图片资源
    imagedestroy($newImg);
    imagedestroy($img);
    return false;
}

function utf8_substr($str, $start = 0, $length)
{
    if (function_exists('utf8_substr')) {
        return mb_substr($str, $start, $length, 'UTF-8');
    }
    preg_match_all("/./u", $str, $arr);
    return implode("", array_slice($arr[0], $start, $length));
}

//对象转数组,使用get_object_vars返回对象属性组成的数组
function objectToArray($obj){
    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if(is_array($arr)){
        return array_map(__FUNCTION__, $arr);
    }else{
        return $arr;
    }
}

//数组转对象
function arrayToObject($arr){
    if(is_array($arr)){
        return (object) array_map(__FUNCTION__, $arr);
    }else{
        return $arr;
    }
}
/**
 * @消息推送(ios,生产环境)
 */
function push($uid,$title,$content,$img,$nickname,$hx_username,$alias,$is_follow,$apply_show_id,$url){
    $jg = M('System')->where(array('id'=>1))->find();
    $app_key = $jg['jg_appkey'];
    $master_secret = $jg['jg_secret'];
    // 初始化
    import('Vendor.JPush.jpush');
    $client = new \JPush($app_key, $master_secret);
    //return $client->push()->setPlatform('android')->addAlias($alias)->addAndroidNotification($content, $title, 1, array("user_type"=>$type,"message_code"=>$staus,"is_global"=>$global))->setOptions(100000, 86400, null, false)->send();
    try {
        $result = $client->push()->setPlatform(['ios','android'])->addAlias($alias)->
        addAndroidNotification($content, $title, 1, array("user_id"=>$uid,"img"=>$img,"nickname"=>$nickname,"hx_username"=>$hx_username,"alias"=>$alias,"is_follow"=>$is_follow,"apply_show_id"=>$apply_show_id,"url"=>$url))->
        addIosNotification(['alert'=>$content,'sound'=>'','available'=>true,'extras'=>['user_id'=>$uid,'img'=>$img,'nickname'=>$nickname,'hx_username'=>$hx_username,'alias'=>$alias,'is_follow'=>$is_follow,'apply_show_id'=>$apply_show_id,'url'=>$url]])->
        setOptions(100000, 86400, null, true)->send();
        return 1;
    } catch (APIRequestException $e) {
        return 2;
    } catch (APIConnectionException $e) {
        return 3;
    }
}
/**
 * @消息推送(ios,开发环境)
 */
function push3($uid,$title,$content,$img,$nickname,$hx_username,$alias,$is_follow,$apply_show_id,$url){
    $jg = M('System')->where(array('id'=>1))->find();
    $app_key = $jg['jg_appkey'];
    $master_secret = $jg['jg_secret'];
    // 初始化
    import('Vendor.JPush.jpush');
    $client = new \JPush($app_key, $master_secret);
    //return $client->push()->setPlatform('android')->addAlias($alias)->addAndroidNotification($content, $title, 1, array("user_type"=>$type,"message_code"=>$staus,"is_global"=>$global))->setOptions(100000, 86400, null, false)->send();
    try {
        $result = $client->push()->setPlatform(['ios','android'])->addAlias($alias)->
        addAndroidNotification($content, $title, 1, array("user_id"=>$uid,"img"=>$img,"nickname"=>$nickname,"hx_username"=>$hx_username,"alias"=>$alias,"is_follow"=>$is_follow,"apply_show_id"=>$apply_show_id,"url"=>$url))->
        addIosNotification(['alert'=>$content,'sound'=>'','available'=>true,'extras'=>['user_id'=>$uid,'img'=>$img,'nickname'=>$nickname,'hx_username'=>$hx_username,'alias'=>$alias,'is_follow'=>$is_follow,'apply_show_id'=>$apply_show_id,'url'=>$url]])->
        setOptions(100000, 86400, null, false)->send();
        return 1;
    } catch (APIRequestException $e) {
        return 2;
    } catch (APIConnectionException $e) {
        return 3;
    }
}
/**
 * @消息推送2
 */
function push2($alias,$title,$content,$type,$staus,$global,$log,$lag){
    $jg = M('System')->where(array('id'=>1))->find();
    $app_key = $jg['jg_appkey'];
    $master_secret = $jg['jg_secret'];
    // 初始化
    import('Vendor.JPush.jpush');
    $client = new \JPush($app_key, $master_secret);
    //return $client->push()->setPlatform('android')->addAlias($alias)->addAndroidNotification($content, $title, 1, array("user_type"=>$type,"message_code"=>$staus,"is_global"=>$global))->setOptions(100000, 86400, null, false)->send();
    try {
        $result = $client->push()->setPlatform('android')->addAlias($alias)->addAndroidNotification($content, $title, 1, array("user_type"=>$type,"message_code"=>$staus,"is_global"=>$global,"log"=>$log,"lag"=>$lag))->setOptions(100000, 86400, null, false)->send();
        return 1;
    } catch (APIRequestException $e) {
        return 2;
    } catch (APIConnectionException $e) {
        return 3;
    }
}
//获取表的名称
function list_tables($database)
{
    $rs = mysql_list_tables($database);
    $tables = array();
    while ($row = mysql_fetch_row($rs)) {
        $tables[] = $row[0];
    }
    mysql_free_result($rs);
    return $tables;
}
//导出数据库
function dump_table($table, $fp = null)
{
    $need_close = false;
    if (is_null($fp)) {
        $fp = fopen($table . '.sql', 'w');
        $need_close = true;
    }
    $a  = mysql_query("show create table `{$table}`");
    $row = mysql_fetch_assoc($a);fwrite($fp,$row['Create Table'].';');//导出表结构
    $rs = mysql_query("SELECT * FROM `{$table}`");
    while ($row = mysql_fetch_row($rs)) {
        fwrite($fp, get_insert_sql($table, $row));
    }
    mysql_free_result($rs);
    if ($need_close) {
        fclose($fp);
    }
}
//导出表数据
function get_insert_sql($table, $row)
{
    $sql = "INSERT INTO `{$table}` VALUES (";
    $values = array();
    foreach ($row as $value) {
        $values[] = "'" . mysql_real_escape_string($value) . "'";
    }
    $sql .= implode(', ', $values) . ");";
    return $sql;
}
//生成二维码
function qrcode($url,$filepath, $level=3,$size=4){
    if(!$url) return false;
    //加载二维码类
    Vendor('phpqrcode.phpqrcode');
    //容错级别
    $errorCorrectionLevel =intval($level) ;
    $matrixPointSize = intval($size);//生成图片大小
    //生成二维码图片
    $object = new QRcode();

    $result = $object->png($url, $filepath, $errorCorrectionLevel, $matrixPointSize, 2, true);
    return $result;
}

//图像变模糊
function gaussian_blur($srcImg,$savepath=null,$savename=null,$blurFactor=3){
    $gdImageResource = image_create_from_ext($srcImg);
    $srcImgObj = blur($gdImageResource,$blurFactor);
    $temp = pathinfo($srcImg);
    $name = $temp['basename'];
    $path = $temp['dirname'];
    $exte = $temp['extension'];
    $savename = $savename ? $savename : $name;
    $savepath = $savepath ? $savepath : $path;
    $savefile = $savepath .'/'. $savename;
    $srcinfo = @getimagesize($srcImg);
    switch ($srcinfo[2]) {
        case 1: imagegif($srcImgObj, $savefile); break;
        case 2: imagejpeg($srcImgObj, $savefile); break;
        case 3: imagepng($srcImgObj, $savefile); break;
        default: return '保存失败'; //保存失败
    }

    return $savefile;
    imagedestroy($srcImgObj);
}

/**
 * Strong Blur
 *
 * @param  $gdImageResource  图片资源
 * @param  $blurFactor          可选择的模糊程度
 *  可选择的模糊程度  0使用   3默认   超过5时 极其模糊
 * @return GD image 图片资源类型
 * @author Martijn Frazer, idea based on http://stackoverflow.com/a/20264482
 */
function blur($gdImageResource, $blurFactor = 3)
{
    // blurFactor has to be an integer
    $blurFactor = round($blurFactor);

    $originalWidth = imagesx($gdImageResource);
    $originalHeight = imagesy($gdImageResource);

    $smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
    $smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));

    // for the first run, the previous image is the original input
    $prevImage = $gdImageResource;
    $prevWidth = $originalWidth;
    $prevHeight = $originalHeight;

    // scale way down and gradually scale back up, blurring all the way
    for($i = 0; $i < $blurFactor; $i += 1)
    {
        // determine dimensions of next image
        $nextWidth = $smallestWidth * pow(2, $i);
        $nextHeight = $smallestHeight * pow(2, $i);

        // resize previous image to next size
        $nextImage = imagecreatetruecolor($nextWidth, $nextHeight);
        imagecopyresized($nextImage, $prevImage, 0, 0, 0, 0,
            $nextWidth, $nextHeight, $prevWidth, $prevHeight);

        // apply blur filter
        imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);

        // now the new image becomes the previous image for the next step
        $prevImage = $nextImage;
        $prevWidth = $nextWidth;
        $prevHeight = $nextHeight;
    }

    // scale back to original size and blur one more time
    imagecopyresized($gdImageResource, $nextImage,
        0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
    imagefilter($gdImageResource, IMG_FILTER_GAUSSIAN_BLUR);

    // clean up
    imagedestroy($prevImage);

    // return result
    return $gdImageResource;
}

function image_create_from_ext($imgfile)
{
    $info = getimagesize($imgfile);
    $im = null;
    switch ($info[2]) {
        case 1: $im=imagecreatefromgif($imgfile); break;
        case 2: $im=imagecreatefromjpeg($imgfile); break;
        case 3: $im=imagecreatefrompng($imgfile); break;
    }
    return $im;
}

/**
 * 发送简单邮件
 *
 * @param string $email
 * @param string $title
 * @param string $content
 * @return array $data["sta"]=1; $data["sta"]=2;
 *         $data["msg"]="发送失败".$r;
 */
function sendSimpleEmail($email, $title, $content) {
    Vendor ( 'PHPMailer.PHPMailerAutoload' );
    $mail = new PHPMailer (); // 实例化
    $mail->IsSMTP (); // 启用SMTP
    $mail->Host = C ( 'MAIL_HOST' ); // smtp服务器的名称（这里以QQ邮箱为例）
    $mail->SMTPAuth = C ( 'MAIL_SMTPAUTH' ); // 启用smtp认证
    $mail->Username = C ( 'MAIL_USERNAME' ); // 你的邮箱名
    $mail->Password = C ( 'MAIL_PASSWORD' ); // 邮箱密码
    $mail->From = C ( 'MAIL_FROM' ); // 发件人地址（也就是你的邮箱地址）
    $mail->FromName = C ( 'MAIL_SEND_NAME' ); // 发件人姓名
    $mail->AddAddress ( $email, "尊敬的客户" );
    $mail->WordWrap = 50; // 设置每行字符长度
    $mail->IsHTML ( C ( 'MAIL_ISHTML' ) ); // 是否HTML格式邮件
    $mail->CharSet = C ( 'MAIL_CHARSET' ); // 设置邮件编码
    $mail->Subject = $title; // 邮件主题
    $mail->Body = $content; // 邮件内容
    // $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
    $r = $mail->Send ();
    if ($r) {
        $data ["sta"] = 1;
        $data ["msg"] = "邮件已送到您的邮箱";
    } else {
        $data ["sta"] = 2;
        $data ["msg"] = "发送失败";
    }
    return $data;
}

    function getpage($count, $pagesize) {
    //$p = new Think\Page($count, $pagesize);
    $p=new \Think\Page($count,$pagesize);
    $p->setConfig('header', '<li><a class="num">共%TOTAL_ROW%条记录</a></li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}

function in_quarters($time,$from,$to){
    $time=intval(date("His",strtotime($time)));
    $from=intval(date("His",strtotime($from)));
    $to=intval(date("His",strtotime($to)))-1;
    if($time>=$from && $time<$to){
        return true;
    }else{
        return false;
    }
}

/**
 *用户系统消息写入
 */
function set_message($member_id,$message,$order_id,$type){
    $data['member_id'] = $member_id;
    $data['intime'] = date("Y-m-d H:i:s",time());
    $data['message'] = $message;
    $data['order_id'] = $order_id;
    $data['type'] = $type;
    $result = M('Message')->add($data);
    if($result){
        return true;
    }else{
        return false;
    }
}

//验证身份证是否有效
function validateIDCard($IDCard) {
    if (strlen($IDCard) == 18) {
        return check18IDCard($IDCard);
    } elseif ((strlen($IDCard) == 15)) {
        $IDCard = convertIDCard15to18($IDCard);
        return check18IDCard($IDCard);
    } else {
        return false;
    }
}

//计算身份证的最后一位验证码,根据国家标准GB 11643-1999
function calcIDCardCode($IDCardBody) {
    if (strlen($IDCardBody) != 17) {
        return false;
    }

    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $code = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;

    for ($i = 0; $i < strlen($IDCardBody); $i++) {
        $checksum += substr($IDCardBody, $i, 1) * $factor[$i];
    }

    return $code[$checksum % 11];
}

// 将15位身份证升级到18位
function convertIDCard15to18($IDCard) {
    if (strlen($IDCard) != 15) {
        return false;
    } else {
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($IDCard, 12, 3), array('996', '997', '998', '999')) !== false) {
            $IDCard = substr($IDCard, 0, 6) . '18' . substr($IDCard, 6, 9);
        } else {
            $IDCard = substr($IDCard, 0, 6) . '19' . substr($IDCard, 6, 9);
        }
    }
    $IDCard = $IDCard . calcIDCardCode($IDCard);
    return $IDCard;
}

// 18位身份证校验码有效性检查
function check18IDCard($IDCard) {
    if (strlen($IDCard) != 18) {
        return false;
    }

    $IDCardBody = substr($IDCard, 0, 17); //身份证主体
    $IDCardCode = strtoupper(substr($IDCard, 17, 1)); //身份证最后一位的验证码

    if (calcIDCardCode($IDCardBody) != $IDCardCode) {
        return false;
    } else {
        return true;
    }
}

/**
 * 根据身份证判断,是否满足年龄条件
 * @param type $IDCard 身份证
 * @param type $minAge 最小年龄
 */
function isMeetAgeByIDCard($IDCard, $minAge) {
//    $ret = validateIDCard($IDCard);
//    if ($ret === FALSE) {
//        return FALSE;
//    }

    if (strlen($IDCard) <= 15) {
        $IDCard = convertIDCard15to18($IDCard);
    }

    $year = date('Y') - substr($IDCard, 6, 4);
    $monthDay = date('md') - substr($IDCard, 10, 4);

    return ($year > $minAge || $year == $minAge && $monthDay > 0) ? TRUE : FALSE;
}

/**
 *积分记录
 */
function note_score($member_id,$order_id,$order_type,$score,$extra_number,$detail,$type){
    $data['member_id'] = $member_id;
    $data['order_id'] = $order_id;
    $data['order_type'] = $order_type;
    $data['number'] = $score;
    $data['extra_number'] = $extra_number;
    $data['detail'] = $detail;
    if(empty($type)) $data['type']=1; else $data['type'] = $type;
    $data['intime'] = date("Y-m-d H:i:s",time());
    $result = M('ScoreRecord')->add($data);
    if($result){
        return true;
    }else{
        return false;
    }
}

function trimall($str)//删除空格
{
    $qian=array(" ","　","\t","\n","\r");
    $hou=array("","","","","");
    return str_replace($qian,$hou,$str);
}

function  get_week($date)
{
    //强制转换日期格式
    $date_str = date('Y-m-d', strtotime($date));
    //封装成数组
    $arr = explode("-", $date_str);
    //参数赋值
    //年
    $year = $arr[0];
    //月，输出2位整型，不够2位右对齐
    $month = sprintf('%02d', $arr[1]);
    //日，输出2位整型，不够2位右对齐
    $day = sprintf('%02d', $arr[2]);
    //时分秒默认赋值为0；
    $hour = $minute = $second = 0;
    //转换成时间戳
    $strap = mktime($hour, $minute, $second, $month, $day, $year);
    //获取数字型星期几
    $number_wk = date("w", $strap);
    //自定义星期数组
    $weekArr = array("周日", "周一", "周二", "周三", "周四", "周五", "周六");

    //获取数字对应的星期
    return $weekArr[$number_wk];
}
function checkSubstrs($list,$str){
    $flag = false;
    for($i=0;$i<count($list);$i++){
        if(strpos($str,$list[$i]) > 0){
            $flag = true;
            break;
        }
    }
    return $flag;
}

function check_scan(){
    if (isset($_SERVER['HTTP_VIA'])) return true;
    if (isset($_SERVER['HTTP_X_NOKIA_CONNECTION_MODE'])) return true;
    if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])) return true;
    if (strpos(strtoupper($_SERVER['HTTP_ACCEPT']),"VND.WAP.WML") > 0) {
        // Check whether the browser/gateway says it accepts WML.
        $br = "WML";
    } else {
        $browser = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
        if(empty($browser)) return true;
        $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
        $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');
        $found_mobile=checkSubstrs($mobile_os_list,$browser) ||checkSubstrs($mobile_token_list,$browser);
        if($found_mobile)
            $br ="WML";
        else $br = "WWW";
    }
}

/**
 *@curl_post提交json数据
 */
//function curl_post_json($url,$data){
//    $ch = curl_init($url);
//    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_TIMEOUT, 20); //超时
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json',
//            'Content-Length: ' . strlen($data))
//    );
//    $result = curl_exec($ch);
//    curl_close($ch);//释放句柄
//    return $result;
//}
function curl_post_json($url,$data){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出;去掉就直接输出
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: '.strlen($data))
    );
    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    return $result;
}

function httpcopy($url,$file='',$timeout=60){
    $file = empty($file) ? pathinfo($url,PATHINFO_BASENAME) : $file;
    $dir = pathinfo($file,PATHINFO_DIRNAME);
    !is_dir($dir)&&@mkdir($dir,0755,true);
    $url = str_replace(' ',"%20",$url);
    $result = array('fileName'=>'','way'=>'','size'=>0,'spendTime'=>0);
    $startTime=explode(' ',microtime());
    $startTime=(float)$startTime[0]+(float)$startTime[1];
    if(function_exists('curl_init')){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $temp=curl_exec($ch);
        return $temp;
        if(@file_put_contents($file,$temp)&&!curl_error($ch)){
            $result['fileName']=$file;
            $result['way']='curl';
            $result['size']=sprintf('%.3f',strlen($temp)/1024);
        }
    }else{
        $opts=array(
            'http'=>array(
                'method'=>'GET',
                'header'=>'',
                'timeout'=>$timeout
            )
        );
        $context=stream_context_create($opts);
        if(@copy($url,$file,$context)){
            $result['fileName']=$file;
            $result['way']='copy';
            $result['size']=sprintf('%.3f',strlen($context)/1024);
        }
    }
    $endTime=explode(' ',microtime());
    $endTime=(float)$endTime[0]+(float)$endTime[1];
    $result['spendTime']=round($endTime-$startTime)*1000;//单位：毫秒
    return $result;
 }

function down_url($file){
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    }
}

/**
 *@写入用户余额记录
 */
function set_amount($member_id,$amount,$type,$content){
    $data['member_id'] = $member_id;
    $data['amount'] =   $amount;
    $data['type']   =   $type;
    $data['content']    = $content;
    $data['intime'] =   date("Y-m-d H:i:s",time());
    $result = M('AmountRecord')->add($data);
    if($result){
        return true;
    }else{
        return false;
    }
}

function curl_get($url)
{
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOP_TIMEOUT, "60");
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //运行curl，结果以jason形式返回
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function https_request($url, $data)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
//    $errorno= curl_errno($curl);
//    if ($errorno) {
//        return array('curl'=>false,'errorno'=>$errorno);
//    }else{
//        $ob= simplexml_load_string($output);
//        $json  = json_encode($ob);
//        $configData = json_decode($json, true);
//        return $configData;
//    }
    curl_close($curl);
    return $output;
}

/**
 *@生成uuid
 */
function uuid($prefix = '')
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid  = substr($chars,0,8) . '-';
    $uuid .= substr($chars,8,4) . '-';
    $uuid .= substr($chars,12,4) . '-';
    $uuid .= substr($chars,16,4) . '-';
    $uuid .= substr($chars,20,12);
    return $prefix . $uuid;
}

function translate_date($date){
    $today = strtotime(date("Y-m-d",time()));
    $time = strtotime($date);
    $value = $time - $today;
    if($value>0){
        $minutes = ceil((time()-$time)/60);
        if($minutes <=3 ){
                return '刚刚';
        }elseif(3<$minutes && $minutes<60){
            return $minutes.'分钟前';
        }else{
            $hour =  floor($minutes/60);
            return $hour.'小时前';
        }
    }else{
        if($today-$time<24*3600){
            return '昨天';
        }else{
            return date("Y-m-d",$time);
        }
    }
}

/*
    往黑名单中加人
*/
function addUserForBlacklist($username,$usernames){
    $url = "https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/users/$username/blocks/users";
    $body=json_encode(['usernames'=>[$usernames]]);
    $header=array(getTokens());
    $result=postCurl($url,$body,$header,'POST');
    return $result;
}



/*
        从黑名单中减人
    */
function deleteUserFromBlacklist($username,$blocked_name){
    $url = "https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/users/$username/blocks/users/".$blocked_name;
    //$url=$this->url.'users/'.$username.'/blocks/users/'.$blocked_name;
    $header=array(getTokens());
    $result=postCurl($url,'',$header,'DELETE');
    return $result;

}

/**
 * @助通短信发送
 */
function zhutong_sendSMS($content,$mobile){
    $url 		= "http://www.ztsms.cn/sendNSms.do";//提交地址
    $username 	= "YLD";//用户名
    $password 	= "Yld123456";//原密码
    // 初始化
    import('Vendor.zhutong.zhutong');
    $data = array(
        'content' 	=> $content,//短信内容
        'mobile' 	=> $mobile,//手机号码
        'productid' => '676767',//产品id
        'xh'		=> ''//小号
    );
    $sendAPI = new \sendAPI($url, $username, $password);
    $sendAPI->data = $data;//初始化数据包
    $return = $sendAPI->sendSMS('POST');//GET or POST
    return $return;
}

function GetfourStr($len){
    $chars_array = array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
    $charsLen = count($chars_array) - 1;
    $outputstr = "";
    for ($i=0; $i<$len; $i++)
    {
        $outputstr .= $chars_array[mt_rand(0, $charsLen)];
    }
    return $outputstr;
}

function get_number(){
    $digits = M('System')->where(['id'=>1])->getField('digits');
    $a = range(0,9);
    for($i=0;$i<$digits;$i++){
        $b[] = array_rand($a);
    }
    $rs=join("",$b);
    return $rs;
}
/**
 * @随机生成7位数字
 */
function get_number7(){
    $a = range(0,9);
    for($i=0;$i<7;$i++){
        $b[] = array_rand($a);
    }
    $rs=join("",$b);
    if(M('User')->where(['ID'=>$rs])->find()){
        get_number7();
    }else{
        return $rs;
    }
}

/**
 *对象转化数组
 */
function std_class_object_to_array($stdclassobject)
{
    $_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;

    foreach ($_array as $key => $value) {
        $value = (is_array($value) || is_object($value)) ? std_class_object_to_array($value) : $value;
        $array[$key] = $value;
    }

    return $array;
}

/**
 * @等级提升
 */
function ascension_grade($user_id,$experience){
    $grade = M('User')->where(['user_id'=>$user_id])->getField('grade');
    $level = M('Level')->field('level,experience')->where(['experience'=>['ELT',$experience]])->order("level_id desc")->limit(1)->find();
    $big_level = M('Level')->field('level,experience')->order('level desc')->limit(1)->find();
    if ($big_level['level']==$grade){
        true;
    }else{
        if($grade != $level['level']){
            M('User')->where(['user_id'=>$user_id])->save(['grade'=>$level['level'],'uptime'=>time()]);
        }
    }
}

/**
 * @计算两个时间戳相差的月份
 */
function get_month_value($time1,$time2){
    $year1  = date("Y",$time1);   // 时间1的年份
    $month1 = date("m",$time1);   // 时间1的月份
    $year2  = date("Y",$time2);   // 时间2的年份
    $month2 = date("m",$time2);   // 时间2的月份
    // 相差的月份
    $value =  ($year2 * 12 + $month2) - ($year1 * 12 + $month1);
    return $value;
}

function array_to_xml($arr){
    $xml = "<root>";
    foreach ($arr as $key=>$val){
        if(is_array($val)){
            $xml.="<".$key.">".array_to_xml($val)."</".$key.">";
        }else{
            $xml.="<".$key.">".$val."</".$key.">";
        }
    }
    $xml.="</root>";
    return $xml;
}

function arrayToXml($arr,$dom=0,$item=0){
    if (!$dom){
        $dom = new DOMDocument("1.0");
    }
    if(!$item){
        $item = $dom->createElement("root");
        $dom->appendChild($item);
    }
    foreach ($arr as $key=>$val){
        $itemx = $dom->createElement(is_string($key)?$key:"item");
        $item->appendChild($itemx);
        if (!is_array($val)){
            $text = $dom->createTextNode($val);
            $itemx->appendChild($text);

        }else {
            arrayToXml($val,$dom,$itemx);
        }
    }
    return $dom->saveXML();
}


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

function action_curl_pos(){
    var_dump($_FILES);
    $aa= move_uploaded_file($_FILES["upload"]["tmp_name"], "/wamp/tools/1.rar");
    if($aa){
        echo "11";
    }
}

