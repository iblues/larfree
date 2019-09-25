<?php

namespace Larfree\Models;

/**
 * 高级筛选,不耦合
 * Trait AdvWhere
 * @author blues (I@iblues.name)
 * @package Larfree\Models
 */
trait AdvWhere{
    /**
     * 多种筛选方式,具体参考doc/url.md
     * name=$%123%
     * name=>|123,<123
     * name=>$123|<123     >123 or <123
     * name=$[1,2,3]
     * name=![1,2,3]
     * @param $model
     * @param $key
     * @param $val
     * @return mixed
     * @throws \Larfree\Exceptions\ApiException
     */
    public function scopeAdvWhere(&$model,$key,$val){

        $mode_array = ['|','$','!'];

        $eq_array=['>','>=','<','<='];

        if(!$val)
            return $model;

        //由于之前是key$=name的形式.这里变更下
        $mode = mb_substr($val,0,1,'utf-8');
        //如果在匹配的mode里
        if(!in_array($mode,$mode_array)){
            //如果直接存在这个字段.(不带$和|)那就直接相等
            $columns = $this->getColumns();
            if(in_array($key,$columns)) {
                return $model->where($key,$val);
            }else{
                return $model;
            }
        }


        //真实的key名字
        $real_key = $key;
        $key = $key.$mode;
        $val = substr($val,1);//处理为之前的模式

//        //如果字段中存在| 代表多字段.就or的关系
        if(stripos($key,'|')!==false && stripos($key,'|')!= strlen($key)-1){
            if(!in_array($mode,$mode_array))
                apiError('复杂筛选模式必须$,|,!开头,如id|title$');
            $multi = explode('|',$real_key);

            $model->where(function ($query)use($val,$mode,$multi){
                foreach($multi as $k){
                    $query->orWhere(function($query)use($k,$val,$mode){
                        $query->advWhere($k,$mode.$val,$query);
                    });
                }
            });

            return $model;
        }

        //&user:name$=123&user:id|=1
        if(stripos($key,':')!==false){
            if(!in_array($mode,$mode_array))
                apiError('复杂筛选模式必须$,|,!结尾,如id|title$');
            $multi = explode(':',$real_key);
            if($mode=='|'){
                $model->orWhereHas($multi[0],function($query)use($multi,$val,$mode){
                    $query->advWhere($multi[1],$mode.$val,$query);
                });
            }elseif($mode=='$'){
                $model->whereHas($multi[0],function($query)use($multi,$val,$mode){
                    $query->advWhere($multi[1],$mode.$val,$query);
                });
            }elseif($mode=='!'){
                $model->whereDoesntHave($multi[0],function($query)use($multi,$val,$mode){
                    $query->advWhere($multi[1],$mode.$val,$query);
                });
            }
            return $model;
        }



        switch ( $mode ){
            /**
             * $结尾
             * AND模式.
             */
            case '$':
                //id$=>1,<3, 但是要排除[1,2,3];
                if(stripos($val,',')!==false && $val[0]!='['){
                    $multi = explode(',',$val);
                    $model->Where(function($query)use($real_key,$multi,$val,$mode){
                        foreach($multi as $k){
                            $query->advWhere($real_key,'$'.$k);
                        }
                    });
                    return $model;
                }

                //id$=>1|<3
                if(stripos($val,'|')!==false){
                    $multi = explode('|',$val);
                    $model->Where(function($query)use($real_key,$multi,$val,$mode){
                        foreach($multi as $k){
                            $query->advWhere($real_key,'|'.$k);
                        }
                    });
                    return $model;
                }

                //name$=%key%
                if(stripos($val,'%')!==false){
                    $model->where($real_key,'like',$val);
                    return $model;
                }
                //name$=>1 name$=<=1
                if(in_array(substr($val,0,2),$eq_array)) {
                    $model->where($real_key, substr($val, 0, 2), substr($val, 2));
                    return $model;
                }

                if(in_array(substr($val,0,1),$eq_array)){
                    $model->where($real_key,substr($val,0,1),substr($val,1));
                    return $model;
                }

                //name$=[1,2,3]  等于in
                if(substr($val,0,1)=='[' && substr($val,-1)==']'){
                    $model->whereIn($real_key,explode(',',substr($val,1,-1)));
                    return $model;
                }



                $model->where($real_key,$val);
                return $model;


            /**
             * |结尾
             * Or模式
             */
            case '|':
                //id|=>1,<3, 但是要排除[1,2,3];
                if(stripos($val,',')!==false && $val[0]!='['){
                    $multi = explode(',',$val);
                    $model->orWhere(function($query)use($real_key,$multi,$val,$mode){
                        foreach($multi as $k){
                            $query->advWhere($real_key,'$'.$k);
                        }
                    });
                    return $model;
                }

                //id|=>1|<3,[1,2,3];
                if(stripos($val,'|')!==false){
                    $multi = explode('|',$val);
                    $model->orWhere(function($query)use($real_key,$multi,$val,$mode){
                        foreach($multi as $k){
                            $query->advWhere($real_key,'|'.$k);
                        }
                    });
                    return $model;
                }

                if(stripos($val,'%')!==false){
                    $model->orWhere($real_key,'like',$val);
                    return $model;
                }

                //name|=<=1
                if(in_array(substr($val,0,2),$eq_array)) {
                    $model->orWhere($real_key, substr($val, 0, 2), substr($val, 2));
                    return $model;
                }

                //name|=>1
                if(in_array(substr($val,0,1),$eq_array)) {
                    $model->orWhere($real_key, substr($val, 0, 1), substr($val, 1));
                    return $model;
                }

                //name|=[1,2,3]  等于in
                if(substr($val,0,1)=='[' && substr($val,-1)==']'){
                    $model = $model->orWhereIn($real_key,explode(',',substr($val,1,-1)));
                    return $model;
                }

                //name$=123
                $model->orWhere($real_key,$val);
                break;

            /**
             * !结尾
             * 不匹配模式
             */
            case '!':
                //name!=%123%
                if(stripos($val,'%')!==false){
                    $model->where($real_key,'not like',$val);
                    return $model;
                }
                //name!=[1,2,3]  not in
                if(substr($val,0,1)=='[' && substr($val,-1)==']'){
                    $model->WhereNotIn($real_key,explode(',',substr($val,1,-1)));
                    return $model;
                }
                //name!=123
                $model->where($real_key,'!=',$val);
                return $model;
        }
        return $model;
    }
}
