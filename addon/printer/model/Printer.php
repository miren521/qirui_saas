<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace addon\printer\model;

use app\model\BaseModel;
use addon\printer\data\sdk\yilianyun\api\PrinterService;
use addon\printer\data\sdk\yilianyun\config\YlyConfig;
use addon\printer\data\sdk\yilianyun\oauth\YlyOauthClient;
use app\model\system\Config;
use Exception;

class Printer extends BaseModel
{

    private $brand = [
//        ['brand' => '365','name' => '365'],
//        ['brand' => 'feie','name' => '飞鹅'],
        ['brand' => 'yilianyun' ,'name' => '易联云'],
    ];

    /**
     * 获取打印机品牌
     * @return array
     */
    public function getPrinterBrand()
    {
        return $this->brand;
    }

    /**
     * 添加小票打印
     * @param $data
     * @return array
     */
    public function addPrinter($data)
    {
        $data['create_time'] = time();

        model('printer')->startTrans();
        try{
            $res = model('printer')->add($data);
            //易联云
            if($data['brand'] == 'yilianyun'){
                $result = $this->addPrinterYly($data);
                if($result['code'] < 0){

                    model('printer')->rollback();
                    return $result;
                }
            }
            model('printer')->commit();
            return $this->success($res);
        }catch (Exception $e){
            model('printer')->rollback();
            return $this->error('',$e->getMessage());
        }
    }


    /**
     * 编辑小票打印
     * @param $data
     * @return array
     */
    public function editPrinter($data)
    {
        $data['update_time'] = time();

        $res = model('printer')->update($data,[ ['printer_id','=',$data['printer_id']] ]);
        return $this->success($res);
    }

    /**
     * 删除
     * @param $condition
     * @return array
     */
    public function deletePrinter($condition)
    {
        model('printer')->startTrans();
        try{

            $printer_info = model('printer')->getInfo($condition,'*');

            $res = model('printer')->delete($condition);
            //易联云
            if($printer_info['brand'] == 'yilianyun'){

                $result = $this->deletePrinterYly($printer_info);
                if($result['code'] < 0){

                    model('printer')->rollback();
                    return $result;
                }
            }
            model('printer')->commit();
            return $this->success($res);

        }catch (Exception $e){

            model('printer')->rollback();
            return $this->error('',$e->getMessage());
        }
    }

    /**
     * 获取小票打印信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getPrinterInfo($condition = [], $field = '*')
    {
        $res = model('printer')->getInfo($condition,$field);
        return $this->success($res);
    }

    /**
     * 获取小票打印列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getPrinterList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('printer')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取小票打印分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getPrinterPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('printer')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }


    /**************************************************** 打印机管理（第三方） *********************************************************/

    /******************** 易联云 start ************************/

    /**
     * 电子面单设置
     * @param $data
     * @return array
     */
    public function setYlyTokenConfig($data)
    {
        $config = new Config();
        $res = $config->setConfig($data, '易联云小票打印token', 1, [ [ 'site_id', '=', $data['site_id'] ], [ 'app_module', '=', 'shop' ], [ 'config_key', '=', 'PRINTER_YLYTOKEN' ] ]);
        return $this->success($res);
    }

    /**
     * 获取电子面单设置
     * @param $site_id
     * @return array
     */
    public function getYlyTokenConfig($site_id)
    {
        $config = new Config();
        $res = $config->getConfig([ [ 'site_id', '=', $site_id ], [ 'app_module', '=', 'shop' ], [ 'config_key', '=', 'PRINTER_YLYTOKEN' ] ]);
        if (empty($res['data']['value'])) {
            $res['data']['value'] = [

                'access_token' => '',
                'end_time' => '0'//token有效期
            ];
        }
        return $res;
    }

    /**
     * 获取易联云token
     * @param $yly_config
     * @param $site_id
     * @return mixed
     */
    public function getYlyToken($yly_config,$site_id)
    {
        //token配置
        $config_data = $this->getYlyTokenConfig($site_id);
        $config = $config_data['data']['value'];

        $client = new YlyOauthClient($yly_config);

        if($config['end_time'] == 0 || $config['end_time'] < time()){
            $token = $client->getToken();           //若是开放型应用请传授权码code
            $access_token = $token->access_token;   //调用API凭证AccessToken

            //更新token
            $expires_in = $token->expires_in;
            $end_time = strtotime('+' . $expires_in/86400 . 'day');
            $token_data = [
                'site_id' => $site_id,
                'access_token' => $token->access_token,
                'end_time' => $end_time
            ];
            $this->setYlyTokenConfig($token_data);
        }else{
            $access_token = $config['access_token'];
        }
        return $access_token;
    }

    /**
     * 添加易联云打印机授权
     * @param $param
     * @return array|mixed
     */
    public function addPrinterYly($param)
    {
        $yly_config = new YlyConfig($param['open_id'],$param['apikey']);

        $access_token = $this->getYlyToken($yly_config,$param['site_id']);
        //添加打印机
        $printer = new PrinterService($access_token, $yly_config);
        $data = $printer->addPrinter($param['printer_code'], $param['printer_key'], '', '');
        if(isset($data->error) && $data->error == 0){
            return $this->success();
        }else{
            return $data;
        }
    }

    /**
     * 删除易联云打印机授权
     * @param $param
     * @return array|mixed
     */
    public function deletePrinterYly($param)
    {

        $yly_config = new YlyConfig($param['open_id'],$param['apikey']);

        $access_token = $this->getYlyToken($yly_config,$param['site_id']);

        //添加打印机
        $printer = new PrinterService($access_token, $yly_config);
        $data = $printer->deletePrinter($param['printer_code']);
        if(isset($data->error) && $data->error == 0){
            return $this->success();
        }else{
            return $data;
        }
    }

    /******************** 易联云 end ************************/


}