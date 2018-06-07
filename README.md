# Gestionar pagos por Redsys en Laravel

Este package sirve para poder realizar gestionar de forma sencilla tanto la solicitud de pagos vía Redsys, así como la recepción de la confirmación de dichos pagos.

A continuación puedes ver un ejemplo simple de cómo se puede generar un formulario que lleve a Redsys para realizar un pago.

En el controller crearíamos la nueva solicitud de pago:

```php
$solicitudPagoRedsys = new \Xoborg\LaravelRedsys\Models\SolicitudPagoRedsys();
$solicitudPagoRedsys->order = 1;
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

Puedes publicar la migración con:

```bash
php artisan vendor:publish --provider="Xoborg\LaravelRedsys\LaravelRedsysServiceProvider" --tag="migrations"
```

Después de que se haya publicado la migración, puedes crear la tabla 'pagos_redsys' ejecutando las migraciones:

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

Esto hará que se cree un archivo blade en `resources/views/vendor/laravel-redsys/example-payment-form.blade`.


## Testing

Puedes ejecutar los tests con:

```bash
vendor/bin/phpunit
```