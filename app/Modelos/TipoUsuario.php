<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;
use Sofa\Eloquence\Mutable;

class TipoUsuario extends Model{

	protected $table = 'tbl_tipos_usuario';
	//protected $primaryKey = 'idusuario';
	protected $primaryKey = 'tus_id';

  use Eloquence, Mappable, Mutable;

  protected $maps = [
    'titulo' => 'tus_titulo',
  ];

  protected $appends  = [
    'titulo',
  ];

  public function getId(){
    return $this->attributes['tus_id'];
  }
	public function getTusIdAttribute($value)
  {
    return $value?$value:0;
  }

  protected $hidden = [
    'tus_id',
    'tus_titulo'
  ];

  protected $getterMutators = [
        'usu_nombre' => 'strtolower|ucwords',
        'usu_apellido' => 'strtolower|ucwords',
    ];


	public function usuarios(){
			return $this->hasMany('App\Modelos\Usuario','tus_id','tus_id');
		}
}
