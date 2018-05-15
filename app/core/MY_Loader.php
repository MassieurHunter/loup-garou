<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Extension of CI_Loader wich give us the possibility to use params in models constructors
 *
 * @author Massieur Hunter
 */
class MY_Loader extends CI_Loader
{

	/**
	 * Model Loader
	 *
	 * Loads and instantiates models.
	 *
	 * @param	string	$model		Model name
	 * @param	string	$name		An optional object name to assign to
	 * @param	bool	$db_conn	An optional database connection configuration to initialize
	 * @param	array	$params		array of parametters for the constructor
	 * @return	object
	 */
	public function model($model, $name = '', $db_conn = FALSE, $params = array()) {
		if (empty($model)) {
			return $this;
		} elseif (is_array($model)) {
			foreach ($model as $key => $value) {
				is_int($key) ? $this->model($value, '', $db_conn) : $this->model($key, $value, $db_conn);
			}

			return $this;
		}

		$path = '';

		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (($last_slash = strrpos($model, '/')) !== FALSE) {
			// The path is in front of the last slash
			$path = substr($model, 0, ++$last_slash);

			// And the model name behind it
			$model = substr($model, $last_slash);
		}

		if (empty($name)) {
			$name = $model;
		}

		if (in_array($name, $this->_ci_models, TRUE)) {
			return $this;
		}

		$CI = & get_instance();
		if (isset($CI->$name)) {
			throw new RuntimeException('The model name you are loading is the name of a resource that is already being used: ' . $name);
		}

		if ($db_conn !== FALSE && !class_exists('CI_DB', FALSE)) {
			if ($db_conn === TRUE) {
				$db_conn = '';
			}

			$this->database($db_conn, FALSE, TRUE);
		}

		if (!class_exists('CI_Model', FALSE)) {
			load_class('Model', 'core');
		}

		$model = ucfirst($model);
		if (!class_exists($model)) {
			foreach ($this->_ci_model_paths as $mod_path) {
				if (!file_exists($mod_path . 'models/' . $path . $model . '.php')) {
					continue;
				}

				require_once($mod_path . 'models/' . $path . $model . '.php');
				if (!class_exists($model, FALSE)) {
					throw new RuntimeException($mod_path . "models/" . $path . $model . ".php exists, but doesn't declare class " . $model);
				}

				break;
			}

			if (!class_exists($model, FALSE)) {
				throw new RuntimeException('Unable to locate the model you have specified: ' . $model);
			}
		} elseif (!is_subclass_of($model, 'CI_Model')) {
			throw new RuntimeException("Class " . $model . " already exists and doesn't extend CI_Model");
		}

		$this->_ci_models[] = $name;
		if (isset($params) && !empty($params)) {
			$CI->$name = new $model($params);
		} else {
			$CI->$name = new $model();
		}
		return $this;
	}

}