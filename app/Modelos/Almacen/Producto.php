<?php

namespace App\Modelos\Almacen;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model{
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


	protected $table = 'productos';
	protected $primaryKey = 'id';

	protected $fillable = [
		'nombre',
    'descripcion',
    'idMarca'
	];

    public function marca()
    {
        return $this->belongsTo('App\Models\Marca','idMarca');
    }

    public function productoProveedores(){
        return $this->hasMany('App\Models\ProveedorProducto','idProducto','id')->orderBy('creado','desc');
    }

    public function imagenes(){
        return $this->hasMany('App\Models\ProductoImagen','idProducto','id')->orderBy('creado','desc');
    }

    public function preciosVenta(){
        return $this->hasMany('App\Models\PrecioVenta','idProducto','id')->orderBy('creado','desc');
    }

    public function preciosCompra(){
        return $this->hasMany('App\Models\PrecioCompra','idProducto','id')->orderBy('creado','desc');
    }

    public function listasVenta(){
        return $this->hasMany('App\Models\ListaVentaProducto','idProducto','id')->orderBy('creado','desc');
    }

		public function ofertas(){
        return $this->hasMany('App\Modelos\Almacen\Oferta','idProducto','id');
    }

    public function precioVenta()
    {
        return $this->preciosventa->first()->precio ;
    }

    public function precioCompra()
    {
        return $this->precioscompra->first()->precio ;
    }
}
