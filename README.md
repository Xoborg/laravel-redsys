# Gestionar pagos por Redsys en Laravel

Este package sirve para poder realizar gestionar de forma sencilla tanto la solicitud de pagos vía Redsys, así como la recepción de la confirmación de dichos pagos.

A continuación puedes ver un ejemplo simple de cómo se puede generar un formulario que lleve a Redsys para realizar un pago.

En el controller crearíamos la nueva solicitud de pago:

```php
$solicitudPagoRedsys = new \Xoborg\LaravelRedsys\Models\SolicitudPagoRedsys();
$solicitudPagoRedsys->order = '0001';
$solicitudPagoRedsys->amount = 1000;
```

Y en nuestra vista iría un formulario similar al siguiente:

```html
<form name="frm" action="{{ config('redsys.url.pruebas') }}" method="POST">
	Ds_Merchant_SignatureVersion <input type="text" name="Ds_SignatureVersion" value="{{ config('redsys.ds_signature_version') }}"/></br>
	Ds_Merchant_MerchantParameters <input type="text" name="Ds_MerchantParameters" value="{{ $pagoRedsys->getMerchantParameters() }}"/></br>
	Ds_Merchant_Signature <input type="text" name="Ds_Signature" value="{{ $pagoRedsys->getMerchantSignature() }}"/></br>
	<input type="submit" value="Enviar" >
</form>
``` 

## Instalación

Puedes instalar el package mediante composer con el siguiente comando:

```bash
composer require xoborg/laravel-redsys
```

Puedes publicar las migraciones con:

```bash
php artisan vendor:publish --provider="Xoborg\LaravelRedsys\LaravelRedsysServiceProvider" --tag="migrations"
```

Después de que se hayan publicado las migraciones, puedes crear las tablas 'pagos_redsys' y 'notificaciones_online_redsys' ejecutando las migraciones:

```bash
php artisan migrate
```

También puedes publicar el archivo de configuración con:

```bash
php artisan vendor:publish --provider="Xoborg\LaravelRedsys\LaravelRedsysServiceProvider" --tag="config"
```

Por defecto se usan los datos de prueba que aparecen en los manuales de Redsys, para cambiarlos tan sólo tienes que crear las variables de entorno necesarias:

```php
    ...
    
    DS_MERCHANT_MERCHANTCODE=000000000
    DS_MERCHANT_TERMINAL=0
    etc.
    
    ...
```

Además, puedes acceder a una vista con el formulario de ejemplo con:

```bash
php artisan vendor:publish --provider="Xoborg\LaravelRedsys\LaravelRedsysServiceProvider" --tag="views"
```

Esto hará que se cree un archivo blade en `resources/views/vendor/laravel-redsys/example-payment-form.blade.php`.

## Documentación

#### Ejemplo completo del proceso

A continuación puedes ver un ejemplo bastante más avanzado de como podría ser el proceso completo, desde la creación de la solicitud hasta la recepción de la notificación online y de la parte del cliente.

Lo primero sería crear la solicitud de pago en una acción del controller que queramos:

```php
...

$solicitudPagoRedsys = new \Xoborg\LaravelRedsys\Models\SolicitudPagoRedsys();
$solicitudPagoRedsys->order = '0002';
$solicitudPagoRedsys->amount = 2075;
$solicitudPagoRedsys->merchantUrl = route('notificacion-online');
$solicitudPagoRedsys->urlOk = route('pago-ok');
$solicitudPagoRedsys->urlKo = route('pago-ko');
$solicitudPagoRedsys->productDescription = 'Producto de ejemplo';
$solicitudPagoRedsys->titular = 'Nombre del cliente';
$solicitudPagoRedsys->merchantName = 'Empresa de ejemplo S.L.';

$pagoRedsys = $solicitudPagoRedsys->saveInDatabase();

// Guardariamos el id del pago en nuestra propia tabla de compras o similar.

return view('formulario-pago', compact('solicitudPagoRedsys'));
```

Una vez el usuario haga clic en el botón de pagar del fomulario se desencadenarán dos acciones, siempre que pongamos tanto el merchantUrl como las urls de OK y KO.

Para poder recibir la notificación online tendremos que tener una ruta que acepte peticiones POST (la misma que hemos puesto en merchantUrl de la solicitud) y un controller con una acción parecida a esta:

