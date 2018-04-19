<?php

namespace model\shop;
use think\facade\Request;

class BaseModel extends \Illuminate\Database\Eloquent\Model
{

    public function scopeWithOnly($query, $relation, Array $columns)
    {
        return $query->with([$relation => function ($query) use ($columns) {
            $query->select(array_merge(['id'], $columns));
        }]);
    }

    protected function get_Transaction_obj()
    {
        return parent::$resolver->connection();///返回连接的实例
    }

    protected function paginat($perpage){
        dd('da');
        $page=Request::post('page');
        return  parent::paginate($perpage, $columns = ['*'], $pageName = 'page', $page);
    }
}