<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018-01-08
 * Time: 11:09
 */

namespace app\wqun\model;


class User extends BaseModel
{
    /**
     * 获取用户状态，判断是否拉黑
     *
     * @param $openid
     * @return array|null
     */
    public function getUserStatus($openid)
    {
        $res = self::getOne(['openid' => $openid], ['black']);
        return $res;
    }

    /**
     * 通过openid 拉黑或者拉白用户
     *
     * @param $openid
     * @param $ip
     * @param $status 0为正常用户， 1表示被拉黑用户
     * @return false|int
     */
    public function defriendOpenid($openid, $ip, $status)
    {
        if ($openid) {
            self::update(['black' => $status], ['openid' => $openid]);
        }
        $black_openid = new BlackOpenid();
        if ($status) {
            $data['openid'] = $openid;
            $data['ip'] = $ip;
            $res = $black_openid->save($data);
        } else {
            $res = $black_openid->where(['openid' => $openid])->delete();
        }
        return $res;
    }

    /**
     * 通过ip 拉黑或者拉白用户
     *
     * @param $openid
     * @param $ip
     * @param $status
     * @return false|int
     */
    public function defriendIp($openid, $ip, $status)
    {
        if ($openid) {
            self::update(['black' => $status], ['openid' => $openid]);
        }
        $black_ip = new BlackIp();
        if ($status) {
            $data['ip'] = $ip;
            $res = $black_ip->save($data);
        } else {
            $res = $black_ip->where(['ip' => $ip])->delete();
        }
        return $res;
    }

    public function getUserQRCode($id, $openid)
    {
        $key = "user_ewm_{$id}_{$openid}";

        //缓存里面没有
        if (!$this->mc->get($key)) {
            $user = $this->getOne(['qun_id' => $id, 'openid' => $openid], ['qr_code_id']);
            if ($user) {
                $user_qr_code = $user['qr_code_id'];
            } else {//用户不存在
                $quns = new Qun();
                $current_qr_code = $quns->getCurrentQRCode($id);
                $ips = getIps();
                $data['openid'] = $openid;
                $data['ip'] = $ips['ip'];
                $data['ip2'] = $ips['ip2'];
                $data['entrance'] = input('id', 0);
                $data['qun_id'] = $id;
                $data['qr_code_id'] = $current_qr_code;
                $data['opens'] = 0;
                $this->save($data);
                //二维码打开人次加1
                $qr_code = new QRCode();
                $qr_code->addOpen($id, $current_qr_code);
                $user_qr_code = $current_qr_code;
            }
            $this->mc->set($key, $user_qr_code, 86400 * 30);
            return $user_qr_code;
        }
        return $this->mc->get($key);
    }
}