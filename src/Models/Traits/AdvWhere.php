<?php

namespace Larfree\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * 高级筛选,不耦合
 * Trait AdvWhere
 * @author blues (I@iblues.name)
 * @package Larfree\Models
 */
trait AdvWhere
{
    use OrderByRelationship;

    /**
     *
     * 同一个字段及多个字段组合查询
     * @param  Builder  $model
     * @param $request
     * @return array
     */
    public function scopeParseRequest(Builder $model, $request)
    {
        $query = $request;
//        $where = [];
        if (!$query) {
            return $model;
        }
//        $columns = $this->model->getColumns();

        foreach ($query as $key => $val) {
            //新模式
            $model->AdvWhere($key, $val);
        }

        if ($sort = Arr::get($request, '@sort', null)) {
            $sort = explode('.', $sort, 3);
            if (count($sort) > 2) {
                $model->orderByRelationship($sort[0], $sort[1], $sort[2]);
            } else {
                $model->orderBy($sort[0], $sort[1]);
            }
        } else {
            $model->orderBy('id', 'desc');
        }

        return $model;
    }

    /**
     * 多种筛选方式,具体参考doc/url.md
     * name=$%123%
     * name=>|123,<123
     * name=>$123|<123     >123 or <123
     * name=$[1,2,3]
     * name=![1,2,3]
     * user.name=![1,2,3]
     * @param $model
     * @param $key
     * @param $val
     * @return mixed
     * @throws \Larfree\Exceptions\ApiException
     */
    public function scopeAdvWhere(&$model, $key, $val)
    {
        $mode_array = ['|', '$', '!'];

        $eq_array = ['>', '>=', '<', '<='];

        if (is_null($val) || $val==='') {
            return $model;
        }

        //由于之前是key$=name的形式.这里变更下
        $mode = mb_substr($val, 0, 1, 'utf-8');
        //如果在匹配的mode里
        if (!in_array($mode, $mode_array)) {
            //如果直接存在这个字段.(不带$和|)那就直接相等
            $columns = $this->getColumns();
            if (in_array($key, $columns)) {
                return $model->where($key, $val);
            } else {
                return $model;
            }
        }


        //真实的key名字
        $real_key = $key;
        $key      = $key.$mode;
        $val      = substr($val, 1);//处理为之前的模式

//        //如果字段中存在| 代表多字段.就or的关系
        if (stripos($key, '|') !== false && stripos($key, '|') != strlen($key) - 1) {
            if (!in_array($mode, $mode_array)) {
                apiError('复杂筛选模式必须$,|,!开头,如id|title');
            }
            $multi = explode('|', $real_key);

            $model->where(function ($query) use ($val, $mode, $multi) {
                foreach ($multi as $k) {
                    $query->orWhere(function ($query) use ($k, $val, $mode) {
                        $query->advWhere($k, $mode.$val, $query);
                    });
                }
            });

            return $model;
        }


        //如果存在点.说明是链表的 进行链表处理
        if (stripos($real_key, '.')) {
            $explode = explode('.', $real_key, 2);
            $model->whereHas($explode[0], function ($query) use ($explode, $val) {
                $query->advWhere($explode[1], $val);
            });
            return $model;
        }


        //&user:name$=123&user:id|=1
        if (stripos($key, ':') !== false) {
            if (!in_array($mode, $mode_array)) {
                apiError('复杂筛选模式必须,|,!结尾,如id|title');
            }
            $multi = explode(':', $real_key);
            if ($mode == '|') {
                $model->orWhereHas($multi[0], function ($query) use ($multi, $val, $mode) {
                    $query->advWhere($multi[1], $mode.$val, $query);
                });
            } elseif ($mode == '$') {
                $model->whereHas($multi[0], function ($query) use ($multi, $val, $mode) {
                    $query->advWhere($multi[1], $mode.$val, $query);
                });
            } elseif ($mode == '!') {
                $model->whereDoesntHave($multi[0], function ($query) use ($multi, $val, $mode) {
                    $query->advWhere($multi[1], $mode.$val, $query);
                });
            }
            return $model;
        }


        switch ($mode) {
            /**
             * $结尾
             * AND模式.
             */
            case '$':
                //id$=>1,<3, 但是要排除[1,2,3];
                if (stripos($val, ',') !== false && $val[0] != '[') {
                    $multi = explode(',', $val);
                    $model->Where(function ($query) use ($real_key, $multi, $val, $mode) {
                        foreach ($multi as $k) {
                            $query->advWhere($real_key, '$'.$k);
                        }
                    });
                    return $model;
                }

                //id$=>1|<3
                if (stripos($val, '|') !== false) {
                    $multi = explode('|', $val);
                    $model->Where(function ($query) use ($real_key, $multi, $val, $mode) {
                        foreach ($multi as $k) {
                            $query->advWhere($real_key, '|'.$k);
                        }
                    });
                    return $model;
                }

                //name$=%key%
                if (stripos($val, '%') !== false) {
                    $model->where($real_key, 'like', $val);
                    return $model;
                }
                //name$=>1 name$=<=1
                if (in_array(substr($val, 0, 2), $eq_array)) {
                    $model->where($real_key, substr($val, 0, 2), substr($val, 2));
                    return $model;
                }

                if (in_array(substr($val, 0, 1), $eq_array)) {
                    $model->where($real_key, substr($val, 0, 1), substr($val, 1));
                    return $model;
                }


                // 下面的分支需要考虑链表情况
                $schemas = $model->getModel()->getSchemas();
                $schemas = $schemas[$real_key];

                // name$=[1,2,3]  等于in
                if (substr($val, 0, 1) == '[' && substr($val, -1) == ']') {
                    //把[1,2,3]处理成数组
                    $val = explode(',', substr($val, 1, -1));

                    //当有链表的时候
                    $schemas = $model->getModel()->getSchemas();
                    //belongsTo意外的链表要处理
                    if (isset($schemas['link']) && $schemas['link']['model'][0] !== 'belongsTo') {
                        $model->whereHas($schemas['link']['as'] ?? $real_key, function ($model) use ($val) {
                            $model->whereIn('id', $val);
                        });
                    } else {
                        $model->whereIn($real_key, $val);
                    }
                    return $model;
                }
                // 如果有link 链表查下
                // belongsTo意外的链表要处理
                if (isset($schemas['link']) && $schemas['link']['model'][0] !== 'belongsTo') {
                    $model->whereHas($schemas['link']['as'] ?? $real_key, function ($model) use ($val) {
                        $model->where('id', $val);
                    });
                } else {
                    $model->where($real_key, $val);
                }

                return $model;


            /**
             * |结尾
             * Or模式
             */
            case '|':
                //id|=>1,<3, 但是要排除[1,2,3];
                if (stripos($val, ',') !== false && $val[0] != '[') {
                    $multi = explode(',', $val);
                    $model->orWhere(function ($query) use ($real_key, $multi, $val, $mode) {
                        foreach ($multi as $k) {
                            $query->advWhere($real_key, '$'.$k);
                        }
                    });
                    return $model;
                }

                //id|=>1|<3,[1,2,3];
                if (stripos($val, '|') !== false) {
                    $multi = explode('|', $val);
                    $model->orWhere(function ($query) use ($real_key, $multi, $val, $mode) {
                        foreach ($multi as $k) {
                            $query->advWhere($real_key, '|'.$k);
                        }
                    });
                    return $model;
                }

                if (stripos($val, '%') !== false) {
                    $model->orWhere($real_key, 'like', $val);
                    return $model;
                }

                //name|=<=1
                if (in_array(substr($val, 0, 2), $eq_array)) {
                    $model->orWhere($real_key, substr($val, 0, 2), substr($val, 2));
                    return $model;
                }

                //name|=>1
                if (in_array(substr($val, 0, 1), $eq_array)) {
                    $model->orWhere($real_key, substr($val, 0, 1), substr($val, 1));
                    return $model;
                }

                //name|=[1,2,3]  等于in
                if (substr($val, 0, 1) == '[' && substr($val, -1) == ']') {
                    $model = $model->orWhereIn($real_key, explode(',', substr($val, 1, -1)));
                    return $model;
                }

                //name$=123
                $model->orWhere($real_key, $val);
                break;

            /**
             * !结尾
             * 不匹配模式
             */
            case '!':
                //name!=%123%
                if (stripos($val, '%') !== false) {
                    $model->where($real_key, 'not like', $val);
                    return $model;
                }
                //name!=[1,2,3]  not in
                if (substr($val, 0, 1) == '[' && substr($val, -1) == ']') {
                    $model->WhereNotIn($real_key, explode(',', substr($val, 1, -1)));
                    return $model;
                }
                //name!=123
                $model->where($real_key, '!=', $val);
                return $model;
        }
        return $model;
    }
}
