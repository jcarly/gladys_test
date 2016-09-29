<?php
	if(isset($_POST['model'])){
		$model = htmlentities(mb_convert_encoding($_POST['model'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8');

		if($model == "fiche"){
			require_once('fiche_model.php'); 

			if(isset($_POST['method'])){
				$method = htmlentities(mb_convert_encoding($_POST['method'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8');
				$fiche = new Fiche();
				switch ($method) {
					case 'get_all':
						if(isset($_POST['categories']) && isset($_POST['show_child']) && ! empty($_POST['categories'])){
							$categories = $_POST['categories'];
							if($_POST['show_child'] == "true"){
								require_once('category_model.php');
								$category = new Category();
								$new_categories = array();
								foreach ($categories as $key => $value) {
									$new_categories += $category->get_child($value);
								}
								$categories = array_unique($new_categories);
							}
							$fiches = $fiche->get_all_by_categories($categories);
						}
						else{
							$fiches = $fiche->get_all();
						}
						$output = '';
						foreach ($fiches as $key => $current_fiche) {
							$fiche_categories = '';

							foreach ($current_fiche['categories'] as $key => $current_category) {
				        		$fiche_categories .= '<a href="#!" data-id="' . $key . '" class="category_link">' . $current_category['cname'] . '</a> ';
							}	
							$output .= '<li class="collection-item fiche-resume" data-id="' . $current_fiche['id'] . '">
						        	<div>
						        		<a class="modal-trigger show-fiche" href="#modal_fiche"> <h5>' . $current_fiche['title'] . '</h5></a>
						        		<p>
						        			' . $fiche_categories . '				        				
						        		</p>
						        		<a class="btn-floating light-blue modal-trigger show-fiche" href="#modal_fiche"><i class="material-icons">visibility</i></a>
						        		<a class="btn-floating green modal-trigger edit-fiche" href="#modal_fiche"><i class="material-icons">mode_edit</i></a>
						        		<a class="btn-floating red remove-fiche"><i class="material-icons">clear</i></a>
						        	</div>
						        </li>';
						}

						echo $output; 

						break;

					case 'get':
						if(isset($_POST['id'])){
							$id = intval(htmlentities(mb_convert_encoding($_POST['id'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
							$fiche->get($id);

							$categories = '';

							foreach ($fiche->categories as $key => $category) {
				        		$categories .= '<a href="#!" data-id="' . $key . '" class="category_link">' . $category['cname'] . '</a> ';
							}
							echo '<div class="row" data-id="' . $fiche->id . '"> 
									<h4>' . $fiche->title . '</h4>
					    			<div class="row">' . $categories . '</div>
									<div class="row">' . $fiche->body . '</div>
									<a class="btn-floating green edit-fiche"><i class="material-icons">mode_edit</i></a>
							        <a class="btn-floating red remove-fiche"><i class="material-icons">clear</i></a>
								</div>';
						}
						break;

					case 'get_form':
						if(isset($_POST['id'])){
							$id = intval(htmlentities(mb_convert_encoding($_POST['id'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
							$fiche->get($id);
							require_once('category_model.php');
							$category = new Category();
							$categories = $category->get_all();

							echo '<div class="row" data-id="' . $fiche->id . '"> 									
									<form class="col s12" id="edit_fiche_form">
								      	<div class="row">
								      		<input type="hidden" value="' . $fiche->id .'" id="id">
								      		<div class="input-field col s6">
												<input type="text" name="title" id="title" value="' . $fiche->title .'">
												<label for="title" class="active">Titre</label>
											</div>
											<div class="input-field col s6">
											    <select multiple name="categories" id="categories">
											      	<option value="0" disabled selected>Selectionner</option>
											      	' . Category::to_options($categories, array_keys($fiche->categories)) . '
											    </select>
											    <label for="categories">Catégories</label>
											</div>
										</div>
										<div class="row">
									        <div class="input-field col s12">
									          	<textarea id="body" class="materialize-textarea">' . $fiche->body .'</textarea>
									        </div>
									    </div>
										<button type="submit" class="btn-floating right btn-large waves-effect waves-light light-blue tooltipped" data-position="left" data-delay="50" data-tooltip="Enregistrer" href="#!"><i class="material-icons">save</i></button>
									</form>
									<a class="btn-floating light-blue show-fiche"><i class="material-icons">visibility</i></a>
							        <a class="btn-floating red remove-fiche"><i class="material-icons">clear</i></a>
								</div>';	
						}
						break;

					case 'insert':
						if(isset($_POST['title']) && isset($_POST['body']) && isset($_POST['categories'])){
							$title = htmlentities(mb_convert_encoding($_POST['title'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8');
							$body = htmlentities(mb_convert_encoding($_POST['body'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8');
							$fiche->insert($title, $body, $_POST['categories']);
						}
						break;

					case 'update':
						if(isset($_POST['id']) && isset($_POST['title']) && isset($_POST['body']) && isset($_POST['categories'])){
							$id = intval(htmlentities(mb_convert_encoding($_POST['id'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
							$title = htmlentities(mb_convert_encoding($_POST['title'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8');
							$body = htmlentities(mb_convert_encoding($_POST['body'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8');
							$fiche->update($id, $title, $body, $_POST['categories']);
						}
						break;

					case 'delete':
						if(isset($_POST['id'])){
							$id = intval(htmlentities(mb_convert_encoding($_POST['id'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
							$fiche->delete($id);
						}
						break;
					
					default:
						break;
				}
			}
		}

		elseif($model == "category"){

			require_once('category_model.php'); 
			
			if(isset($_POST['method'])){
				$method = htmlentities(mb_convert_encoding($_POST['method'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8');
				$category = new Category();
				switch ($method) {
					case 'get_options':
						$categories = $category->get_all();
						echo '<option value="0" disabled selected>Selectionner</option>' . Category::to_options($categories);
						break;

					case 'get_all':
						echo json_encode($category->get_all());
						break;

					case 'insert':
						if(isset($_POST['name']) && isset($_POST['parent'])){
							$name = htmlentities(mb_convert_encoding($_POST['name'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8');
							$parent = intval(htmlentities(mb_convert_encoding($_POST['parent'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
							$category_id = $category->insert($name, $parent);

							$new = $category->get_all();
							echo json_encode(array('options' => '<option value="0" disabled selected>Selectionner</option>' . Category::to_options($new), 'id' => $category_id), JSON_HEX_QUOT | JSON_HEX_TAG);
						}
						break;

					case 'delete':
						if(isset($_POST['id'])){
							$id = intval(htmlentities(mb_convert_encoding($_POST['id'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
							$category->delete($id);

							$categories = $category->get_all();
							echo '<option value="0" disabled selected>Selectionner</option>' . Category::to_options($categories);
						}
						break;

					case 'update_hierarchy':
						if(isset($_POST['list'])){
							$list = json_decode($_POST['list']);
							$category->update_hierarchy($list);

							$categories = $category->get_all();
							echo '<option value="0" disabled selected>Selectionner</option>' . Category::to_options($categories);
						}
						break;

					case 'update_name':
						if(isset($_POST['id']) && isset($_POST['name'])){
							$id = intval(htmlentities(mb_convert_encoding($_POST['id'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
							$name = htmlentities(mb_convert_encoding($_POST['name'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8');

							$category->update_name($id, $name);
							$categories = $category->get_all();
							echo '<option value="0" disabled selected>Selectionner</option>' . Category::to_options($categories);
						}
						break;
					
					default:
						break;
				}
			}
		}
	}
?>
	