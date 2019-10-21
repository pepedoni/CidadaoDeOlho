<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class Deputado extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
       'id', 'nome', 'situacao', 'localizacao'
   ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'VALOR_DESPESA' => 'decimal:2',
        'VALOR_REEMBOLSO' => 'decimal:2'
    ];

   public $timestamps = false;
   
   /*
   * Retorna a lista de deputados ordenados por verba indenizatoria em um mês
   * 
   * @param $mes
   **/
   public static function getDeputadosOrdenadosPorVerba($mes, $page, $size) {
       $sql = "SELECT D.ID,
                D.NOME,
                CASE 
                	WHEN SITUACAO = 1 THEN 'Em Exercício' 
                    WHEN SITUACAO = 2 THEN 'Exerceu Mandato'
                    WHEN SITUACAO = 3 THEN 'Renunciou' 
                    WHEN SITUACAO = 4 THEN 'Afastado'
                    WHEN SITUACAO = 5 THEN 'Perdeu o Mandato'
                END AS SITUACAO,
                CAST(SUM(VALOR_DESPESA) AS DEC(15,2)) AS VALOR_DESPESA,
                CAST(SUM(VALOR_REEMBOLSO) AS DEC(15,2)) AS VALOR_REEMBOLSO
            FROM VERBAS_INDENIZATORIAS V, DEPUTADOS D
            WHERE MONTH(DATA_REFERENCIA) = ? 
            AND D.ID = V.DEPUTADO_ID
            GROUP BY  D.ID, D.NOME, D.SITUACAO
            ORDER BY  VALOR_REEMBOLSO DESC, VALOR_DESPESA DESC";
        
        $data = DB::select($sql, [$mes]);

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

