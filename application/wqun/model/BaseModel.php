<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018-01-08
 * Time: 11:10
 */

namespace app\wqun\model;


use app\wqun\extend\MemcacheSASL;
use think\Model;

class BaseModel extends Model
{
    //自动写入时间，可直接读取
    protected $autoWriteTimestamp = 'datetime';

    public $mc;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->mc = new MemcacheSASL();
        //$this->mc->addServer();
        //$this->mc->setSaslAuthData();
        $this->mc->setSaveHandler();
    }

    /**
     * 获取某条数据的某个字段
     *
     * @param string $where
     * @param string $feild
     * @return array|null
     */
    public function getOne($where = '', $field = ''){
        $res = self::where($where)->field($field)->find();
        if($res){
            return $res->toArray();
        }else{
            return null;
        }
    }

    /**
     * 获取多条数据的某个字段，按id降序排列
     *
     * @param string $where
     * @param string $field
     * @param array $order
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAll($where = '', $field = '', $order = ['id' => 'desc']){
        $res = self::where($where)->field($field)->order($order)->select();
        return $res;
    }

    /**
     * 获取指定条数(默认30条)数据的某个字段，按id降序排列
     *
     * @param string $where
     * @param string $field
     * @param string $limit
     * @param array $order
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getLimit($where = '', $field = '', $limit = '0,30', $order = ['id' => 'desc']){
        $res = self::where($where)->field($field)->limit($limit)->order($order)->select();
        return $res;
    }

    /**
     * 获取指定条数数据的某个字段，按id降序排列,分页展示(分页展示)
     *
     * @param string $where
     * @param string $field
     * @param int $page
     * @param array $order
     * @return array|null
     */
    public function getPaginate($where = '', $field = '', $page = 30, $order = ['id' => 'desc']){
        $res = self::where($where)->field($field)->order($order)->paginate($page, false, ['query' => request()->param()]);
        if($res){
            $data = $res->toArray();
            $data['page'] = $res->render();
        }else{
            $data = null;
        }
        return $data;
    }

    /**
     * 增加或者更新数据
     * 返回更新结果。false更新失败；成功返回受影响行数(正正数)；0执行成功，但没更改数据库
     * 判断执行失败的时候要用恒等于(===false，用==false时，则0也能通过)
     *
     * @param string $where
     * @param string $data
     * @return array|false|int
     */
    public function addOrUp($where = '', $data = ''){
        $res = self::save($data, $where);
        if ($res){
            return $this->toArray();
        }else{
            return $res;
        }
    }

    /**
     * 删除数据
     *
     * @param $where
     * @return int
     */
    public function del($where){
        $res = self::where($where)->delete();
        return $res;
    }

    /**
     * 自增
     *
     * @param array|string $where
     * @param int $field
     * @param $step
     * @return int|true
     */
    public function inc($where, $field, $step){
        $res = self::where($where)->setInc($field, $step);
        return $res;
    }

    /**
     * 自减
     *
     * @param array|string $where
     * @param int $field
     * @param $step
     * @return int|true
     */
    public function dec($where, $field, $step){
        $res = self::where($where)->setDec($field, $step);
        return $res;
    }
}