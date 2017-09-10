<?php

namespace App\Modelos\Comedor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;
use Sofa\Eloquence\Mutable;

class Menu extends Model{
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
  protected $primaryKey = 'men_id';

	//protected $table = 'menus';
	protected $table = 'tbl_menus';
  use Eloquence, Mappable, Mutable;

  protected $maps = [
    'id' => 'men_id',
    'fecha' => 'men_fecha',
		'descripcion' => 'men_descripcion',
		'precio' => 'men_precio',
		'cantidad' => 'men_cantidad',
    'finalizado' => 'men_finalizado',
    'comprados' => 'men_comprados',
    'restantes' => 'men_restantes',
    'validados' => 'men_validados'
  ];

  protected $appends  = [
    'id',
    'fecha',
		'descripcion',
		'precio',
		'cantidad',
    'finalizado',
    'comprados',
    'restantes',
    'validados',
  ];

  protected $hidden = [
    'men_id',
    'men_fecha',
		'men_descripcion',
		'men_precio',
    'men_cantidad',
		'men_finalizado',
    'men_comprados',
    'men_restantes',
    'modificado',
    'creado',
    'estado',
    'men_validados',
  ];

	protected $fillable = [
		'men_fecha',
		'men_descripcion',
		'men_precio',
		'men_cantidad',
		'men_finalizado',
    'men_validados',
	];

  public function tickets(){
        return $this->hasMany('App\Modelos\Comedor\Ticket','men_id','men_id');
    }

		protected static function boot(){
		  parent::boot();
		  // Order by name ASC
		  static::addGlobalScope('order', function (Builder $builder) {
		      $builder->orderBy('men_fecha', 'desc');
		  });
		}

}
