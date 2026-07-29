( function () {
	const data = window.omnipointAiSettings || {};

	const select = document.getElementById( data.selectId );
	const status = document.getElementById( data.statusId );
	const list = document.getElementById( data.listId );

	if ( ! select ) {
		return;
	}

	if ( status && list ) {
		status.addEventListener( 'click', function () {
			if ( ! list.childElementCount ) {
				return;
			}

			const isHidden = 'none' === list.style.display;
			list.style.display = isHidden ? 'block' : 'none';
			status.setAttribute( 'aria-expanded', isHidden ? 'true' : 'false' );
		} );
	}

	function populate( ids, currentValue ) {
		select.innerHTML = '';

		const emptyOption = document.createElement( 'option' );
		emptyOption.value = '';
		emptyOption.textContent = data.noOverrideLabel;
		select.appendChild( emptyOption );

		if ( list ) {
			list.innerHTML = '';
		}

		ids.forEach( function ( modelId ) {
			const option = document.createElement( 'option' );
			option.value = modelId;
			option.textContent = modelId;
			if ( modelId === currentValue ) {
				option.selected = true;
			}
			select.appendChild( option );

			if ( list ) {
				const item = document.createElement( 'li' );
				item.textContent = modelId;
				list.appendChild( item );
			}
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

			const modelIds = Array.isArray( json.data.models ) ? json.data.models : [];

			populate( modelIds, data.currentModel );

			if ( status ) {
				status.textContent = json.data.message || '';
			}
		} )
		.catch( showError );
} )();
