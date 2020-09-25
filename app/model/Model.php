<?php
/**
 * KirySaaS--------||bai T o o Y ||
 * =========================================================
 * ----------------------------------------------
 * User Mack Qin
 * Copy right 2019-2029 kiry 保留所有权利。
 * ----------------------------------------------
 * =========================================================
 */


namespace app\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\Validate;

/**
 * 模型基类
 */
class Model
{

    // 查询对象
    private static $query_obj = null;
    //验证规则
    protected $rule = [];
    //验证信息
    protected $message = [];
    //验证场景
    protected $scene = [];
    //错误信息
    protected $error;

    protected $table;

    public function __construct($table = '')
    {
        if ($table) {
            $this->table = $table;
        }
    }

    /**
     * 获取列表数据
     * @param array $condition
     * @param bool $field
     * @param string $order
     * @param string $alias
     * @param array $join
     * @param string $group
     * @param null $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    final public function getList($condition = [], $field = true, $order = '', $alias = 'a', $join = [], $group = '', $limit = null)
    {
        self::$query_obj = Db::name($this->table)->where($condition)->order($order);

        if (!empty($join)) {
            self::$query_obj->alias($alias);
            self::$query_obj = $this->parseJoin(self::$query_obj, $join);
        }

        if (!empty($group)) {
            self::$query_obj = self::$query_obj->group($group);
        }

        if (!empty($limit)) {
            self::$query_obj = self::$query_obj->limit($limit);
        }

        $result = self::$query_obj->field($field)->select()->toArray();

        self::$query_obj->removeOption();
        return $result;
    }

    final public function all()
    {
        return Db::name($this->table)->select()->toArray();
    }

    /**
     * 获取分页列表数据
     * @param array $condition
     * @param bool $field
     * @param string $order
     * @param int $page
     * @param int $list_rows
     * @param string $alias
     * @param array $join
     * @param null $group
     * @param null $limit
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    final public function pageList($condition = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = [], $group = null, $limit = null)
    {
        self::$query_obj = Db::name($this->table)->alias($alias)->where($condition)->order($order);
        $count_obj       = Db::name($this->table)->alias($alias)->where($condition)->order($order);
        if (!empty($join)) {
            $db_obj          = self::$query_obj;
            self::$query_obj = $this->parseJoin($db_obj, $join);
            $count_obj       = $this->parseJoin($count_obj, $join);
        }

        if (!empty($group)) {
            self::$query_obj = self::$query_obj->group($group);
            $count_obj       = $count_obj->group($group);
        }

        if (!empty($limit)) {
            self::$query_obj = self::$query_obj->limit($limit);
        }

        $count = $count_obj->count();
        if ($list_rows == 0) {
            //查询全部
            $result_data          = self::$query_obj->field($field)->limit($count)->page($page)->select()->toArray();
            $result['page_count'] = 1;
        } else {
            $result_data          = self::$query_obj->field($field)->limit($list_rows)->page($page)->select()->toArray();
            $result['page_count'] = ceil($count / $list_rows);
        }
        $result['count'] = $count;
        $result['list']  = $result_data;


        self::$query_obj->removeOption();
        return $result;
    }

    /**
     * 获取分页列表数据
     * @param array $condition
     * @param bool $field
     * @param string $order
     * @param int $page
     * @param int $list_rows
     * @param string $alias
     * @param array $join
     * @param null $group
     * @param null $limit
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    final public function rawPageList($condition = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = [], $group = null, $limit = null)
    {
        if (is_array($order)) {
            self::$query_obj = Db::name($this->table)->alias($alias)->where($condition)->order($order);
            $count_obj       = Db::name($this->table)->alias($alias)->where($condition)->order($order);
        } else {
            self::$query_obj = Db::name($this->table)->alias($alias)->where($condition)->orderRaw($order);
            $count_obj       = Db::name($this->table)->alias($alias)->where($condition)->orderRaw($order);
        }

        if (!empty($join)) {
            $db_obj          = self::$query_obj;
            self::$query_obj = $this->parseJoin($db_obj, $join);
            $count_obj       = $this->parseJoin($count_obj, $join);
        }

        if (!empty($group)) {
            self::$query_obj = self::$query_obj->group($group);
            $count_obj       = $count_obj->group($group);
        }

        if (!empty($limit)) {
            self::$query_obj = self::$query_obj->limit($limit);
        }

        $count = $count_obj->count();
        if ($list_rows == 0) {
            //查询全部
            $result_data          = self::$query_obj->field($field)->limit($count)->page($page)->select()->toArray();
            $result['page_count'] = 1;
        } else {
            $result_data          = self::$query_obj->field($field)->limit($list_rows)->page($page)->select()->toArray();
            $result['page_count'] = ceil($count / $list_rows);
        }
        $result['count'] = $count;
        $result['list']  = $result_data;


        self::$query_obj->removeOption();
        return $result;
    }

    /**
     * 获取单条数据
     * @param array $where
     * @param bool $field
     * @param string $alias
     * @param null $join
     * @param null $data
     * @return array|\think\Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    final public function getInfo($where = [], $field = true, $alias = 'a', $join = null, $data = null)
    {
        if (empty($join)) {
            $result = Db::name($this->table)->where($where)->field($field)->find($data);
        } else {
            $db_obj = Db::name($this->table)->alias($alias);
            $db_obj = $this->parseJoin($db_obj, $join);
            $result = $db_obj->where($where)->field($field)->find($data);
        }

        return $result;
    }

    /**
     * join分析
     * @param $db_obj
     * @param $join
     * @return mixed
     */
    protected function parseJoin($db_obj, $join)
    {
        foreach ($join as $item) {
            list($table, $on, $type) = $item;
            $type = strtolower($type);
            switch ($type) {
                case "left":
                    $db_obj = $db_obj->leftJoin($table, $on);
                    break;
                case "inner":
                    $db_obj = $db_obj->join($table, $on);
                    break;
                case "right":
                    $db_obj = $db_obj->rightjoin($table, $on);
                    break;
                case "full":
                    $db_obj = $db_obj->fulljoin($table, $on);
                    break;
                default:
                    break;
            }
        }
        return $db_obj;
    }