```php
...

$notificacionOnlineRedsys = new \Xoborg\LaravelRedsys\Models\NotificacionOnlineRedsys();
$notificacionOnlineRedsys->setUp($request->input('Ds_MerchantParameters'));

if ($notificacionOnlineRedsys->firmaValida($request->input('Ds_Signature'))) {
	
	$pagoRedsys = \Xoborg\LaravelRedsys\Models\PagoRedsys::where('Ds_Merchant_Order', $notificacionOnlineRedsys->order)->firstOrFail();
	
	// También es muy recomendable comprobar que algunos de los datos recibidos son los mismos que los que tenemos guardados de la solicitud de pago, como por ejemplo el importe o la moneda (la orden la hemos utilizado para buscar el propio pago).
	
	...
	
	// Insertamos la notificación online en DB
	$pagoRedsys->notificacionesOnlineRedsys()->save($notificacionOnlineRedsys);
	
	// Ahora podríamos cambiar el estado de la compra en nuestra propia tabla, etc.
}

// La firma no es válida así que aquí no tendríamos que hacer nada más ya que no podemos fiarnos de la información que ha llegado

```

Además, el usuario habrá sido redirigido a la ruta de OK o KO, dos acciones de un controller que podrían tener un código similar a lo siguiente:

```php
...

// IMPORTANTE: Aquí sólo debemos utilizar la información que nos llegue para mostrarle al usuario el estado de la operación, no debemos utilizar esta información para guardarla en DB ni fiarnos de ella ya que puede haberse modificado.

$notificacionOnlineRedsys = new \Xoborg\LaravelRedsys\Models\NotificacionOnlineRedsys();
$notificacionOnlineRedsys->setUp($request->input('Ds_MerchantParameters'));

if ($notificacionOnlineRedsys->firmaValida($request->input('Ds_Signature'))) {
	
	$pagoRedsys = \Xoborg\LaravelRedsys\Models\PagoRedsys::where('Ds_Merchant_Order', $notificacionOnlineRedsys->order)->firstOrFail();
    	
	// También es muy recomendable comprobar que algunos de los datos recibidos son los mismos que los que tenemos guardados de la solicitud de pago, como por ejemplo el importe o la moneda (la orden la hemos utilizado para buscar el propio pago).
	
	...
	
	// Podemos utilizar el método "getResponse()" del modelo NotificacionOnlineRedsys para obtener un código de respuesta que se pueda enseñar al propio usuario
	
	$codigoRespuesta = $notificacionOnlineRedsys->getResponse();
	
	return view('pago-ok', compact('codigoRespuesta'));
	
}

// Aquí podemos reenviar al usuario a una pantalla de error ya que la información que nos ha llegado no es válida

```

Y con esto habríamos finalizado el proceso de pago.

#### Clases de utilidad

Dentro del namespace `\Xoborg\LaravelRedsys\Services\Redsys` hay 4 clases que nos ayudarán a configurar el package, etc. sin tener que ver la documentación oficial de Redsys. 

**DsMerchantConsumerLanguage**

Con esta clase podremos obtener el código de idioma para especificarlo a la hora de crear una nueva solicitud de pago:

```php
$solicitudPagoRedsys->consumerLanguage = \Xoborg\LaravelRedsys\Services\Redsys\DsMerchantConsumerLanguage::CASTELLANO;
```

**DsMerchantCurrency**

Con esta clase podremos obtener el código de la moneda para especificarlo a la hora de crear una nueva solicitud de pago:

```php
$solicitudPagoRedsys->currency = \Xoborg\LaravelRedsys\Services\Redsys\DsMerchantCurrency::EUROS;
```

**DsMerchantTransactionType**

Con esta clase podremos obtener el tipo de transacción que vamos a utilizar en la solicitud de pago:

```php
$solicitudPagoRedsys->transactionType = \Xoborg\LaravelRedsys\Services\Redsys\DsMerchantTransactionType::AUTORIZACION;
```

**NotificacionOnlineHumanReadableResponses**

Con esta clase podremos obtener el texto correspondiente al código de respuesta enviado por Redsys una vez hecho un pago:

```php
$textoRespuesta = \Xoborg\LaravelRedsys\Services\Redsys\NotificacionOnlineHumanReadableResponses::getResponse($responseCode);
```

## Testing

Puedes ejecutar los tests con:

```bash
vendor/bin/phpunit
```

## License

The MIT License (MIT). Mira el [archivo de licencia](LICENSE.md) para más información.
