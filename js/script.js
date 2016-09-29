function initiate(){
	categories = [];
	fiches = [];
	$.ajax({
	  	method: "POST",
	  	url: "includes/ajax.php",
	  	data: { model: "category", method: "get_all" }
	})
	.done(function( output ) {
	    categories = output;
	});

	$.ajax({
	  	method: "POST",
	  	url: "includes/ajax.php",
	  	data: { method: "get_all" }
	})
	.done(function( output ) {
	    fiches = output;
	});
}

function updateNestable(){
	$('.dd-item .remove-category').show();
	$('.dd-list').parent('.dd-item').children('.remove-category').hide();
}
function updateCategoryLists(options){
	$('#create_category_form select[name="parent"]').html(options);
    $('#create_category_form select[name="parent"]').val(0);
    $('#create_fiche_form select[name="categories"]').html(options);
    $('#create_fiche_form select[name="categories"]').val(0);
    $('select#collection-categories').html(options);
    $('select#collection-categories').val(0);
    $('#create_category_form select[name="parent"]').material_select();
    $('#create_fiche_form select[name="categories"]').material_select();
    $('select#collection-categories').material_select();
}

function updateFiches(){
	var categories = $('select#collection-categories').val();
	var show_child = $('input#show-child').is(':checked');

	var data = { model: "fiche", method: "get_all", categories: categories, show_child: show_child };

	$.ajax({
	  	method: "POST",
	  	url: "includes/ajax.php",
	  	data: data
	})
	.done(function( output ) {
		$('#fiches').html(output);	
		$('.modal-trigger').leanModal();
	});
}


