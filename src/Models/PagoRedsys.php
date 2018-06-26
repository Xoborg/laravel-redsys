<?php

namespace Xoborg\LaravelRedsys\Models;

use Illuminate\Database\Eloquent\Model;
use Xoborg\LaravelRedsys\Services\Redsys\NotificacionOnlineHumanReadableResponses;

class PagoRedsys extends Model
{
	protected $table = 'pagos_redsys';

	/**
	 * @return string
	 * @throws \Xoborg\LaravelRedsys\Exceptions\NotificacionOnlineResponseCodeException
	 */
	public function getResponseText(): string
	{
		if (!is_null($this->ds_response)) {
			return NotificacionOnlineHumanReadableResponses::getResponse($this->ds_response);
		}

		return 'No se ha recibido la notificación online.';
	}
}