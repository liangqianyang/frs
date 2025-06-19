<?php

namespace app\index\controller;

use HuaweiCloud\SDK\Core\Auth\BasicCredentials;
use HuaweiCloud\SDK\Core\Exceptions\ConnectionException;
use HuaweiCloud\SDK\Core\Exceptions\RequestTimeoutException;
use HuaweiCloud\SDK\Core\Exceptions\ServiceResponseException;
use HuaweiCloud\SDK\Core\Http\HttpConfig;
use HuaweiCloud\SDK\Frs\V2\FrsClient;
use HuaweiCloud\SDK\Frs\V2\Model\DetectLiveByBase64Request;
use HuaweiCloud\SDK\Frs\V2\Model\LiveDetectBase64Req;
use think\Controller;
use think\facade\Env;
use think\response\Json;

class Index extends Controller
{
    private array $warningList = [
        1 => '人脸没有朝向前方',
        2 => '视频没有超过1秒',
        3 => '视频超过15秒',
        4 => '两个人脸',
        5 => '没有人脸',
        6 => '动作幅度太小',
        7 => '视频质量差或者视频拍摄不是真人',
        8 => '选择不出优选图片',
        101 => '整体人脸质量过低',
        102 => '人脸模糊',
        103 => '人脸姿态太大',
        104 => '人脸有遮挡',
        105 => '图片太暗，光照不够',
        106 => '图片中包含多张人脸',
    ];

    public function index()
    {
        return $this->fetch();
    }

    /**
     * 人脸动作活体检测
     * @return Json
     */
    public function activeLiveness(): Json
    {
        $result = ['alive' => false, 'picture' => '', 'error' => ''];
        $actions = $this->request->param('actions', []);
        $video = $this->request->param('video', '');
        if (empty($actions) || empty($video)) {
            return json(['code' => 400, 'msg' => '参数错误']);
        }
        preg_match('/base64,(.*)/', $video, $matches);
        if (!isset($matches[1])) {
            return json(['code' => 400, 'msg' => '视频格式错误']);
        }
        $video = $matches[1];
        $actions = implode(',', $actions);
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
        $body->setActions($actions);
        $body->setVideoBase64($video);
        $request->setBody($body);
        try {
            $response = $client->DetectLiveByBase64($request);
            if ($response) {
                $result['alive'] = $response['videoResult']['alive'];
                $result['picture'] = $response['videoResult']['picture'];
                if (!empty($response['warningList'])) {
                    $warnings = [];
                    foreach ($response['warningList'] as $warning) {
                        if (isset($this->warningList[$warning['warningCode']])) {
                            $warnings[] = $this->warningList[$warning['warningCode']];
                        } else {
                            $warnings[] = '未知警告：' . $warning['warningMsg'];
                        }
                    }
                    $result['error'] = implode('；', $warnings);
                }
            }
        } catch (ConnectionException|RequestTimeoutException $e) {
            $result['error'] = $e->getMessage();
        } catch (ServiceResponseException $e) {
            $error = '状态码：' . $e->getHttpStatusCode() . '，错误码：' . $e->getErrorCode() . '，错误信息：' . $e->getErrorMsg();
            $result['error'] = $error;
        }
        return json($result);
    }
}
