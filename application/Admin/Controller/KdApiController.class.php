<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/5
 * Time: 17:35
 */

namespace Admin\Controller;


use Think\Controller;

class KdApiController extends Controller
{
    private $EBusinessID = '';
    private $AppKey = '';
    private $ReqURL = '';
    public function _initialize(){
        $this->EBusinessID = '1275645';
        $this->AppKey = 'd05e6ce6-bd4b-4377-8727-20c42ac740ad';
        $this->ReqURL = 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';
    }

//调用查询物流轨迹
//---------------------------------------------


//---------------------------------------------



    /**
     * Json方式 查询订单物流轨迹
     */
    public function getTracesByJson(){
        /**
         *  post提交数据
         * @param  string $url 请求Url
         * @param  array $datas 提交的数据
         * @return url响应返回的html
         */
        function sendPost($url, $datas) {
            $temps = array();
            foreach ($datas as $key => $value) {
                $temps[] = sprintf('%s=%s', $key, $value);
            }
            $post_data = implode('&', $temps);
            $url_info = parse_url($url);
            if(empty($url_info['port']))
            {
                $url_info['port']=80;
            }
            $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
            $httpheader.= "Host:" . $url_info['host'] . "\r\n";
            $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
            $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
            $httpheader.= "Connection:close\r\n\r\n";
            $httpheader.= $post_data;
            $fd = fsockopen($url_info['host'], $url_info['port']);
            fwrite($fd, $httpheader);
            $gets = "";
            $headerFlag = true;
            while (!feof($fd)) {
                if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                    break;
                }
            }
            while (!feof($fd)) {
                $gets.= fread($fd, 128);
            }
            fclose($fd);

            return $gets;
        }

        /**
         * 电商Sign签名生成
         * @param data 内容
         * @param appkey Appkey
         * @return DataSign签名
         */
        function encrypt_sign($data, $appkey) {
            return urlencode(base64_encode(md5($data.$appkey)));
        }

        $danhao = I('kuaidi');            //单号
        $code = I('kuaidi_node');           //物流公司编码
        if(empty($code))        error("物流公司标识不能为空");
        if(empty($danhao))        error("单号不能为空");
        $requestData= "{'OrderCode':'','ShipperCode':'".$code."','LogisticCode':'".$danhao."'}";
        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = encrypt_sign($requestData, $this->AppKey);
        $result= sendPost($this->ReqURL, $datas);

        //根据公司业务处理返回的信息......

        $result = json_decode($result,true);
        success($result);
    }

}