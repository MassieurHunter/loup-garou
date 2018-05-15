<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Copyright (C) 2014 @avenirer [avenir.ro@gmail.com]
 * Everyone is permitted to copy and distribute verbatim or modified copies of this license document,
 * and changing it is allowed as long as the name is changed.
 * DON'T BE A DICK PUBLIC LICENSE TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 *
 * **** Do whatever you like with the original work, just don't be a dick.
 * **** Being a dick includes - but is not limited to - the following instances:
 * ******** 1a. Outright copyright infringement - Don't just copy this and change the name.
 * ******** 1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.
 * ******** 1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.
 * **** If you become rich through modifications, related works/services, or supporting the original work, share the love. Only a dick would make loads off this work and not buy the original works creator(s) a pint.
 * **** Code is provided with no warranty.
 * ********** Using somebody else's code and bitching when it goes wrong makes you a DONKEY dick.
 * ********** Fix the problem yourself. A non-dick would submit the fix back.
 *
 * @property \User_model $_oUser
 * @property \CI_DB_result $result
 * @property \CI_DB_query_builder $db
 *
 * @link https://github.com/avenirer/CodeIgniter-MY_Model documentation
 *
 * Heavily modified by Nissa Hunter <nissa@eapc2.com>
 *
 * */
class MY_Model extends CI_Model
{

	const TABLE_FIELD_SEPARATOR  = '_-_';
	const GROUP_CONCAT_SEPARATOR = '|---|';
	const CONCAT_SEPARATOR       = '|-|';
	/** @var null
	 * Sets table name
	 */
	public $table            = NULL;

	/** relationships variables */
	public $translationTable = 'translations';
	/**
	 * @var null
	 * Sets PRIMARY KEY
	 */
	public $primary_key = 'id';
	public $lang        = '';
	/**
	 * 1:1 relationship
	 * example :
	 * [propName] = array(
	 *        'foreign_model' => '',
	 *        'foreign_table_alias' => '',
	 *        'foreign_key' => '',
	 *        'local_key' => '',
	 *        'type' => '', type of JOIN (LEFT,RIGHT)
	 * )
	 *
	 * @var array
	 */
	public $has_one = [];
	/**
	 * 1:n relationship
	 * example :
	 * [propName] = array(
	 *        'foreign_model' => '',
	 *        'foreign_table_alias' => '',
	 *        'foreign_key' => '',
	 *        'local_key' => '',
	 *        'type' => '', type of JOIN (LEFT,RIGHT)
	 * )
	 *
	 * @var array
	 */
	public $has_many = [];
	/**
	 * n:n relationship with pivot table
	 * example :
	 * [propName] = array(
	 *        'foreign_model'=>'',
	 *        'foreign_table_alias'=>'',
	 *        'pivot_table'=>'',
	 *        'pivot_table_alias'=>'',
	 *        'local_key'=>'',
	 *        'pivot_local_key'=>'', this is the related key in the pivot table to the local key
	 *      'pivot_foreign_key'=>'', the same as above, but for foreign table's keys
	 *      'foreign_key'=>'',
	 *        'type' => '', type of JOIN (LEFT,RIGHT)v
	 * )
	 *
	 * @var array
	 */
	public $has_many_pivot = [];
	public $apis           = [];
	public $apiInfos       = [];
	public $veryBasics     = [];
	public $veryBasicInfos = [];
	public $basics         = [];
	public $basicInfos     = [];
	public $advanced       = [];
	public $advancedInfos  = [];
	public $arrFields      = [];
	public $uniqueID       = '';
	/**
	 *
	 * @var float
	 */
	protected $microtime;
	/**
	 *
	 * @var float
	 */
	protected $lastMicrotime;
	/**
	 *
	 * List of fields required to add an entry with the current model
	 *
	 * @var array
	 */
	protected $requiredFields = [];

	public function __construct($arrParams = []) {
		$this->microtime = microtime(true);
		parent::__construct();
		$this->load->library('session');
		$this->load->library('date');
		$this->load->helper(
			[
				'string',
				'text',
				'array',
				'cookie',
				'inflector',
				'date',
			]
		);

		$this->initArrFields();

		if (!empty($arrParams)) {
			if (isset($arrParams['pkValue'])) {
				$this->init($arrParams['pkValue']);
			} elseif (isset($arrParams['properties']) && !empty($arrParams['properties'])) {
				$this->init(false, $arrParams['properties']);
			}
		}
	}

