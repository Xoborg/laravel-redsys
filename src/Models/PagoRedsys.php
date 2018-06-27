<?php

namespace Xoborg\LaravelRedsys\Models;

use Illuminate\Database\Eloquent\Model;

class PagoRedsys extends Model
{
	protected $table = 'pagos_redsys';

	public function notificacionesOnlineRedsys()
	{
		return $this->hasMany(NotificacionOnlineRedsys::class);
	}
}