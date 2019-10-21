<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerbasIndenizatoria extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        "deputado_id", "descricao", "emitente", "data_referencia", "data_emissao", "documento", "valor_reembolso", "valor_despesa"    
    ];
    
    public $timestamps = false;
}
