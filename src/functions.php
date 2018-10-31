<?php
/**
 * 数组获取缩略图
 * @param $filename
 * @param $width
 * @param $height
 * @param int $mode
 * @return mixed
 */
function getArrayThumb($array, $width, $height, $mode = 0)
{
    if (is_array($array)) {
        foreach ($array as $k => $v) {
            $array[$k] = getThumb($v, $width, $height, $mode);
        }
    } else {
        $array = getThumb($array, $width, $height, $mode);
    }
    return $array;
}

/**
 * 获取配置
 * @param $cat 分类名
 * @param $name 具体名
 */
function conf($cat, $name = '')
{
    if (!$name) {
        $data = \App\Models\Config::select(['key', 'value'])->where('cat', $cat)->get();
        $data = $data->toArray();
        return array_pluck($data, 'value', 'key');
    } else {
        $data = \App\Models\Config::select(['key', 'value'])->where('cat', $cat)->where('key', $name)->first();
        return $data->value;
    }
}

/**
 * 用来处理
 * @param $str
 * @param bool $mode
 * @return mixed
 */
function larfree_json_decode($str, $mode = 1)
{
//    if(preg_match('/(\w)+:/', $str)){
//        $str = preg_replace('/(\w+):/is', '"$1":', $str);
//    }
//    if(preg_match('/:\'(\w)+\'/', $str)){
//        $str = preg_replace('/:\'(\w+)\'/is', ':"$1"', $str);
//    }
    return json_decode($str, $mode);
}

/**
 * 下划线转驼峰
 * @param $str
 * @param bool $ucfirst
 * @return string
 */
function lineToHump($str, $ucfirst = true)
{
    while (($pos = strpos($str, '_')) !== false)
        $str = substr($str, 0, $pos) . ucfirst(substr($str, $pos + 1));

    return $ucfirst ? ucfirst($str) : $str;
}

/**
 * 驼峰转下划线
 * @param $str
 * @param bool $ucfirst
 * @return string
 */
function humpToLine($str, $separator = '_')
{
    $str = lcfirst($str);
    $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
        return '_' . strtolower($matches[0]);
    }, $str);
    return $str;
}

/**
 * 从命名空间中提取当前类名
 * @param $class
 * @return string
 */
function getClassName($class)
{
    return class_basename($class);
}

/**
 * 获取用户id,也可以指定
 * @param string $uid
 * @return mixed
 */
function getLoginUserID($uid = '')
{
    static $id;
    if ($uid) {
        $id = $uid;
    }
    if ($id) {
        return $id;
    } elseif (true) {
        $user = \Auth::guard('api')->user();
        if (!$user)
            return @$_ENV['DEF_USER'];
        return $user->id;
    } else {
        return $_ENV['DEF_USER'];
    }
}

/**
 * api中报错,跑出异常
 * @param string $msg
 * @param array $data
 * @param int $code
 * @throws \Larfree\Exceptions\ApiException
 */
function apiError($msg = '', $data = [], $code = 412)
{
    throw new Larfree\Exceptions\ApiException($msg, $data, $code);
}


/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function listToTree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 获取缩略图
 * @param $filename
 * @param $width
 * @param $height
 * @param int $mode
 * @return mixed
 */
function getThumb($filename, $width, $height, $mode = 0)
{
    if (!$filename)
        return '';
    $type = config('UPLOAD_TYPE','file');
    switch ($type) {
        case 'file':
            return env('APP_URL') . '/' . $filename . "?imageView2/{$mode}/w/{$width}/h/{$height}";
            break;
        default:
            $disk = \Storage::disk('qiniu'); //使用七牛云上传
            if ($mode == '-1') {
                return $disk->downloadUrl($filename)->__toString();//裁剪
            } else {
                return $disk->imagePreviewUrl($filename, "imageView2/{$mode}/w/{$width}/h/{$height}")->__toString();//裁剪
            }
    }
}

/**
 * 类似dd
 * @param $data
 */
function pd($data)
{
    print_r($data);
    exit();
}

/**
 * 递归调目录文件
 * @param $dir
 * @return array
 */
function dirToArray($dir)
{
    $result = array();
    $cdir = scandir($dir);
    foreach ($cdir as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
            } else {
                $result[] = $value;
            }
        }
    }
    return $result;
}

/**
 * 多维数组的合并
 * 详情可参看functionTest用例
 * @param $array1
 * @param $array2
 * @return array
 */
function array_merges(array $array1, $array2)
{
    foreach ($array1 as $key => $var) {

        if (is_array($var)) {
            //如果有值,并且不是null
            if (isset($array2[$key]) && !is_null($array2[$key])) {
                $array2[$key] = array_merges($var, $array2[$key]);
                //新的数组没有,就用默认的
            } elseif (!isset($array2[$key])) {
                //isset和null分不开,只能这样
                //新的有,又是null,都消除
                if (in_array($key, array_keys($array2))) {
                    unset($array1[$key]);
                    unset($array2[$key]);
                } else {
                    //不在新数组中,用原来的
                    $array2[$key] = $var;
                }
            } else {
                //其他情况用默认
                $array2[$key] = $var;
            }
        }
    }
    return array_merge($array1, $array2);
}

if( !function_exists('config_path')){
    function config_path(){
        return dirname(__FILE__).'/Copy/config';
    }
}