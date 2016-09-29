<?php
	require_once('model.php'); 

	class Fiche extends Model{
		var $id;
		var $title;
		var $body;
		var $date;
		var $categories;

		function get_all(){
			$query = $this->db->query('SELECT * FROM fiches');
			$output = $query->fetchAll();
			$query->CloseCursor();

			foreach ($output as $key => $value) {
				$query = $this->db->prepare('SELECT c.id as cid, c.name as cname FROM fiche_category as fc INNER JOIN categories as c ON fc.category_id = c.id WHERE fc.fiche_id = :id ORDER BY cid');
				$query->bindValue(':id', $value['id'], PDO::PARAM_INT);
				$query->execute();
				$output[$key]['categories'] = array_map('reset', $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC));
				$query->CloseCursor();
			}
			return $output;
		}

		function get_all_by_categories($categories){
			$condition = implode(',', array_fill(0, count($categories), '?'));
			$query = $this->db->prepare('SELECT DISTINCT * FROM fiches as f INNER JOIN fiche_category as fc ON fc.fiche_id = f.id WHERE fc.category_id IN(' . $condition . ') GROUP BY f.id');
			foreach ($categories as $key => $category){
    			$query->bindValue(($key+1), $category);
			}
			$query->execute();
			$output = $query->fetchAll();
			$query->CloseCursor();

			foreach ($output as $key => $value) {
				$query = $this->db->prepare('SELECT c.id as cid, c.name as cname FROM fiche_category as fc INNER JOIN categories as c ON fc.category_id = c.id WHERE fc.fiche_id = :id ORDER BY cid');
				$query->bindValue(':id', $value['id'], PDO::PARAM_INT);
				$query->execute();
				$output[$key]['categories'] = array_map('reset', $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC));
				$query->CloseCursor();
			}
			return $output;
		}

		function get($id){
			$query = $this->db->prepare('SELECT * FROM fiches WHERE id = :id ');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$output = $query->fetch();
			$query->CloseCursor();

			$this->id = $output['id'];
			$this->title = $output['title'];
			$this->body = $output['body'];
			$this->date = $output['date'];

			
			$query = $this->db->prepare('SELECT c.id as cid, c.name as cname  FROM fiche_category as fc INNER JOIN categories as c ON fc.category_id = c.id WHERE fc.fiche_id = :id ORDER BY cid');
			$query->bindValue(':id', $output['id'], PDO::PARAM_INT);
			$query->execute();
			$this->categories = array_map('reset', $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC));
			$query->CloseCursor();

			return $this;
		}

		function insert($title, $body, $categories = array()){
			$query = $this->db->prepare('INSERT INTO fiches(title, body) VALUES(:title, :body)');
			$query->bindValue(':title', $title, PDO::PARAM_STR);
			$query->bindValue(':body', $body, PDO::PARAM_STR);
			$query->execute();
			$fiche_id = $this->db->lastInsertId();
			$query->CloseCursor();

			foreach ($categories as $key => $category) {
				$query = $this->db->prepare('INSERT INTO fiche_category(fiche_id, category_id) VALUES(:fiche_id, :category_id)');
				$query->bindValue(':fiche_id', $fiche_id, PDO::PARAM_INT);
				$query->bindValue(':category_id', $category, PDO::PARAM_INT);
				$query->execute();
				$query->CloseCursor();
			}
		}

		function update($id, $title, $body, $categories = array()){
			$query = $this->db->prepare('UPDATE fiches SET title = :title, body = :body WHERE id = :id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->bindValue(':title', $title, PDO::PARAM_STR);
			$query->bindValue(':body', $body, PDO::PARAM_STR);
			$query->execute();
			$fiche_id = $this->db->lastInsertId();
			$query->CloseCursor();

			$query = $this->db->prepare('DELETE FROM fiche_category WHERE fiche_id = :id');			
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();

			foreach ($categories as $key => $category) {
				$query = $this->db->prepare('INSERT INTO fiche_category(fiche_id, category_id) VALUES(:fiche_id, :category_id)');
				$query->bindValue(':fiche_id', $id, PDO::PARAM_INT);
				$query->bindValue(':category_id', $category, PDO::PARAM_INT);
				$query->execute();
				$query->CloseCursor();
			}
		}

		function delete($id){
			$query = $this->db->prepare('DELETE FROM fiches WHERE id = :id');			
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();

			$query = $this->db->prepare('DELETE FROM fiche_category WHERE fiche_id = :id');			
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$query->CloseCursor();
		}
	}
?>