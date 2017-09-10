<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;
use Sofa\Eloquence\Mutable;

class Usuario extends Model{
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

	protected $table = 'tbl_usuarios';
	//protected $primaryKey = 'idusuario';
	protected $primaryKey = 'usu_id';

  use Eloquence, Mappable, Mutable;

  protected $maps = [
    'id' => 'usu_id',
    'dni' => 'usu_dni',
		'nombre' => 'usu_nombre',
		'apellido' => 'usu_apellido',
		'token' => 'usu_token',
    'saldo' => 'usu_saldo',
    'tickets' => 'usu_tickets',
    'condicion' => 'usu_estado',
  ];

  protected $appends  = [
    'id',
    'dni',
		'nombre',
		'apellido',
		'token',
    'saldo',
    'tickets',
    'condicion',
  ];

  public function getUsuSaldoAttribute($value)
  {
    return $value?$value:0;
  }

  public function getUsuTicketsAttribute($value)
  {
    return $value?$value:0;
  }

  public function getContraseña(){
    return $this->attributes['usu_contraseña'];
  }

  protected $hidden = [
    'usu_id',
    'usu_dni',
		'usu_nombre',
		'usu_apellido',
    'usu_contraseña',
		'usu_token',
    'modificado',
    'creado',
    'estado',
    'tus_id',
    'usu_saldo',
    'usu_tickets',
    'usu_estado',
  ];

	protected $fillable = [
		'usu_dni',
		'usu_nombre',
		'usu_apellido',
    'usu_contraseña',
		'usu_token'
	];

  protected $setterMutators = [
        'usu_nombre' => 'strtolower',
        'usu_apellido' => 'strtolower',
    ];
  protected $getterMutators = [
        'usu_nombre' => 'strtolower|ucwords',
        'usu_apellido' => 'strtolower|ucwords',
    ];


	public function productos(){
			return $this->hasMany('App\Modelos\Almacen\Producto','usu_id','usu_id');
			//return $this->hasMany('App\Models\ListaCompraProducto','idusuario','idusuarios');
		}
	public function locales(){
			return $this->hasMany('App\Modelos\Almacen\Producto','usu_id','usu_id');
			//return $this->hasMany('App\Models\ListaCompraProducto','idusuario','idusuarios');
		}
}
