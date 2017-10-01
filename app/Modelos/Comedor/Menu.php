<?php

namespace App\Modelos\Comedor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;
use Sofa\Eloquence\Mutable;

/**
 * @SWG\Definition(required={"fecha","cantidad"}, type="object", @SWG\Xml(name="Menu"))
 */
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
/**
 * Numero de identificatorio del menu
 * @var int
 *
 * @SWG\Property(
 *   property="id",
 *   type="integer",
 *   description="Identificador unico del menu"
 * )
 *
 * Fecha en el cual el menu sera servido
 * @var date
 *
 * @SWG\Property(
 *   property="fecha",
 *   type="date",
 *   description="Fecha del menu"
 * )
 *
 * Reseña puesta para ser usado como descripcion del menu
 * @var string
 *
 * @SWG\Property(
 *   property="descripcion",
 *   type="string",
 *   description="Descripcion del menu"
 * )
 *
 * Valor para comprar un ticket para tal menu
 * @var double
 *
 * @SWG\Property(
 *   property="precio",
 *   type="double",
 *   description="Precio del menu"
 * )
 *
 * Cantidad total de tickets que estan disponibles para la venta de este menu
 * @var integer
 *
 * @SWG\Property(
 *   property="cantidad",
 *   type="integer",
 *   description="Cantidad de tickets"
 * )
 *
 * Estado en el cual el menu se encuentra
 * @var boolean
 *
 * @SWG\Property(
 *   property="finalizado",
 *   type="boolean",
 *   description="Estado del menu"
 * )
 *
 * Cantidad de tickets comprados en el actual menu
 * @var integer
 *
 * @SWG\Property(
 *   property="comprados",
 *   type="integer",
 *   description="Cantidad de tickets vendidos",
 * )
 *
 * Cantidad de tickets restantes en el actual menu, que estan a la venta
 * @var integer
 *
 * @SWG\Property(
 *   property="restantes",
 *   type="integer",
 *   description="Cantidad de tickets disponibles que pueden venderse",
 * )
 *
 * Cantidad de tickets validados en el dia del menu
 * @var integer
 *
 * @SWG\Property(
 *   property="validados",
 *   type="integer",
 *   description="Tickets validados",
 * )
 *
 */
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

/**
*
* Listado de tickets que pretenecen al menu de la fecha
* @var object
*
* @SWG\Property(
*   property="tickets",
*   type="array",
*   description="Tickets comprados",
*   @SWG\Items(
*       type="object",
*
* @SWG\Property(
*   property="id",
*   type="integer",
*   description="Identificador unico de un ticket"
* ),
*
* @SWG\Property(
*   property="fecha",
*   type="string",
*   description="Fecha del menu del ticket"
* ),
*
* @SWG\Property(
*   property="precio",
*   type="string",
*   description="Precio del ticket"
* ),
*
* @SWG\Property(
*   property="condicion",
*   type="string",
*   enum={"Activo","Cancelado","Vencido"},
*   description="Estado del ticket"
* ),
*
* @SWG\Property(
*   property="codigo",
*   type="integer",
*   description="Codigo del ticket",
* ),
*
*   ),
* )
*
*/
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