    /**
     * /**
     * 获取某个列的数组
     * @param array $where 条件
     * @param string $field 字段名 多个字段用逗号分隔
     * @param string $key 索引
     * @return array
     */
    final public function getColumn($where = [], $field = '', $key = '')
    {
        return Db::name($this->table)->where($where)->column($field, $key);
    }

    /**
     * 得到某个字段的值
     * @access public
     * @param array $where 条件
     * @param string $field 字段名
     * @param mixed $default 默认值
     * @param bool $force 强制转为数字类型
     * @return mixed
     */
    final public function getValue($where = [], $field = '', $default = null, $force = false)
    {
        return Db::name($this->table)->where($where)->value($field, $default, $force)->toArray();
    }

    /**
     * 新增数据
     * @param array $data 数据
     * @param bool $is_return_pk 返回自增主键
     * @return int|string
     */
    final public function add($data = [], $is_return_pk = true)
    {
        return Db::name($this->table)->insert($data, true, $is_return_pk);
    }

    /**
     * 新增多条数据
     * @param array $data 数据
     * @param int $limit 限制插入行数
     * @return int
     */
    final public function addList($data = [], $limit = null)
    {
        return Db::name($this->table)->insertAll($data, false, $limit);
    }

    /**
     * 更新数据
     * @param array $where 条件
     * @param array $data 数据
     * @return int
     * @throws DbException
     */
    final public function update($data = [], $where = [])
    {
        return Db::name($this->table)->where($where)->update($data);
    }

    /**
     * 设置某个字段值
     * @param array $where 条件
     * @param string $field 字段
     * @param string $value 值
     * @return int
     * @throws DbException
     */
    final public function setFieldValue($where = [], $field = '', $value = '')
    {
        return $this->update([$field => $value], $where);
    }

    /**
     * 设置数据列表
     * @param array $data_list 数据
     * @param boolean $replace 是否自动识别更新和写入
     * @return mixed
     */
    final public function setList($data_list = [], $replace = false)
    {
        return Db::name($this->table)->saveAll($data_list, $replace);
    }

