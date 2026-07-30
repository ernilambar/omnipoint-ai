( function () {
	const data = window.omnipointAiSettings || {};

	const select = document.getElementById( data.modelSelectId );
	const status = document.getElementById( 'omnipoint-ai-status' );
	const listWrap = document.getElementById( 'omnipoint-ai-model-list-wrap' );
	const list = document.getElementById( 'omnipoint-ai-model-list' );
	const showAll = document.getElementById( 'omnipoint-ai-show-all' );

	if ( ! select ) {
		return;
	}

	const maxVisible = 10;

	if ( showAll && list ) {
		showAll.addEventListener( 'click', function () {
			Array.prototype.forEach.call( list.children, function ( item ) {
				item.style.display = '';
			} );
			showAll.style.display = 'none';
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

		ids.forEach( function ( modelId, index ) {
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
				if ( index >= maxVisible ) {
					item.style.display = 'none';
				}
				list.appendChild( item );
			}
		} );

		if ( listWrap ) {
			listWrap.style.display = ids.length ? 'block' : 'none';
		}

		if ( showAll ) {
			showAll.style.display = ids.length > maxVisible ? '' : 'none';
		}

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