$(document).ready(function(){

	/* INITiALISATION */

	$('.modal-trigger').leanModal();
	$('select').material_select();
	$('.dd').nestable().on('change', function(){
		var list = JSON.stringify($(this).nestable('serialize'));
		var data = { model: "category", method: "update_hierarchy", list: list };
		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( output ) {	
			updateCategoryLists(output);	
			updateFiches();	
		});
		updateNestable();
	});

	$('.dd-list').parent('.dd-item').children('.remove-category').hide();

	/* CATEGORIES */

	$('.dd').on('click', '.dd-item .remove-category', function(e){
		e.preventDefault();

		var id = $(this).parent('.dd-item').attr('data-id');		
		$(this).parent('.dd-item').remove();
		$('.dd-list').each(function(){
			if($(this).is(':empty')){
		  		$(this).remove();
		  	}
		});

		updateNestable();

		var data = { model: "category", method: "delete", id: id };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( output ) {
			updateCategoryLists(output);
			updateFiches();	    
		});

	});

	$('.dd').on('click', '.dd-item .edit-category', function(e){
		e.preventDefault();
		e.stopPropagation(); 

		var name = $(this).parent('.dd-item').children('.dd-handle').html();
		$(this).parent('.dd-item').children('.dd-handle').html('');
		$(this).parent('.dd-item').children('.dd-handle').after('<input type="text">');

		$(this).removeClass('edit-category');
		$(this).addClass('update-category');

		$(this).parent('.dd-item').children('input').focus();
		$(this).parent('.dd-item').children('input').val(name);

		$(this).children('.material-icons').html('done');
	});

	$('.dd').on('click', '.dd-item .update-category', function(e){
		e.preventDefault();
		e.stopPropagation(); 

		var id = $(this).parent('.dd-item').attr('data-id');	
		var name = $(this).parent('.dd-item').children('input').val();	

		$(this).parent('.dd-item').children('.dd-handle').html(name);
		$(this).parent('.dd-item').children('input').remove();

		$(this).removeClass('update-category');
		$(this).addClass('edit-category');

		$(this).children('.material-icons').html('mode_edit');

		var data = { model: "category", method: "update_name", id: id, name: name };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( output ) {
			updateCategoryLists(output);	    
			updateFiches();
		});
	});

	$('.dd').on('focusout', '.dd-item input', function(e){
		e.preventDefault();

		var id = $(this).parent('.dd-item').attr('data-id');	
		var name = $(this).val();	

		var item = $(this).parent('.dd-item');

		$(this).parent('.dd-item').children('.dd-handle').html(name);		

		$(this).remove();

		var data = { model: "category", method: "update_name", id: id, name: name };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( output ) {
			updateCategoryLists(output);	
			item.children('.update-category').addClass('edit-category');
			item.children('.update-category').removeClass('update-category');

			item.children('.edit-category').children('.material-icons').html('mode_edit');  

			updateFiches();

			e.stopPropagation();  
		});
	});

	$('#create_category_link').click(function(e){
		e.preventDefault();
		$('#create_category_form').animate({
            height: "toggle",
            opacity: "toggle"
        }, "slow");
	});

	$('#create_category_form').submit(function(e){
		e.preventDefault();
		var name = $(this).find('input[name="name"]').val();
		var parent = $(this).find('select[name="parent"]').val();
		var data = { model: "category", method: "insert", name: name, parent: parent };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( output ) {			
			output = $.parseJSON(output);
		    $('#create_category_form input[name="name"]').val('');
		    updateCategoryLists(output.options);
		    if(parent != null){
			    if(! $('.dd').find('.dd-item[data-id="' + parent + '"]').find('.dd-list').length){
			    	 $('.dd').find('.dd-item[data-id="' + parent + '"]').append('<ol class="dd-list"></ol>');
			    }
			    $('.dd').find('.dd-item[data-id="' + parent + '"]').children('.dd-list').append('<li class="dd-item" data-id="' + output.id + '"><div class="dd-handle">' + name + '</div><a class="btn-floating right green edit-category"><i class="material-icons">mode_edit</i></a><a class="btn-floating right red remove-category"><i class="material-icons">clear</i></a></li>');
			}
			else{
				if(! $('.dd').children('.dd-list').length){
			    	$('.dd').append('<ol class="dd-list"></ol>');
			    }
				$('.dd').children('.dd-list').append('<li class="dd-item" data-id="' + output.id + '"><div class="dd-handle">' + name + '</div><a class="btn-floating right green edit-category"><i class="material-icons">mode_edit</i></a><a class="btn-floating right red remove-category"><i class="material-icons">clear</i></a></li>');
			}
			$('.dd').nestable();
			updateNestable();
		});
	});

	$('body').on('click', '.category_link', function(e){
		e.preventDefault();
		var category_id = $(this).attr('data-id');
		$('select#collection-categories').val([category_id]);
		$('select#collection-categories').material_select();
		$('select#collection-categories').trigger('change');
		$('#modal_fiche').closeModal();
	});

	/* FICHE */

	$('select#collection-categories').on('change', function(){
		updateFiches();
	});

	$('input#show-child').on('change', function(){
		updateFiches();
	});

	$('#create_fiche_link').click(function(e){
		e.preventDefault();
		$('#create_fiche_form').animate({
            height: "toggle",
            opacity: "toggle"
        }, "slow");
	});

	$('#create_fiche_form').submit(function(e){
		e.preventDefault();
		var title = $('#create_fiche_form input#title').val();
		var body = $('#create_fiche_form textarea#body').val();
		var categories = $('#create_fiche_form select[name="categories"]').val();

		var data = { model: "fiche", method: "insert", title: title, body: body, categories: categories };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( msg ) {
		    $('#create_fiche_form input[name="title"]').val('');
		    $('#create_fiche_form #body').val('');
		    $('#create_fiche_form select[name="categories"]').val(0);
		    $('#create_fiche_form select[name="categories"]').material_select();
		    updateFiches();
		});
	});

	$('#modal_fiche').on('submit', '#edit_fiche_form', function(e){
		e.preventDefault();
		var id = $('#edit_fiche_form input#id').val();
		var title = $('#edit_fiche_form input#title').val();
		var body = $('#edit_fiche_form textarea#body').val();
		var categories = $('#edit_fiche_form select[name="categories"]').val();

		var data = { model: "fiche", method: "update", id: id, title: title, body: body, categories: categories };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( msg ) {
			$('#modal_fiche').closeModal();
			updateFiches();
		});
	});

	$('#fiches').on('click', '.show-fiche', function(e){
		e.preventDefault();

		var id = $(this).parent('div').parent('.collection-item').attr('data-id');		

		var data = { model: "fiche", method: "get", id: id };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( output ) {
			$('#modal_fiche .modal-content').html(output);    
		});		
	});

	$('#fiches').on('click', '.edit-fiche', function(e){
		e.preventDefault();

		var id = $(this).parent('div').parent('.collection-item').attr('data-id');		

		var data = { model: "fiche", method: "get_form", id: id };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( output ) {
			$('#modal_fiche .modal-content').html(output);   
			$('select').material_select(); 
		});		
	});

	$('#fiches').on('click', '.remove-fiche', function(e){
		e.preventDefault();

		var id = $(this).parent('div').parent('.collection-item').attr('data-id');			
		$(this).parent('div').parent('.collection-item').remove();

		var data = { model: "fiche", method: "delete", id: id };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		});
	});

	$('#modal_fiche').on('click', '.show-fiche', function(e){
		e.preventDefault();

		var id = $(this).parent('.row').attr('data-id');		

		var data = { model: "fiche", method: "get", id: id };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( output ) {
			$('#modal_fiche .modal-content').html(output);    
		});		
	});

	$('#modal_fiche').on('click', '.edit-fiche', function(e){
		e.preventDefault();

		var id = $(this).parent('.row').attr('data-id');		

		var data = { model: "fiche", method: "get_form", id: id };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		})
		.done(function( output ) {
			$('#modal_fiche .modal-content').html(output);   
			$('select').material_select(); 
		});		
	});

	$('#modal_fiche').on('click', '.remove-fiche', function(e){
		e.preventDefault();

		var id = $(this).parent('.row').attr('data-id');			
		$('#fiches').find('.collection-item[data-id="' + id + '"]').remove();

		$('#modal_fiche').closeModal(); 

		var data = { model: "fiche", method: "delete", id: id };

		$.ajax({
		  	method: "POST",
		  	url: "includes/ajax.php",
		  	data: data
		});
	});


	

	/*$('#contactform').validate({

 
    // Add requirements to each of the fields
    rules: {
      name: {
        required: true,
        minlength: 2
      },
      
    },
 
    // Specify what error messages to display
    // when the user does something horrid
    messages: {
      name: {
        required: "Please enter your name.",
        minlength: jQuery.format("At least {0} characters required.")
      },
     
    },
 
    // Use Ajax to send everything to processForm.php
    submitHandler: function(form) {
      $("#send").attr("value", "Sending...");
      $(form).ajaxSubmit({
        target: "#response",
        success: function(responseText, statusText, xhr, $form) {
          $(form).slideUp("fast");
          $("#response").html(responseText).hide().slideDown("fast");
        }
      });
      return false;
    }
  });*/
});