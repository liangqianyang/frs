<?php

namespace app\index\controller;

use HuaweiCloud\SDK\Core\Auth\BasicCredentials;
use HuaweiCloud\SDK\Core\Exceptions\ConnectionException;
use HuaweiCloud\SDK\Core\Exceptions\RequestTimeoutException;
use HuaweiCloud\SDK\Core\Exceptions\ServiceResponseException;
use HuaweiCloud\SDK\Core\Http\HttpConfig;
use HuaweiCloud\SDK\Frs\V2\FrsClient;
use HuaweiCloud\SDK\Frs\V2\Model\DetectLiveByBase64Request;
use HuaweiCloud\SDK\Frs\V2\Model\DetectLiveFaceByBase64Request;
use HuaweiCloud\SDK\Frs\V2\Model\LiveDetectBase64Req;
use HuaweiCloud\SDK\Frs\V2\Model\LiveDetectFaceBase64Req;
use think\Controller;
use think\facade\Env;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function activeLiveness()
    {
        $config = HttpConfig::getDefaultConfig();
        $ak = Env::get('HUAWEICLOUD_SDK_AK');
        $sk = Env::get('HUAWEICLOUD_SDK_SK');
        $endpoint = "https://face.cn-north-4.myhuaweicloud.com";
        $projectId = Env::get('HUAWEICLOUD_PROJECT_ID');
        $credentials = new BasicCredentials($ak, $sk, $projectId);
        $client = FrsClient::newBuilder()
            ->withHttpConfig($config)
            ->withEndpoint($endpoint)
            ->withCredentials($credentials)
            ->build();
        $request = new DetectLiveByBase64Request();
        $body = new LiveDetectBase64Req();
        $body->setActions("1,2");
        $body->setVideoBase64("视频数据的base64编码");
        $request->setBody($body);
        try {
            $response = $client->DetectLiveByBase64($request);
            echo $response;
        } catch (ConnectionException|RequestTimeoutException $e) {
            $msg = $e->getMessage();
            echo "\n" . $msg . "\n";
        } catch (ServiceResponseException $e) {
            echo "\n";
            echo $e->getHttpStatusCode() . "\n";
            echo $e->getErrorCode() . "\n";
            echo $e->getErrorMsg() . "\n";
        }
    }
}
