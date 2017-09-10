<?php

namespace App\Modelos\Almacen;

use Illuminate\Database\Eloquent\Model;

class Local extends Model{
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


	protected $table = 'gpslocales';
	protected $primaryKey = 'idLocal';

	protected $fillable = [
		'idUsuario',
    'nombre',
    'direccion',
    'latitud',
    'longitud'
	];

    public function usuario()
    {
        return $this->belongsTo('App\Modelos\Usuario','idUsuario');
    }

    public function ofertas(){
        return $this->hasMany('App\Modelos\Almacen\Oferta','idLocal','idLocal');
    }
}
