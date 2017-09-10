<?php

namespace App\Modelos\Comedor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;
use Sofa\Eloquence\Mutable;

class Ticket extends Model{
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
	protected $primaryKey = 'tic_id';
	protected $table = 'tbl_tickets';
  use Eloquence, Mappable, Mutable;

  protected $maps = [
    'id' => 'tic_id',
    'precio' => 'tic_precio',
		'condicion' => 'tic_estado',
		'codigo' => 'tic_codigo',
    'idMenu' => 'men_id',
    'idUsuario' => 'usu_id',
    'fecha' => 'tic_fecha',
  ];

  protected $appends  = [
    'id',
    'precio',
		'condicion',
		'codigo',
    'idMenu',
    'idUsuario',
    'fecha',
  ];

  protected $hidden = [
    'tic_id',
    'tic_precio',
		'tic_estado',
		'tic_codigo',
    'men_id',
		'usu_id',
    'estado',
    'creado',
    'modificado',
    'tic_fecha',
  ];

	protected $fillable = [
		'men_id',
		'usu_id',
		'tic_precio',
		'tic_estado',
    'tic_fecha',
		'tic_codigo',
	];

	public function usuario(){
			return $this->belongsTo('App\Modelos\Usuario','usu_id');
	}

	public function menu(){
			return $this->belongsTo('App\Modelos\Comedor\Menu','men_id');
	}

}
