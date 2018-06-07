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
		return NotificacionOnlineHumanReadableResponses::getResponse($this->response);
	}
}