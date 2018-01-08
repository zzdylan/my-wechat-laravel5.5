<?php

namespace App\Http\Controllers;

use Log;
use EasyWeChat;
use EasyWeChat\Kernel\Messages\Image;
use App\Classes\TuLing;
use App\Classes\Girl;

class WeChatController extends Controller {

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve() {
        Log::info('request arrived.'); 
        $app = app('wechat.official_account');
        $app->server->push(function($message) {
            switch ($message['MsgType']) {
                case 'event':
                    return "/::D哇咔咔，欢迎来到阿震的项目分享，以后会经常分享一些好玩的东西，让我们拭目以待！本订阅号已对接机器人，欢迎来撩\n\r回复'功能'查看当前公众号功能";
                    //return '收到事件消息';
                    break;
                case 'text':
                    if ($message['Content'] == '功能') {
                        $text = [
                            "1、妹子(有女朋友的同志慎用):回复关键字'妹子'有福利(爬虫技术和微信开发结合，后面的教程中会讲解到)",
                            "2、跳一跳刷分:菜单中点击'历史文章'查看跳一跳刷分教程",
                            "3、图灵机器人:回复除关键字以外的文字可以撩图灵机器人(机器人有很多实用的功能哦，比如查快递等等)"
                        ];
                        return implode("\n\r", $text);
                    } elseif ($message['Content'] == '妹子') {
                        $officialAccount = EasyWeChat::officialAccount();
                        $girlPath = Girl::findGirl();
                        $upload = $officialAccount->media->uploadImage($girlPath);
                        $image = new Image($upload['media_id']);
                        return $image;
                    }
                    $tuLing = new TuLing();
                    $res = $tuLing->bot($message['Content'], $message['FromUserName']);
                    return $res['values'][$res['resultType']];
                    //return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    //return "哇咔咔，欢迎来到阿震的项目分享，以后会经常分享一些好玩的东西，让我们拭目以待！";
                    break;
            }
        });

        return $app->server->serve();
    }

}
