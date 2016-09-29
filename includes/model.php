<?php
	class Model{
		protected $db;

		public function __construct(){
	      	try{
				$this->db= new PDO('mysql:host=localhost;dbname=gladys','root','');
			}
			catch (Exception $e){
				die('Erreur : ' . $e->getMessage());
			}   
	   	}		
	}     
?>