	/**
	 *
	 */
	protected function initArrFields() {
		if ($this->table) {
			$this->arrFields = $this->db->list_fields($this->table);
		}
	}

	/**
	 * Fill the object with the datas collected in the DB
	 *
	 * @param int|bool $pkValue value of the primary key
	 * @param array|object $arrParams array of parama if you don't provide the primary key (faster if you already have the datas from a SQL query )
	 * @return self
	 */
	public function init($pkValue = false, $arrParams = []) {
		/*
		 * If we give the value of the primary key we execute the SQL query to get all the datas
		 */
		if ($pkValue) {
			$this->db->reset_query();
			$select = '';
			$arrManyRelationships = $this->has_many + $this->has_many_pivot;
			$arrFields = [];
			$this->arrFields = $this->db->list_fields($this->table);
			$arrFields[$this->table] = $this->arrFields;

			$this->db->where($this->table . '.' . $this->primary_key, $pkValue);
			/*
			 * Loop for 1:1 JOIN
			 */
			foreach ($this->has_one as $propertyName => $params) {
				$tempChilObjectName = 'temp_' . $propertyName;
				$this->load->model($params['foreign_model'], $tempChilObjectName);
				$this->$propertyName = clone $this->$tempChilObjectName;
				unset($this->$tempChilObjectName);

				/*
				 * Managing alias
				 */
				if (isset($params['foreign_table_alias']) && !empty($params['foreign_table_alias'])) {
					$tableAlias = ' as ' . $params['foreign_table_alias'];
				} else {
					$tableAlias = '';
					$params['foreign_table_alias'] = $this->$propertyName->table;
				}

				if (!isset($params['type'])) {
					$params['type'] = '';
				}

				$arrFields[$params['foreign_table_alias']] = $this->$propertyName->table->getArrFields();
				$this->db->join($this->$propertyName->table . $tableAlias, $this->table . '.' . $params['local_key'] . '=' . $params['foreign_table_alias'] . '.' . $params['foreign_key'], $params['type']);
			}

			/**
			 * Loop for 1:n JOIN
			 */
			foreach ($this->has_many as $propertyName => $params) {
				$this->load->model($params['foreign_model'], 'temp_' . $propertyName);
				/*
				 * Managing alias
				 */
				if (isset($params['foreign_table_alias']) && !empty($params['foreign_table_alias'])) {
					$tableAlias = ' as ' . $params['foreign_table_alias'];
				} else {
					$tableAlias = '';
					$params['foreign_table_alias'] = $this->{'temp_' . $propertyName}->table;
				}

				if (!isset($params['type'])) {
					$params['type'] = '';
				}

				/*
				 * 1:n and n:n child object are stored on an array
				 */
				$this->{'arr' . ucfirst($propertyName)} = [];
				$arrFields[$params['foreign_table_alias']] = $this->{'temp_' . $propertyName}->getArrFields();
				$this->db->join($this->{'temp_' . $propertyName}->table . $tableAlias, $this->table . '.' . $params['local_key'] . '=' . $params['foreign_table_alias'] . '.' . $params['foreign_key'], $params['type']);
			}

			/*
			 * Loop for n:n JOIN with pivot Table
			 */
			foreach ($this->has_many_pivot as $propertyName => $params) {
				$this->load->model($params['foreign_model'], 'temp_' . $propertyName);
				/*
				 * Managing alias
				 */
				if (isset($params['pivot_table_alias']) && !empty($params['pivot_table_alias'])) {
					$pivotAlias = ' as ' . $params['pivot_table_alias'];
				} else {
					$pivotAlias = '';
					$params['pivot_table_alias'] = $params['pivot_table'];
				}

				if (isset($params['foreign_table_alias']) && !empty($params['foreign_table_alias'])) {
					$tableAlias = ' as ' . $params['foreign_table_alias'];
				} else {
					$tableAlias = '';
					$params['foreign_table_alias'] = $this->{'temp_' . $propertyName}->table;
				}

				if (!isset($params['type'])) {
					$params['type'] = '';
				}

				/*
				 * 1:n and n:n child object are stored on an array
				 */
				$this->{'arr' . ucfirst($propertyName)} = [];
				$arrFields[$params['foreign_table_alias']] = $this->{'temp_' . $propertyName}->getArrFields();
				$this->db->join($params['pivot_table'] . $pivotAlias, $this->table . '.' . $params['local_key'] . '=' . $params['pivot_table_alias'] . '.' . $params['pivot_foreign_key'], $params['type']);
				$this->db->join($this->{'temp_' . $propertyName}->table . $tableAlias, $params['pivot_table_alias'] . '.' . $params['pivot_local_key'] . '=' . $params['foreign_table_alias'] . '.' . $params['foreign_key'], $params['type']);
			}


			/*
			 * We add the table name in front of the fieldname to avoid field colisions
			 */
			foreach ($arrFields as $table => $fields) {
				foreach ($fields as &$field) {
					$field = $table . '.' . $field . ' as ' . $table . self::TABLE_FIELD_SEPARATOR . $field;
				}
				$select .= implode(',', $fields) . ',';
			}
			$select = substr($select, 0, -1);


			/*
			 * Executing the created query
			 */
			$arrOProperties = $this->db
				->select($select)
				->from($this->table)
				->get()
				->result();

			foreach ($arrOProperties as $rowNum => $oProperties) {

				foreach ($oProperties as $key => $value) {
					$splitedKey = explode(self::TABLE_FIELD_SEPARATOR, $key);
					$tableName = $splitedKey[0];
					$propName = $splitedKey[1];

					/*
					 * For fields belonging to the Object we fill the properties
					 * Only for first row
					 */
					if ($tableName == $this->table && $rowNum == 0) {
						if (property_exists($this, $propName)) {
							if ($this->{$propName} === null) {
								$this->{$propName} = $value;
							}
						}
						/*
						 * If the fields belong to a child object 
						 * we try to find out wich one 
						 * and we fill the properties
						 */
					} else {
						/*
						 * Finding out the child object
						 */
						if (!isset($joinTable) || $joinTable != $tableName) {
							$joinTable = false;
							$propFound = false;
							$isOne = false;
							$isMany = false;
							/*
							 * filling the 1:1 JOIN only for the first row
							 */
							foreach ($this->has_one as $propertyName => $params) {
								$tableAlias = isset($params['foreign_table_alias']) && !empty($params['foreign_table_alias']) ? $params['foreign_table_alias'] : $this->$propertyName->table;
								if ($tableAlias == $tableName && $rowNum == 0) {
									$joinTable = $tableName;
									$prop = $propertyName;
									$propFound = true;
									$isOne = true;

									break;
								}
							}
							/*
							 * filling 1:n and n:n JOIN
							 */
							foreach ($arrManyRelationships as $propertyName => $params) {
								$randomSuffix = '_' . random_string();
								$tableAlias = isset($params['foreign_table_alias']) && !empty($params['foreign_table_alias']) ? $params['foreign_table_alias'] : $this->{'temp_' . $propertyName}->table;
								$tablePrimaryKey = $this->{'temp_' . $propertyName}->primary_key;
								if ($tableAlias == $tableName) {
									$joinTable = $tableName;
									/*
									 * Setting temp property only on first loop on the child object
									 */
									if (!property_exists($this, 'temp_' . $propertyName . $randomSuffix)) {
										$this->{'temp_' . $propertyName . $randomSuffix} = clone $this->{'temp_' . $propertyName};
									}

									$arrPropName = 'arr' . ucfirst($propertyName);
									$propForeignKey = $tableAlias . self::TABLE_FIELD_SEPARATOR . $tablePrimaryKey;

									/*
									 * if the entry on the array is not setted 
									 * we set it there
									 */

									if (!isset($this->{$arrPropName}[$oProperties->$propForeignKey]) && $oProperties->$propForeignKey) {
										$this->{$arrPropName}[$oProperties->$propForeignKey] = $this->{'temp_' . $propertyName . $randomSuffix};
									}

									$propFound = true;
									$isMany = true;
									break;
								}
							}
						}

						/*
						 * filling the child object
						 */
						if ($joinTable && $joinTable == $tableName && $propFound) {
							if ($isOne) {
								if (property_exists($this->{$prop}, $propName)) {
									if ($this->{$prop}->{$propName} === null) {
										$this->{$prop}->{$propName} = $value;
									}
								}
							} elseif ($isMany) {
								if ($oProperties->$propForeignKey && property_exists($this->{$arrPropName}[$oProperties->$propForeignKey], $propName)) {
									if ($this->{$arrPropName}[$oProperties->$propForeignKey]->{$propName} === null) {
										$this->{$arrPropName}[$oProperties->$propForeignKey]->{$propName} = $value;
									}
								}
							}
						}
					}
				}
			}
			/*
			 * If we give a data array (or an object) we set the properties with it
			 */
		} elseif ($arrParams) {
			foreach ($arrParams as $key => $value) {
				if (property_exists($this, $key)) {
					if ($this->{$key} === null) {
						$this->{$key} = $value;
					}
				}
			}
		}

		return $this;
	}

