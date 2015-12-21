<?php namespace App\Services\ServiceImpl;

use App\Favorite;
use App\Services\FavoriteService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class FavoriteServiceImpl implements FavoriteService{


    /**
     * 获取购物车记录的商品随机号
     * @param $fid 购物车id
     * */
    public function getPidByFid($fid)
    {
        // TODO: Implement getPidByFid() method.
        $favorite = DB::table("favorites")->select("pid")
                    ->where("fid",$fid)->first();
        return $favorite->pid;
    }

    /**
     * 获取购物车所有的数据
     * */
    public function getFavorites(){
        $favorites = array();
        if(self::exsitObj()){
            $favorites = DB::table("favorites")->get();
        }
//        dd($favorites);
        return $favorites;
    }

    /**
     * 获取单个购物车信息
     * @param $fid 购物车编号
     * */
    public function getFavoriteById($fid){

    }

    /**
     * 购物车分页函数
     * @param $page 页数
     * @param $perPage 每页条数
     * */
    public function getPages($page,$perPage = 10){

        //翻页的开始-结束
        $from = $page*$perPage-$perPage;
        $to = $perPage;

        //按更新时间倒序输出
        $totalData = DB::table("favorites")->orderBy("updated_at","desc")->get();
        $data['favorites'] = DB::table("favorites")->orderBy("updated_at","desc")
                        ->skip($from)->take($to)->get();//相当于limit $from,$to
        $data['products'] = Favorite::getProductList($this);//获取购物车相关联的商品表数据
        $data['page']['total'] = count($totalData);//计算数据总条数

        $availible = ceil((count($totalData)/$perPage));//计算数据可分的总页数
        $last = $page;
        //当前页页码大于1的时候，才能-1
        if($page>1)
            $last = $page-1;//计算上一页的页码
        $data['page']['last'] = $last;
        $next = $page+1;//计算下一页的页码
        if($next <= $availible)
            $data['page']['next'] = $page+1;//如果下一页的页码小于或等于可分的总页数，下一页的页码+1
        //把数据存入翻页类中
        $data['paginator'] = new Paginator($data['favorites'], count($totalData), $page);
//        dd($data);

        return $data;
    }

    /**
     * 判断表中是否存在记录
     * @return boolen
     * */
    public function exsitObj()
    {
        // TODO: Implement exsitObj() method.
        $result = DB::table('favorites')->select(DB::raw(1))->first();
        if(sizeof($result)>0) return true;
        return false;
    }

    /**
     * 根据购物车id，判断表中是否存在该记录
     * @param $fid 购物车编号
     * @return boolen
     * */
    public function exsitObjById($fid)
    {
        // TODO: Implement exsitObj() method.
        $result = DB::table('favorites')->select(DB::raw(1))->where('fid',$fid)->first();
        if(sizeof($result)>0) return true;
        return false;
    }

    /**
     * 判断表中是否存在该记录
     * @param $uid 用户id
     * @param $pid 商品随机号
     * @return boolen
     * */
    public function exsitObjByUPid($uid, $pid)
    {
        // TODO: Implement exsitObjByUPid() method.
        $result = DB::table('favorites')
                    ->select(DB::raw(1))
                    ->where('uid',$uid)
                    ->where('pid',$pid)
                    ->first();
        if(sizeof($result)>0) return true;
        return false;
    }
}