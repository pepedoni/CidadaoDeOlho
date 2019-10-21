<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class RedeSocialDeputado extends Model
{
   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        "deputado_id", "rede_id", "link", "nome"
    ];

    public $timestamps = false;

    /*
   * Retorna a lista de deputados ordenados por verba indenizatoria em um mês
   * 
   * @param $mes
   **/
   public static function getRedesSociaisMaisUtilizadas($page, $size) {
    $sql = "SELECT COUNT(*) AS QT_DEPUTADOS, NOME 
            FROM REDE_SOCIAL_DEPUTADOS 
            GROUP BY REDE_ID, NOME 
            ORDER BY COUNT(*) DESC";
     
     $data = DB::select($sql, []);

     $collect = collect($data);

     $paginationData = new LengthAwarePaginator(
                              $collect->forPage($page, $size),
                              $collect->count(), 
                              $size, 
                              $page
                            );

     return $paginationData;
}
}
