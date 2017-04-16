jQuery(document).ready(function($){
	$('.vk-api-import-errror, .vk-api-import-success').hide();
	var run_update;
	$( '.start-vk-api-import' ).click(function(){
		if( $( this ).data('import-running') !=1  ){
			$('.vk-api-import-success').show().html( '<h2>Import start! Please wait.</h2>' );
			$( this ).removeClass( 'btn-primary' ).addClass( 'btn-danger' ).data('import-running', 1).text('Stop Import');
			var itemsNumber = dashboardImportData.itemsNumber;// how many we need items
			run_update = true;
			runVKImport( itemsNumber );			
		} else {
			run_update = false;
			$( this ).removeClass( 'btn-danger' ).addClass( 'btn-primary' ).text('Start Import').data('import-running', 0 );
			$('.start-vk-api-import-progress').removeClass('active');
			$('.vk-api-import-errror, .vk-api-import-success').hide();
		}
	});
	
	function runVKImport( itemsNumber ){
		if( !run_update ){
			return;
		}
		var itemsCount = 0;
		$.ajax({
			method: "POST",
			url: dashboardImportData.startImportUrl,
			timeout: 360000,
			beforeSend: function( xhr ){ 
				xhr.setRequestHeader( 'X-CSRF-TOKEN', dashboardImportData.token );				
			},				
		}).fail(function( jqXHR, textStatus, errorThrown ) {
			if( !run_update ){
				return;
			}
			$('.vk-api-import-errror').show().html( '<h2>' + errorThrown +'<h2>' );
			//try again on fail
			runVKImport( itemsNumber );
		}).done(function(response){
			if( !run_update ){
				return;
			}
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
                    run_update = false;
                    $( '.start-vk-api-import' ).addClass( 'active' ).data('import-running', 0);
				}
			}else{
				$('.vk-api-import-errror').show().html( '<h2>' + response.message +'</h2>' );
				//try again on fail
				runVKImport( itemsNumber );
			}
		});	
	}
});


