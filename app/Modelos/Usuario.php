<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;
use Sofa\Eloquence\Mutable;

/**
 * @SWG\Definition(required={"menu", "contraseña","nombre"}, type="object", @SWG\Xml(name="Usuario"))
 */
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
	protected $primaryKey = 'usu_id';

  use Eloquence, Mappable, Mutable;

  protected $maps = [
    'id' => 'usu_id',
    'dni' => 'usu_dni',
		'nombre' => 'usu_nombre',
		'apellido' => 'usu_apellido',
    'saldo' => 'usu_saldo',
    'tickets' => 'usu_tickets',
    'condicion' => 'usu_estado',
    'imagen' => 'usu_image',
  ];

/**
 * Numero de documento del Usuario
 * @var int
 *
 * @SWG\Property(
 *   property="dni",
 *   type="integer",
 *   example="35048026",
 *   description="Identificador unico del usuario"
 * )
 *
 * Password o Contraseña usada para autenticarcion
 * @var string
 *
 * @SWG\Property(
 *   property="contraseña",
 *   type="string",
 *   description="Contraseña del usuario"
 * )
 *
 * Nombre Completo del usuario
 * @var string
 *
 * @SWG\Property(
 *   property="nombre",
 *   type="string",
 *   description="Nombre del usuario"
 * )
 * Apellido Completo del usuario
 * @var string
 *
 * @SWG\Property(
 *   property="apellido",
 *   type="string",
 *   description="Nombre del usuario"
 * )
 */
  protected $appends  = [
    'id',
    'dni',
		'nombre',
		'apellido',
    'saldo',
    'tickets',
    'condicion',
    'imagen',
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
    'usu_image',
  ];

	protected $fillable = [
		'usu_dni',
		'usu_nombre',
		'usu_apellido',
    'usu_contraseña',
		'usu_token',
    'usu_image',
    'estado'
	];

  protected $setterMutators = [
      'usu_nombre' => 'strtolower',
      'usu_apellido' => 'strtolower',
  ];
  protected $getterMutators = [
      'usu_nombre' => 'strtolower|ucwords',
      'usu_apellido' => 'strtolower|ucwords',
  ];

  public function tipoUsuario(){
			return $this->belongsTo('App\Modelos\Usuario','tus_id');
	}
}
