<?php

require_once 'libs/paloSantoForm.class.php';
require_once 'libs/paloSantoGrid.class.php';

class Controller {

	protected $pDB;
	protected $smarty;
	protected $module_name;
	protected $local_templates_dir;
	protected $grid;
	protected $form;
	protected $base_url;
	protected $themes;

	function __construct($pDB, $smarty, $module_name, $local_templates_dir){
		$this->pDB = $pDB;
		$this->smarty = $smarty;
		$this->module_name = $module_name;
		$this->local_templates_dir = $local_templates_dir;
		$this->grid = new paloSantoGrid($smarty);
		$this->base_url = $smarty->get_template_vars('WEBPATH');
		$this->themes = $smarty->get_template_vars('THEMENAME');
	}

	protected function load_model($class_name, $object=null){
		if ($object === null) {
			$object = $class_name;
		}
		$path = "modules/$this->module_name/models/$class_name.php";
		if (file_exists($path)) {
			include_once $path;

			$this->{$object} = new $class_name($this->pDB);
		} else {
			echo "Model Class doesn't exist";
			return false;
		}
	}

	protected function view ($filename, $data=array()) {
		$path = "modules/$this->module_name/views/$filename.php";
		if (file_exists($path)) {

			foreach ($data as $key => $value) {
				$$key = $value;
			}
			
			$themes = $this->themes;
			$base_url = $this->base_url;
			$module_name = $this->module_name;

			ob_start();
			eval('?>'.file_get_contents($path));
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		} else {
			echo "file $filename.tpl doesn't exist";
			return false;
		}
	}

	protected function load_view($filename, $filter = true){

		$path = "modules/$this->module_name/views/$filename.tpl";
		if (file_exists($path)) {
			if (!$default) {
				$this->grid->setTplFile($path);
			} else {
				$this->grid->showFilter($path);
			}
			$this->grid->fetchGrid();
		} else {
			echo "file $filename.tpl doesn't exist";
			return false;
		}
	}

	protected function create_form($formCampaign=null){
		$this->form = new paloForm($this->smarty, $formCampaign);
	}

	protected function load_form($filename, $title, $data){
		$path = "modules/$this->module_name/views/$filename.tpl";
		if (file_exists($path)) {
			return $this->form->fetchForm($path, $title, $data);
		} else {
			echo "file $filename.tpl doesn't exist";
			return false;
		}
	}

}