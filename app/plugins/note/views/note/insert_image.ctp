<form class="wysiwyg" id="wysiwyg-addImage" action="javascript:void(0)"><fieldset>
	<div class="form-row"><span class="form-row-key">{preview}:</span><div class="form-row-value"><img src="" alt="{preview}" style="margin: 2px; padding:5px; max-width: 100%; overflow:hidden; max-height: 100px; border: 1px solid rgb(192, 192, 192);"/></div></div>
		<div class="form-row"><label for="name">{url}:</label><div class="form-row-value"><input type="text" name="src" value=""/>
		</div>
	</div>
	<div class="form-row"><label for="name">{title}:</label><div class="form-row-value"><input type="text" name="imgtitle" value=""/></div></div>
	<div class="form-row"><label for="name">{description}:</label><div class="form-row-value"><input type="text" name="description" value=""/></div></div>
	<div class="form-row"><label for="name">{width} x {height}:</label><div class="form-row-value"><input type="text" name="width" value="" class="width-small"/> x <input type="text" name="height" value="" class="width-small"/></div></div>
	<div class="form-row"><label for="name">{original}:</label><div class="form-row-value"><input type="text" name="naturalWidth" value="" class="width-small" disabled="disabled"/>
	<input type="text" name="naturalHeight" value="" class="width-small" disabled="disabled"/></div></div>
	<div class="form-row"><label for="name">{float}:</label><div class="form-row-value"><select name="float">
	<option value="">{floatNone}</option>
	<option value="left">{floatLeft}</option>
	<option value="right">{floatRight}</option></select></div></div>
	<div class="form-row form-row-last"><label for="name"></label><div class="form-row-value"><input type="submit" id="wysiwyg-submit" class="button" value="{submit}"/>
	<input type="reset" value="{reset}"/></div></div></fieldset></form>
