<?php
//
//namespace Tests\Feature;
//
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\RefreshDatabase;
//
///**
// * 基于配置方法.先一个个测试下是否正常访问
// * Class BaseOnRouterApiTest
// * @package Tests\Feature
// */
//class BaseOnSchemasApiTest extends TestCase
//{
//    /**
//     * A basic test example.
//     *  前台验证
//     * @return void
//     */
//    public function testHome(){
//        $urlList = [
//            '/api/manager/common/pay'=>false,//支付的单独测试
//            '/api/admin/admin'=>false,
//            '/api/admin/nav'=>false,
//            '/api/configs/test'=>false,
//            '/api/configs/test_test'=>false,
//            '/api/system/api_doc'=>false,
//            '/api/system/component'=>false,
//            '/api/system/config'=>false,
//            '/api/shop/stock'=>false,
//        ];
//        $this->assert('home',$urlList);
//    }
//
//    /**
//     *后台验证
//     */
//    public function testAdmin(){
//        $urlList = [
//            '/api/manager/common/pay'=>false,//后台不需要支付
//            '/api/manager/configs/test'=>false,
//            '/api/manager/configs/test_test'=>false,
//            '/api/manager/system/api_doc'=>false,
//            '/api/manager/system/component'=>false,
//            '/api/manager/system/config'=>false,
//            '/api/manager/common/pay'=>false,//
//        ];
//        $this->assert('admin',$urlList);
//    }
//    public function assert($type='home',$urlList=''){
//        $dir = self::dirToArray(base_path('/config/Schemas/Schemas'));
//        foreach ($dir as $key=>$value){
//            foreach ($value as $v){
//                $name = explode('.',$v);
//                if($type !== 'home'){
//                    $url = '/api/manager/'.strtolower($key).'/'.lcfirst($name[0]);
//                }else{
//                    $url = '/api/'.strtolower($key).'/'.lcfirst($name[0]);
//                }
//                $url = humpToLine($url);
//                //判断$url存在才执行验证操作,如果不存在就不执行验证操作
//                if(@$urlList[$url]){
//                    $url = $urlList[$url];
//                }elseif(isset($urlList[$url]) && $urlList[$url]==false){
//                    continue;
//                }
//                echo $url."验证中\r\n";
//                $response = $this->get($url);
//                $this->assertStatus($response,200,$url.',返回状态不符合');
//            }
//        }
//    }
//    protected function assertStatus($response,$code,$msg){
//        $actual = $response->getStatusCode();
//        $this->assertEquals($code,$actual,$msg);
//    }
//
//    static function dirToArray($dir) {
//        $result = array();
//        $cdir = scandir($dir);
//        foreach ($cdir as $key => $value) {
//            if (!in_array($value,array(".",".."))) {
//                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
//                    $result[$value] = self::dirToArray($dir . DIRECTORY_SEPARATOR . $value);
//                }
//                else {
//                    $result[] = $value;
//                }
//            }
//        }
//        return $result;
//    }
//
//
//}