    /**
     * 删除数据
     * @param array $where 条件
     * @return int
     * @throws DbException
     */
    final public function delete($where = [])
    {
        return Db::name($this->table)->where($where)->delete();
    }

    /**
     * 统计数据
     * @param array $where 条件
     * @param string $type 查询类型  count:统计数量|max:获取最大值|min:获取最小值|avg:获取平均值|sum:获取总和
     * @param string $field
     * @return mixed
     */
    final public function stat($where = [], $type = 'count', $field = 'id')
    {
        return Db::name($this->table)->where($where)->$type($field);
    }

    /**
     * SQL查询
     * @param string $sql
     * @return array
     */
    final public function query($sql = '')
    {

        return Db::query($sql);
    }

    /**
     * 返回总数
     * @param array $where
     * @param string $field
     * @return int
     */
    final public function getCount($where = [], $field = '*')
    {
        return Db::name($this->table)->where($where)->count($field);
    }
    /**
     * 返回总数
     * @param array $where
     * @param string $field
     * @return int
     */
    final public function getViewCount($where = [], $field = '*', $alias = 'a', $join = null)
    {
        if(empty($join)){
            $result = Db::name($this->table)->where($where)->count($field);
        }else{
            $db_obj = Db::name($this->table)->alias($alias);
            $db_obj = $this->parseJoin($db_obj, $join);
            $result = $db_obj->where($where)->count($field);
        }
        return $result;
    }
    /**
     * 返回总数
     * @param array $where
     * @param string $field
     * @return float
     */
    final public function getSum($where = [], $field = '', $alias = 'a', $join = null)
    {
        if(empty($join)){
            $result = Db::name($this->table)->where($where)->sum($field);
        }else{
            $db_obj = Db::name($this->table)->alias($alias);
            $db_obj = $this->parseJoin($db_obj, $join);
            $result = $db_obj->where($where)->sum($field);
        }
        return $result;
    }

    /**
     * SQL执行
     * @param string $sql
     * @return int
     */
    final public function execute($sql = '')
    {
        return Db::execute($sql);
    }

    /**
     * 查询第一条数据
     * @param $condition
     * @param string $field
     * @param string $order
     * @return array|\think\Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    final function getFirstData($condition, $field = '*', $order = "")
    {
        $data = Db::name($this->table)->where($condition)->order($order)->field($field)->find();
        return $data;
    }

    /**
     * 验证
     * @param array $data
     * @param string $scene_name
     * @return array[$code, $error]
     */
    public function fieldValidate($data, $scene_name = '')
    {
        $validate = new Validate($this->rule, $this->message);

        if (empty($scene_name)) {
            $validate_result = $validate->batch(false)->check($data);
        } else {
            $validate->scene($this->scene);
            $validate_result = $validate->scene($scene_name)->batch(false)->check($data);
        }

        return $validate_result ? [true, ''] : [false, $validate->getError()];
    }

    /**
     * 事物开启
     */
    final public function startTrans()
    {

        Db::startTrans();
    }

    /**
     * 事物提交
     */
    final public function commit()
    {

        Db::commit();
    }

    /**
     * 事物回滚
     */
    final public function rollback()
    {

        Db::rollback();
    }

    /**
     * 获取错误信息
     */
    final public function getError()
    {
        return $this->error;
    }

    /**
     * 自增数据
     * @param array $where
     * @param $field
     * @param int $num
     * @return int
     * @throws DbException
     */
    final public function setInc($where = [], $field, $num = 1)
    {
        return Db::name($this->table)->where($where)->inc($field, $num)->update();
    }

    /**
     * 自减数据
     * @param array $where
     * @param $field
     * @param int $num
     * @return int
     * @throws DbException
     */
    final public function setDec($where = [], $field, $num = 1)
    {
        return Db::name($this->table)->where($where)->dec($field, $num)->update();
    }

    /**
     * 获取最大值
     * @param array $where
     * @param $field
     * @return mixed
     */
    final public function getMax($where = [], $field)
    {
        return Db::name($this->table)->where($where)->max($field);
    }
}
