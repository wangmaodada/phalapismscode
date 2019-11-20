<?php 
namespace App\Api;

use PhalApi\Api;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

/**
 * 短信接口
 *  @auth wangmao 
 *  @desc 当前接口用来获取短信认证
 *  @desc 当前接口已废弃
 */ 
Config::load();

class Sms extends Api
{
    static $acsClient = null;
    public function getRules() {
        return array(
            'getCode' => array(
                'Mobile' => array('name' => 'Mobile', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '用户名'),
                'token' => array('name' => 'token', 'require' => true, 'min' => 6, 'max' => 80, 'desc' => '验证'),
                'shebeiid' => array('name' => 'shebeiid', 'require' => true, 'min' => 6, 'max' => 80, 'desc' => 'shebeiid'),
            ),
        );
    }
    /**
     * 获取AcsClient
     * @desc 获取链接
     * @return DefaultAcsClient
     */
    public static function getAcsClient() {
        //产品名称:云通信短信服务API产品
        $product = "Dysmsapi";

        //产品域名
        $domain = "dysmsapi.aliyuncs.com";

        // accesskey
        $accessKeyId = ""; // AccessKeyId

        $accessKeySecret = ""; // AccessKeySecret

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";


        if(static::$acsClient == null) {

            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }

    /**
     * 发送短信
     * @desc 发送短信认证
     * @param setPhoneNumbers接收的电话号码
     * @param setSignName  签名名称
     * @param setTemplateCode  短信模板CODE
     *
     * @return stdClass
     */
    public static function sendSms($phoneNumbers,$signName,$templateCode,$outId=null,$num) {

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        //设置短信接收号码
        $request->setPhoneNumbers($phoneNumbers);

        //设置签名名称
        $request->setSignName($signName);

        //设置短信模板CODE
        $request->setTemplateCode($templateCode);


		
        // 可选，设置模板参数
        //下方的array 可以自定义设置i并传递
        //*****
        $request->setTemplateParam(json_encode(array("production"=>'问壶藏家',  // 短信模板中字段的值
            "code"=>$num
        ), JSON_UNESCAPED_UNICODE));

        //$outId = null;

        // 可选，设置流水号
        $request->setOutId($outId);

        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        return $acsResponse;
    }
	/**
	 * 获取验证码
	 * 
	 * 
	 */ 
    public function setCode($data)
    {
 
        //调用当前函数传递手机号 验证码
        $phoneNumbers = $data['Mobile'];
        $num=$data['code'];
        $signName = '问壶藏家';
        $templateCode = '模版';
        $outId=1231;
        $response = $this->sendSms($phoneNumbers,$signName,$templateCode,$outId,$num);
        return $response;
    }

}
