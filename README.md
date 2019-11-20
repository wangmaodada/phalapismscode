# phalapismscode

第一步 了解 phalapi 的操作方法；
   这里是第一版的操作，这个会有一个错误产生，看实际操作 https://segmentfault.com/a/1190000016280823
   
第二步 配置参数的获取 

ak 参数（了解阿里就知道是什么参数）
短信发送和 code

第三步 阿里云上的短信文档 
https://dysms.console.aliyun.com/dysms.htm?spm=5176.12818093.recent.ddysms.488716d0qg8cYF#/quickStart

第四步 文件下载配置
https://image-static.segmentfault.com/625/311/625311003-5b8f90af49fbb_articlex

第五步 参数复制 


下方直接复制黏贴 哈哈
"vendor/phalapi/aliyunsms/api_sdk/vendor/autoload.php",
"vendor/phalapi/aliyunsms/msg_sdk/vendor/autoload.php"


下方直接创建一个
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
 * 
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
        //*******
        $accessKeyId = ""; // AccessKeyId
        //************
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
    public function getCode($data)
    {
    //手机号
        $phoneNumbers = $data['Mobile'];
        //验证码
        $num=$data['code'];
        $signName = '短信';
        $templateCode = '短信模版';
        $outId=1231;
        $response = $this->sendSms($phoneNumbers,$signName,$templateCode,$outId,$num);
        return $response;
    }

}

