( function () {
	var data = window.omnipointAiSettings || {};

	var select = document.getElementById( data.selectId );
	var status = document.getElementById( data.statusId );

	if ( ! select ) {
		return;
	}

	function populate( ids, currentValue ) {
		select.innerHTML = '';

		var emptyOption = document.createElement( 'option' );
		emptyOption.value = '';
		emptyOption.textContent = data.noOverrideLabel;
		select.appendChild( emptyOption );

		ids.forEach( function ( modelId ) {
			var option = document.createElement( 'option' );
			option.value = modelId;
			option.textContent = modelId;
			if ( modelId === currentValue ) {
				option.selected = true;
			}
			select.appendChild( option );
		} );

		select.disabled = false;
	}

	function showError() {
		populate( [], data.currentModel );

		if ( status ) {
			status.textContent = data.errorLabel;
		}
	}

	fetch( data.ajaxUrl, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: new URLSearchParams( {
			action: 'omnipoint_ai_get_models',
			nonce: data.nonce,
		} ),
	} )
		.then( function ( response ) {
			return response.json();
		} )
		.then( function ( json ) {
			if ( ! json.success || ! json.data ) {
				showError();
				return;
			}

			var modelIds = Array.isArray( json.data.models ) ? json.data.models : [];

			populate( modelIds, data.currentModel );

			if ( status ) {
				status.textContent = json.data.message || '';
			}
		} )
		.catch( showError );
} )();