	/**
	 *
	 * @param string $name
	 * @param mixed $arguments
	 */
	public function __call($name, $arguments) {
		if (method_exists($this, $name)) {
			$this->$name($arguments);
		} else {
			echo "\n***** METHOD " . get_class($this) . '::' . $name . " does not exist! *****\n";
		}
	}

	/**
	 * Return an array with all the field of the database table used by the model
	 * with a table alias
	 *
	 * @param string $alias
	 * @return array
	 */
	public function getArrFieldsWithAlias($alias) {
		$arrFields = $this->getArrFields();
		foreach ($arrFields as $key => $field) {
			$arrFields[$key] = $alias . '.' . $field;
		}

		return $arrFields;
	}

	/**
	 * Return an array with all the field of the database table used by the model
	 *
	 * @return array
	 */
	public function getArrFields() {
		if (empty($this->arrFields)) {
			$this->initArrFields();
		}
		return $this->arrFields;
	}

	/**
	 * Return all the field of the database table used by the model
	 * separated by coma
	 *
	 * @param string $tableAlias
	 * @return string
	 */
	public function getFieldsForSql($tableAlias = null) {
		$arrFields = $this->getArrFields();

		$sqlFields = '';
		$tablePrefix = $tableAlias ? $tableAlias : $this->table;

		foreach ($arrFields as $field) {
			$sqlFields .= $tablePrefix . '.' . $field . ',';
		}

		$sqlFields = substr($sqlFields, 0, -1);

		return $sqlFields;
	}

