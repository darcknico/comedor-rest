<?php

namespace App\Modelos\Almacen;

use Illuminate\Database\Eloquent\Model;

class Oferta extends Model{
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


	protected $table = 'gpsofertas';
	protected $primaryKey = 'idOferta';

	protected $fillable = [
		'idLocal',
    'idProducto',
    'precio',
    'vencimiento'
	];

    public function local()
    {
        return $this->belongsTo('App\Modelos\Almacen\Local','idLocal');
    }

    public function producto()
    {
        return $this->belongsTo('App\Modelos\Almacen\Producto','idProducto');
    }

}
