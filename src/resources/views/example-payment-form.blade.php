<form name="frm" action="{{ config('redsys.url.pruebas') }}" method="POST">
	Ds_Merchant_SignatureVersion <input type="text" name="Ds_SignatureVersion" value="{{ config('redsys.ds_signature_version') }}"><br>
	Ds_Merchant_MerchantParameters <input type="text" name="Ds_MerchantParameters" value="{{ $pagoRedsys->getMerchantParameters() }}"/><br>
	Ds_Merchant_Signature <input type="text" name="Ds_Signature" value="{{ $pagoRedsys->getMerchantSignature() }}"><br>
	<input type="submit" value="Enviar" >
</form>