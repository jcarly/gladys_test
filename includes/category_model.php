<?php
	require_once('model.php'); 

	class Category extends Model{
		var $name;
		var $parent;

		function get_all(){
			$query = $this->db->query('SELECT * FROM categories');
			$output = array_map('reset', $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC));
			$query->CloseCursor();
			
			return self::hierarchize($output);
		}

		function get($id){
			$query = $this->db->prepare('SELECT * FROM categories WHERE id = :id ')->bindValue(':id', $id, PDO::PARAM_INT);
			$output = $query->execute()->fetchAll();
			$query->CloseCursor();
			return $output;
		}

		function get_child($id, $child = array()){
			$child[] = $id;

			$query = $this->db->prepare('SELECT * FROM categories WHERE parent = :id ');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$output = $query->fetchAll();		
			$query->CloseCursor();

			foreach ($output as $key => $value) {
				$child += $this->get_child($value['id'], $child); 
			}		
					
			return $child;
		}

		function insert($name, $parent=0){
			$query = $this->db->prepare('INSERT INTO categories(name, parent) VALUES(:name, :parent)');
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$query->bindValue(':parent', $parent, PDO::PARAM_INT);
			$query->execute();
			$category_id = $this->db->lastInsertId();
			$query->CloseCursor();

			return $category_id;
		}

		function update($id, $name, $parent=0){
			$query = $this->db->prepare('UPDATE categories SET name = :name, parent = :parent WHERE id = :id');			
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$query->bindValue(':parent', $parent, PDO::PARAM_INT);
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();
		}

		function update_parent($id, $parent=0){
			$query = $this->db->prepare('UPDATE categories SET parent = :parent WHERE id = :id');			
			$query->bindValue(':parent', $parent, PDO::PARAM_INT);
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();
		}

		function update_name($id, $name){
			$query = $this->db->prepare('UPDATE categories SET name = :name WHERE id = :id');			
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();
		}

		function update_hierarchy($list){
			$categories = self::inhierarchize($list);	
			foreach ($categories as $id => $parent) {
				$this->update_parent($id, $parent);
			}		
		}

		function delete($id){
			$query = $this->db->prepare('DELETE FROM categories WHERE id = :id');			
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();

			$query = $this->db->prepare('DELETE FROM fiche_category WHERE category_id = :id');			
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();
		}

		static function to_options($array, $selected = array(), $i = 0){
			$output = '';

			foreach ($array as $key => $value) {
				$output .= '<option class="child_' . $i . '" value="' . $key . '" ' . (in_array($key, $selected) ? 'selected' : '') . '>' . str_repeat('- ', $i)  . $value['name'] . '</option>'; 
				if(isset($value['child'])){
					$output .= self::to_options($value['child'], $selected, $j = $i+1);
				}
			}

			return $output;
		}

		static function to_nestable($array){
			$output = '<ol class="dd-list">';

			foreach ($array as $key => $value) {
				$output .= '<li class="dd-item" data-id="' . $key . '"><div class="dd-handle">' . $value['name'] . '</div><a class="btn-floating right green edit-category"><i class="material-icons">mode_edit</i></a><a class="btn-floating right red remove-category"><i class="material-icons">clear</i></a>'; 
				if(isset($value['child'])){
					$output .= self::to_nestable($value['child']);
				}
				$output .= '</li>';
			}
			$output .= '</ol>';

			return $output;
		}

		static function hierarchize($array, $output = array()){

			if(count($output == 0)){
				foreach ($array as $key => $value) {
					if($value['parent'] == 0){
						$output[$key] = $value;
						unset($array[$key]);
					}
				}
			}
			
			foreach ($array as $key => $value) {
				if(array_key_exists($value['parent'], $output)){
					$output[$value['parent']]['child'][$key] = $value;
					unset($array[$key]);
					if(count($array) != 0){
						$output[$value['parent']]['child'] = self::hierarchize($array, $output[$value['parent']]['child']);
					}
				}
			}

			return $output;
		}

		static function inhierarchize($array, $parent = 0, $output = array()){
			
			foreach ($array as $key => $value) {
				if(isset($value->children)){
					$output += self::inhierarchize($value->children, $value->id, $output);
				}
				if(! array_key_exists($value->id, $output)){
					$output[$value->id] = $parent;
				}
			}

			return $output;
		}

		static function check_name($name){

			$query=$db->prepare('SELECT COUNT(*) AS nbr FROM categories WHERE name =:name');
			$query->bindValue(':name',$name, PDO::PARAM_STR);
			$query->execute();
			$name_free=($query->fetchColumn()==0)?1:0;
			$query->CloseCursor();

			return $name_free;
		}
	}
?>