	/**
	 * Return all the field of the database table used by the model
	 * separated by coma
	 * with a prefix in front of each field
	 *
	 * @param string $prefix
	 * @param string $tableAlias
	 * @return string
	 */
	public function getFieldsForSqlWithPrefix($prefix, $tableAlias = null) {
		$arrFields = $this->getArrFields();
		$arrFieldsPrefix = $this->getArrFieldsWithPrefix($prefix);

		$sqlFields = '';
		$tablePrefix = $tableAlias ? $tableAlias : $this->table;

		foreach ($arrFields as $key => $field) {
			$sqlFields .= $tablePrefix . '.' . $field . ' as "' . $arrFieldsPrefix[$key] . '",';
		}

		$sqlFields = substr($sqlFields, 0, -1);

		return $sqlFields;
	}

	/**
	 * Return an array with all the field of the database table used by the model
	 * with a prefix in front of each field
	 *
	 * @param string $prefix
	 * @return array
	 */
	public function getArrFieldsWithPrefix($prefix) {
		$arrFields = $this->getArrFields();
		foreach ($arrFields as $key => $field) {
			$arrFields[$key] = $prefix . self::TABLE_FIELD_SEPARATOR . $field;
		}

		return $arrFields;
	}

	/**
	 *
	 * @param string $tableAlias
	 * @param boolean $distinct
	 * @param boolean $useGroupSeparator
	 * @return string
	 */
	public function getGroupConcat($tableAlias = null, $distinct = false, $useGroupSeparator = false) {
		$arrFields = $this->getArrFields();

		$groupConcat = $distinct ? 'GROUP_CONCAT(DISTINCT(CONCAT(' : 'GROUP_CONCAT(CONCAT(';
		$tablePrefix = $tableAlias ? $tableAlias : $this->table;

		foreach ($arrFields as $key => $field) {
			$groupConcat .= ($key > 0 ? ', "' . self::CONCAT_SEPARATOR . '",' : '') . 'IFNULL(' . $tablePrefix . '.' . $field . ',"")';
		}

		$groupConcat .= $distinct ? '))' : ')';
		$groupConcat .= $useGroupSeparator ? ' SEPARATOR "' . self::GROUP_CONCAT_SEPARATOR . '")' : ')';


		return $groupConcat;
	}

