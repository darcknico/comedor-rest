<?php

namespace App\Modelos\Comedor;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;
use Sofa\Eloquence\Mutable;

class Transaccion extends Model{
	/**
     * The name of the "created at" column.
     *
     * @var string
     */
  const CREATED_AT = 'creado';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
  const UPDATED_AT = 'modificado';

	//protected $table = 'tickets';
	protected $primaryKey = 'tra_id';
	protected $table = 'tbl_transacciones';
  protected $dateFormat = 'Y-m-d';
  use Eloquence, Mappable, Mutable;

  protected $maps = [
    'id' => 'tra_id',
		'concepto' => 'tra_concepto',
		'token' => 'tra_token',
		'monto' => 'tra_monto',
    'estado_transaccion' => 'tra_estado',
    'fecha_acreditacion' => 'tra_fecha',
  ];

  protected $appends  = [
    'id',
		'concepto',
		'token',
		'monto',
    'estado_transaccion',
    'fecha_acreditacion',
  ];

  protected $hidden = [
    'tra_id',
    'usu_id',
		'tra_concepto',
		'tra_token',
    'tra_monto',
		'tra_estado',
    'tra_fecha',
    'estado',
    'modificado',
  ];

	protected $fillable = [
		'usu_id',
		'tra_concepto',
		'tra_token',
    'tra_monto',
    'tra_fecha',
    'tra_estado',
    'paymentMethodId',
    'cardIssuerId',
    'installment',
    'cardToken',
    'campaignId',
	];

	public function usuario(){
			return $this->belongsTo('App\Modelos\Usuario','usu_id');
	}

}
