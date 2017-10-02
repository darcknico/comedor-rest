<?php

namespace App\Modelos\Comedor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;
use Sofa\Eloquence\Mutable;

/**
 * @SWG\Definition(required={"fecha","cantidad"}, type="object", @SWG\Xml(name="Ticket"))
 */
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
/**
 * Numero de identificatorio del Ticket
 * @var int
 *
 * @SWG\Property(
 *   property="id",
 *   type="integer",
 *   description="Identificador unico de un ticket"
 * )
 *
 * Fecha en el cual el ticket tiene que ser validado
 * @var date
 *
 * @SWG\Property(
 *   property="fecha",
 *   type="date",
 *   description="Fecha del menu del ticket"
 * )
 *
 * Precio por el cual el ticket fue comprado
 * @var double
 *
 * @SWG\Property(
 *   property="precio",
 *   type="double",
 *   description="Precio del ticket"
 * )
 *
 * Estado en el cual se encuentra el ticket
 * @var string
 *
 * @SWG\Property(
 *   property="condicion",
 *   type="string",
 *   enum={"Activo","Cancelado","Vencido"},
 *   description="Estado del ticket"
 * )
 *
 * Codigo identificatorio unico del ticket
 * @var integer
 *
 * @SWG\Property(
 *   property="codigo",
 *   type="integer",
 *   description="Codigo del ticket",
 * )
 *
 */
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

/**
*
* Usuario de a quien pertenece el ticket
* @var object
*
* @SWG\Property(
*   property="usuario",
*   type="object",
*   description="Usuario del ticket",
*   ref="#/definitions/Usuario",
* )
*/

	public function usuario(){
			return $this->belongsTo('App\Modelos\Usuario','usu_id');
	}

/**
*
* Menu por el cual el ticket fue creado
* @var object
*
* @SWG\Property(
*   property="menu",
*   type="integer",
*   description="Menu del ticket",
*   ref="#/definitions/Menu"
* )
*
*/
	public function menu(){
			return $this->belongsTo('App\Modelos\Comedor\Menu','men_id');
	}

}