	/**
	 *
	 * @param string $tableAlias
	 * @return string
	 */
	public function getConcat($tableAlias = false) {
		$arrFields = $this->getArrFields();

		$concat = 'CONCAT(';
		$tablePrefix = $tableAlias ? $tableAlias : $this->table;

		foreach ($arrFields as $key => $field) {
			$concat .= ($key > 0 ? ', "' . self::CONCAT_SEPARATOR . '",' : '') . 'IFNULL(' . $tablePrefix . '.' . $field . ',"")';
		}

		$concat .= ')';

		return $concat;
	}

	/**
	 *
	 * @param array $arrColumns
	 * @param boolean $distinct
	 * @return string
	 */
	public function groupConcatColumns($arrColumns, $distinct = false, $useGroupSeparator = false) {
		$groupConcat = $distinct ? 'GROUP_CONCAT(DISTINCT(CONCAT(' : 'GROUP_CONCAT(CONCAT(';

		foreach ($arrColumns as $key => $field) {
			$groupConcat .= ($key > 0 ? ', "' . self::CONCAT_SEPARATOR . '",' : '') . 'IFNULL(' . $field . ',"")';
		}

		$groupConcat .= $distinct ? '))' : ')';
		$groupConcat .= $useGroupSeparator ? ' SEPARATOR "' . self::GROUP_CONCAT_SEPARATOR . '")' : ')';

		return $groupConcat;
	}

	/**
	 * Alias for getPrimaryKeyValue
	 *
	 * @return int
	 */
	public function getID() {
		return $this->getPrimaryKeyValue();
	}

	/**
	 * Get the value of the primary Key
	 *
	 * @return int
	 */
	public function getPrimaryKeyValue() {
		return $this->{$this->primary_key};
	}

	/**
	 * If the primary key is taken we do an update
	 * if not we create a new line
	 */
	public function saveOrCreate() {
		$checkExists = $this->db
			->select($this->primary_key)
			->from($this->table)
			->where($this->primary_key, $this->getPrimaryKeyValue())
			->get()
			->num_rows();

		if ($checkExists) {
			$this->saveModifications();
		} else {
			$this->create();
		}
	}

	/**
	 * Save the modification of the object in the database
	 * @return boolean TRUE on success, FALSE on failure
	 */
	public function saveModifications() {
		$arrCurrentDatas = $this->db
			->where($this->primary_key, $this->getPrimaryKeyValue())
			->get($this->table)
			->row(0);
		$update = false;

		if (!empty($arrCurrentDatas)) {
			foreach ($arrCurrentDatas as $key => $value) {
				if (property_exists($this, $key) && $this->{$key} !== $value) {
					$this->db->set($key, $this->{$key});
					$update = true;
				}
			}
		}

		if ($update) {
			$res = $this->db
				->where($this->primary_key, $this->getPrimaryKeyValue())
				->update($this->table);
		} else {
			$res = true;
		}
		return $res;
	}

	/**
	 * Create an entry in the database
	 */
	public function create() {
		$arrfields = $this->getArrFields();

		$insert = false;
		foreach ($arrfields as $key => $value) {
			if (property_exists($this, $value)) {
				if ($this->{$value} !== null) {
					$this->db->set($value, $this->{$value});
					$insert = true;
				}
			}
		}

		if ($insert) {
			if ($this->db->insert($this->table)) {
				$primaryKey = $this->primary_key;
				$this->{$primaryKey} = $this->db->insert_id();
				return true;
			}
		}
		return false;
	}

	/**
	 * Gets basic informations of the instance
	 *
	 * @return array
	 */
	public function getInfos($advanced = false) {
		return $advanced ? $this->getAdvanceInfos() : $this->getBasicInfos();
	}

