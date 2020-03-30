jQuery(document).ready(function($){

			 let custom_uploader;

			 $('.my_upl_button').click(function(e) {

			 e.preventDefault();
			 let currentfiles = document.getElementsByClassName('image_url').value;
			 let currentIDs = document.getElementsByClassName('image_id').value;
			

					 //If the uploader object has already been created, reopen the dialog
					 if (custom_uploader) {
							 custom_uploader.open();
							 return;
					 } 

					 //Extend the wp.media object
					 custom_uploader = wp.media.frames.file_frame = wp.media({
							 title: 'Choose File',
							 button: {
									 text: 'Choose File'
							 },
							 multiple: false
					 });

					  //When a file is selected, grab the URL and set it as the text field's value
					  custom_uploader.on('select', function() {

						//assign current values to the hidden fields
						if(currentIDs != ''){
							$('.image_id').val(currentIDs);									
					 	}
						
						if(currentfiles != ''){											 
							$( '.image_url' ).val(currentfiles);
					 	}

						var selection = custom_uploader.state().get('selection').toJSON();

						
						selection.map( function(attachment){			


							//update id's field
							$('.image_id').val(attachment.id);

							//update urls's field
							$( '.image_url' ).val(attachment.url);
							

						});



						custom_uploader = null;
						});


					 //Open the uploader dialog
					 custom_uploader.open();

			 });

});
jQuery(document).ready(function($){

				$('.my_clear_button').click(function(e) {

				$( '.image_id').val("");
				$( '.image_url' ).val("");

				return;
					 
			});

});