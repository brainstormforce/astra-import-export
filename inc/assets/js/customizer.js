
( function( $ ) {
	
	var AIE = {
	
		init: function()
		{
			$( 'input[name=astra-export-button]' ).on( 'click', AIE._export );
			$( 'input[name=astra-import-button]' ).on( 'click', AIE._import );
		},
	
		_export: function()
		{
			window.location.href = ASTRAConfig.customizerURL + '?astra-export=' + ASTRAConfig.exportNonce;
		},
	
		_import: function()
		{
			var win			= $( window ),
				body		= $( 'body' ),
				form		= $( '<form class="astra-form" method="POST" enctype="multipart/form-data"></form>' ),
				controls	= $( '.astra-import-controls' ),
				file		= $( 'input[name=astra-import-file]' ),
				message		= $( '.astra-uploading' );
			
			if ( '' == file.val() ) {
				alert( ASTRAl10n.emptyImport );
			}
			else {
				win.off( 'beforeunload' );
				body.append( form );
				form.append( controls );
				message.show();
				form.submit();
			}
		}
	};
	
	$( AIE.init );
	
})( jQuery );