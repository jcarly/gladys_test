<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="author" content="Jonathan Carly">
		<title>Gladys test</title>

        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css">
    
    	<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    	<script src="http://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>    	
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>

        <link href="css/jquery.nestable.css" rel="stylesheet">
        <script type='text/javascript' src='js/jquery.nestable.js'></script>

        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

        <link href="style.css" rel="stylesheet">
		<script type='text/javascript' src='js/script.js'></script>
	</head>

	<body>

		<?php 
			ini_set('xdebug.var_display_max_depth', 5);
			ini_set('xdebug.var_display_max_children', 256);
			ini_set('xdebug.var_display_max_data', 1024);

			require_once('includes/category_model.php'); 
			require_once('includes/fiche_model.php'); 
			$category_model = new Category();
			$fiche_model = new Fiche();
			$categories = $category_model->get_all();
			$fiches = $fiche_model->get_all();
		?>

		<div class="container">
			<div class="row">
				<h2>Gestionnaire de fiches</h2>
			</div>
			<div class="row">
      			<a id="create_fiche_link" class="btn-floating btn-large waves-effect waves-light light-blue tooltipped" data-position="right" data-delay="50" data-tooltip="Créer une fiche" href="#" >
      				<i class="material-icons">add</i>
      			</a>

			    <form class="col s12 hidden" id="create_fiche_form">
			      	<div class="row">
			      		<div class="input-field col s6">
							<input type="text" name="title" id="title" required>
							<label for="title">Titre</label>
						</div>
						<div class="input-field col s5">
						    <select multiple name="categories" id="categories">
						      	<option value="0" disabled selected>Selectionner</option>
						      	<?php echo Category::to_options($categories); ?>
						    </select>
						    <label for="categories">Catégories</label>
						</div>
						<div class="col s1">
						    <a class="btn-floating right btn-large waves-effect waves-light light-blue tooltipped modal-trigger" data-position="top" data-delay="50" data-tooltip="Gérer les catégories" href="#modal_categories"><i class="material-icons">create</i></a>
						</div>
					</div>
					<div class="row">
				        <div class="input-field col s12">
				          	<textarea id="body" class="materialize-textarea" required></textarea>
				        </div>
				    </div>
					<button type="submit" class="btn-floating right btn-large waves-effect waves-light light-blue tooltipped" data-position="left" data-delay="50" data-tooltip="Enregistrer" href="#!"><i class="material-icons">save</i></button>
				</form>	
			</div>

			<div class="row">

				<h4 class="col s4">Fiches</h4>
				<div class="input-field col s4">
					<select multiple id="collection-categories">
				      	<option value="0" disabled selected>Selectionner</option>
				      	<?php echo Category::to_options($categories); ?>
				    </select>
			    	<label for="collection-categories">Categories</label>
			    </div>
			    <div class="input-field col s4">
				    <input type="checkbox" id="show-child" />
	      			<label for="show-child">Afficher les catégories enfants</label>
	      		</div>
      		</div>

			<div class="row">
				
				<ul class="collection" id="fiches">
			        <?php foreach ($fiches as $key => $fiche): ?>			
				        <li class="collection-item fiche-resume" data-id="<?php echo $fiche['id']; ?>">
				        	<div>
				        		<a class="modal-trigger show-fiche" href="#modal_fiche"> <h5> <?php echo $fiche['title']; ?></h5></a>
				        		<p>
				        			<?php foreach ($fiche['categories'] as $key => $category): ?> 
				        				<a href="#!" data-id="<?php echo $key; ?>" class="category_link"><?php echo $category['cname']; ?></a>
				        			<?php endforeach; ?>				        				
				        		</p>
				        		<a class="btn-floating light-blue modal-trigger show-fiche" href="#modal_fiche"><i class="material-icons">visibility</i></a>
				        		<a class="btn-floating green modal-trigger edit-fiche" href="#modal_fiche"><i class="material-icons">mode_edit</i></a>
				        		<a class="btn-floating red remove-fiche"><i class="material-icons">clear</i></a>
				        	</div>
				        </li>
			        <?php endforeach; ?>
		      	</ul>
		    </div>

		  	<div id="modal_categories" class="modal">
		  	
		  		<a href="#!" class=" modal-action right modal-close waves-effect waves-green btn-flat"><i class="material-icons">clear</i></a>

		    	<div class="modal-content container">

		    		<h4>Gestion des catégories</h4>

	    			<div class="row">
					    <a id="create_category_link" class="btn-floating btn-large waves-effect waves-light blue tooltipped" data-position="right" data-delay="50" data-tooltip="Ajouter une catégorie" href="#" >
		      				<i class="material-icons">add</i>
		      			</a>

					    <form class="col s12 hidden" id="create_category_form">
					      	<div class="row">
					      		<div class="input-field col s4">
									<input type="text" name="name" id="name" required>
									<label for="name">Catégorie</label>
								</div>
								<div class="input-field col s4">
								    <select name="parent" id="parent">
								      	<option value="0" disabled selected>Selectionner</option>
								      	<?php echo Category::to_options($categories); ?>
								    </select>
								    <label for="parent">Parent</label>
								</div>
								<div class="col s4">
									<input type="submit" name="submit" value="Enregistrer" class="waves-effect waves-light btn">
								</div>
							</div>
						</form>	
					</div>
					<div class="row">
						<div class="dd">
							<?php echo Category::to_nestable($categories); ?>
						</div>
					</div>
		    	</div>
		    </div>

		    <div id="modal_fiche" class="modal">
		  	
		  		<a href="#!" class=" modal-action right modal-close waves-effect waves-green btn-flat"><i class="material-icons">clear</i></a>

		    	<div class="modal-content container">

		    	</div>
		    </div>

	  	</div>	
	</body>
</html>