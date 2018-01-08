<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018-01-08
 * Time: 15:31
 */

namespace app\wqun\model;


class QRCode extends BaseModel
{
    /**
     * 二维码打开次数加1;
     *
     * @param $id               //项目id
     * @param $qr_code_id       //二维码id
     * @return int|true
     */
    public function addOpen($id, $qr_code_id){
        return $this->inc(['qun_id' => $id, 'qr_code_id' => $qr_code_id], 'opens', 1);
    }

    /**
     * 二维码长按次数加1；
     *
     * @param $id               //项目id
     * @param $qr_code_id       //二维码id
     * @return int|true
     */
    public function addTouch($id, $qr_code_id){
        return $this->inc(['qun_id' => $id, 'qr_code_id' => $qr_code_id], 'touchs', 1);
    }

    /**
     * 获取二维码打开次数和长按次数
     *
     * @param $id               //项目id
     * @param $qr_code_id       //二维码id
     * @return array|null
     */
    public function getQRCodeOpenAndTouch($id, $qr_code_id){
        return $this->getOne(['qun_id' => $id, 'qr_code_id' => $qr_code_id], ['opens', 'touchs']);
    }
}