	/**
	 * Gets basic informations of the instance
	 *
	 * @return array
	 */
	public function getBasicInfos() {
		/*
		 * Checks if the primarykey property exist (not badly written)
		 * And if the basicInfos variable exists (not implemented in older models)
		 */
		if (isset($this->{$this->primary_key})) {
			/*
			 * The system checks if the primary key value is not null
			 */
			if ($this->{$this->primary_key} !== null) {
				$this->initBasicInfo();
			}
		}
		return $this->basicInfos;
	}

	/**
	 *
	 */
	public function initBasicInfo() {
		foreach ($this->basics as $attribute => $getter) {
			$this->basicInfos[$attribute] = $this->$getter();
		}
	}

	/**
	 * Gets very basic informations of the instance
	 *
	 * @return array
	 */
	public function getApiInfos() {
		/*
		 * Checks if the primarykey property exist (not badly written)
		 * And if the apiInfos variable exists (not implemented in older models)
		 */
		if (isset($this->{$this->primary_key})) {
			/*
			 * The system checks if the primary key value is not null
			 */
			if ($this->{$this->primary_key} !== null) {
				$this->initApiInfo();
			}
		}
		return $this->apiInfos;
	}

	/**
	 *
	 */
	public function initApiInfo() {
		foreach ($this->apis as $attribute => $getter) {
			$this->apiInfos[$attribute] = $this->$getter();
		}
	}

	/**
	 * Gets very basic informations of the instance
	 *
	 * @return array
	 */
	public function getVeryBasicInfos() {
		/*
		 * Checks if the primarykey property exist (not badly written)
		 * And if the veryBasicInfos variable exists (not implemented in older models)
		 */
		if (isset($this->{$this->primary_key})) {
			/*
			 * The system checks if the primary key value is not null
			 */
			if ($this->{$this->primary_key} !== null) {
				$this->initVeryBasicInfo();
			}
		}
		return !empty($this->veryBasicInfos) ? $this->veryBasicInfos : $this->getBasicInfos();
	}

	/**
	 *
	 */
	public function initVeryBasicInfo() {
		foreach ($this->veryBasics as $attribute => $getter) {
			$this->veryBasicInfos[$attribute] = $this->$getter();
		}
	}

	/**
	 * Gets basic informations of the instance
	 *
	 * @return array
	 */
	public function getAdvancedInfos() {
		/*
		 * Checks if the primarykey property exist (not badly written)
		 * And if the basicInfos variable exists (not implemented in older models)
		 */
		if (isset($this->{$this->primary_key})) {
			/*
			 * The system checks if the primary key value is not null
			 */
			if ($this->{$this->primary_key} !== null) {
				$this->initAdvancedInfo();
			}
		}
		return !empty($this->advancedInfos) ? $this->advancedInfos : $this->getBasicInfos();
	}

	/**
	 *
	 */
	public function initAdvancedInfo() {
		foreach ($this->advanced as $attribute => $getter) {
			$this->advancedInfos[$attribute] = $this->$getter();
		}
	}

	/**
	 *
	 * @return string
	 */
	public function getUniqueID() {
		return $this->uniqueID;
	}

	/**
	 *
	 * @param string $uniqueID
	 * @return \MY_Model
	 */
	public function setUniqueID($uniqueID) {
		$this->uniqueID = $uniqueID;
		return $this;
	}

	/**
	 *
	 * @param boolean $default
	 * @return array
	 */
	public function getSqlInfos($default = false) {
		$arrfields = $this->getArrFields();

		$arrInfos = [];
		foreach ($arrfields as $column) {
			if (property_exists($this, $column)) {
				$arrInfos[$column] = $this->{$column} === NULL && $default ? 'DEFAULT' : $this->{$column};
			}
		}

		return $arrInfos;
	}

	/**
	 * Generic delete method
	 * Delete the line in the database
	 */
	public function delete() {
		$this->db
			->where($this->primary_key, $this->getPrimaryKeyValue())
			->delete($this->table);
	}

	/**
	 * Return execustion time from contruction of the controller
	 *
	 * @param boolean $last (get interval from the last time this method was called)
	 * @return string
	 */
	public function microtimePassed($last = false) {
		$microtimeTest = $last ? $this->lastMicrotime : $this->microtime;
		$microtimePassed = microtime(true) - $microtimeTest;
		$this->lastMicrotime = microtime(true);
		return number_format($microtimePassed, 6, '.', ' ') . ' seconds';
	}

}
