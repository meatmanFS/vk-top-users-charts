jQuery(document).ready(function($){
	$('.vk-api-import-errror, .vk-api-import-success').hide();
	
	$( '.start-vk-api-import' ).click(function(){
		if( $( this ).data('import-running') !=1  ){
			$('.vk-api-import-success').show().html( '<h2>Import start! Please wait.</h2>' );
			var itemsNumber = dashboardImportData.itemsNumber;// how many we need items
			$( this ).removeClass( 'active' ).data('import-running', 1);
			runVKImport( itemsNumber );			
		}
	});
	
	function runVKImport( itemsNumber ){
		var itemsCount = 0;
		$.ajax({
			method: "POST",
			url: dashboardImportData.startImportUrl,
			timeout: 360000,
			data: {
				itemsNumber: itemsNumber
			},
			beforeSend: function( xhr ){ 
				xhr.setRequestHeader( 'X-CSRF-TOKEN', dashboardImportData.token );				
			},				
		}).fail(function( jqXHR, textStatus, errorThrown ) {
			$('.vk-api-import-errror').show().html( '<h2>' + errorThrown +'<h2>' );
		}).done(function(response){
			$('.vk-api-import-errror, .vk-api-import-success').hide();
			if( response.result == 'success' ){
				itemsCount = response.items_count;
				if( !$('.start-vk-api-import-progress').hasClass('active') ){
					$('.start-vk-api-import-progress').addClass('active');
				}
				var persents = itemsCount /( itemsNumber / 100 );
				$('.start-vk-api-import-progress').text( persents.toFixed(2) + '%' );
				$('.start-vk-api-import-progress').css( 'width', ( persents < 100 )? persents + '%': 100 + '%' );
				$('.vk-api-import-success').show().html( '<h2>' + response.message + '</h2>'+ '<p>Users count: '+ itemsCount +' of '+ itemsNumber +'</p>' );
				if( itemsCount < itemsNumber  ){
					runVKImport( itemsNumber );											
				} else {
					$('.vk-api-import-success').show().html( '<h2>Import stoped!</h2>'+ '<p>Users count:'+ itemsCount +'</p>' );
					$('.start-vk-api-import-progress').removeClass('active');
					$( '.start-vk-api-import' ).addClass( 'active' ).data('import-running', 0);
				}
			}else{
				$('.vk-api-import-errror').show().html( '<h2>' + response.message +'</h2>' );		
				$('.start-vk-api-import-progress').removeClass('active');
				$( '.start-vk-api-import' ).addClass( 'active' ).data('import-running', 0);
			}
		});	
	}
});


