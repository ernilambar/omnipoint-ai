( function () {
	const data = window.omnipointAiSettings || {};

	const select = document.getElementById( data.modelSelectId );
	const status = document.getElementById( 'omnipoint-ai-status' );
	const modelsTable = document.getElementById( data.modelsTableId );
	const showAll = document.getElementById( 'omnipoint-ai-show-all' );

	if ( ! select ) {
		return;
	}

	const maxVisible = 10;

	if ( showAll && modelsTable ) {
		showAll.addEventListener( 'click', function () {
			Array.prototype.forEach.call( modelsTable.querySelectorAll( 'li' ), function ( item ) {
				item.style.display = '';
			} );
			showAll.style.display = 'none';
		} );
	}

	function esc( str ) {
		return String( str )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' );
	}

	function renderModelsTable( ids ) {
		if ( ! modelsTable ) {
			return;
		}

		if ( ! ids.length ) {
			modelsTable.innerHTML = '';

			if ( showAll ) {
				showAll.style.display = 'none';
			}

			return;
		}

		let html = '<ul>';

		ids.forEach( function ( modelId, index ) {
			html += '<li' + ( index >= maxVisible ? ' style="display: none;"' : '' ) + '><code>' + esc( modelId ) + '</code></li>';
		} );

		html += '</ul>';

		modelsTable.innerHTML = html;

		if ( showAll ) {
			showAll.style.display = ids.length > maxVisible ? '' : 'none';
		}
	}

	function populate( ids, currentValue ) {
		select.innerHTML = '';

		const emptyOption = document.createElement( 'option' );
		emptyOption.value = '';
		emptyOption.textContent = data.noOverrideLabel;
		select.appendChild( emptyOption );

		ids.forEach( function ( modelId ) {
			const option = document.createElement( 'option' );
			option.value = modelId;
			option.textContent = modelId;
			if ( modelId === currentValue ) {
				option.selected = true;
			}
			select.appendChild( option );
		} );

		select.disabled = false;
	}

	function setStatus( message, isError ) {
		if ( ! status ) {
			return;
		}

		status.textContent = message;
		status.classList.toggle( 'omnipoint-ai-status-error', isError );
		status.classList.toggle( 'omnipoint-ai-status-success', ! isError );
	}

	function showError() {
		populate( [], data.currentModel );
		renderModelsTable( [] );
		setStatus( data.errorLabel, true );
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
			renderModelsTable( modelIds );
			setStatus( json.data.message || '', ! modelIds.length );
		} )
		.catch( showError );
} )();
