<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018-01-08
 * Time: 14:53
 */

namespace app\wqun\model;


class Qun extends BaseModel
{
    /**
     * 获取最大打开数和最大长按数
     *
     * @param $id   项目id
     * @return array|bool|float|int|mixed|null|string
     */
    public function getMaxOpenAndTouch($id){
        $key = "qun_{$id}";
        if(!$this->mc->get($key)){
            $res = $this->getOne(['id' => $id], ['key', 'max_open', 'max_touch']);
            $this->mc->set($key, $res, 86400 * 30);
            return $res;
        }
        return $this->mc->get($key);
    }

    /**
     * 获取当前二维码
     *
     * @param $id   //项目id
     * @return bool|float|int|mixed|string
     */
    public function getCurrentQRCode($id){
        $key = "now_ewm_{$id}";
        if(!$this->mc->get($key)){
            $res = $this->getOne(['id' => $id], ['current_qr_code']);
            return $res['current_qr_code'];
        }
        return $this->mc->get($key);
    }

    /**
     * 设置当前二维码
     *
     * @param $id           //项目id
     * @param $qr_code_id   //二维码id
     */
    public function setCurrentQRCode($id, $qr_code_id){
        $qun_code = new QRCode();
        $qr_code = $qun_code->getOne(['qun_id' => $id, 'qr_code_id' => $qr_code_id], ['id']);
        if(!$qr_code){
            //没有，存入数据库
            $data['qun_id'] = $id;
            $data['qr_code_id'] = $qr_code_id;
            $data['start_time'] = time();
            $qun_code->save($data);
        }

        //更新当前二维码
        $this->update(['current_qr_code' => $qr_code_id], ['id' => $id]);
        $key = "now_ewm_{$id}";
        $this->mc->set($key, $qr_code_id, 86400 * 30);
    }
}