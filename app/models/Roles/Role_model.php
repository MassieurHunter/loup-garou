<?php

/**
 * Class Role_model
 *
 * @property Log_model $_log
 * @property Player_model $_player_model
 * @property Role_model $subModel
 *
 */
class Role_model extends MY_Model
{


	const LOUP = 1;
	const VOLEUR = 2;
	const NOISEUSE = 3;
	const TANNEUR = 4;
	const SOULARD = 5;
	const INSOMNIAQUE = 6;
	const VOYANTE = 7;
	const DOPPELGANGER = 8;
	const SBIRE = 9;
	const CHASSEUR = 10;
	const FRANC_MACON = 11;
	const VILLAGEOIS = 12;

	/**
	 * @var string
	 */
	public $table = 'roles';
	/**
	 * @var string
	 */
	public $primary_key = 'roleUid';
	/**
	 * @var array
	 */
	public $basics = [
		'roleUid' => 'getRoleUid',
		'name' => 'getName',
		'description' => 'getDescription',
		'model' => 'getModel',
		'firstAction' => 'hasFirstAction',
		'firstActionName' => 'getFirstActionName',
		'firstActionPassive' => 'isFirstActionPassive',
		'firstActionNbTargets' => 'getFirstActionNbTargets',
		'firstActionTargetType' => 'getFirstActionTargetType',
		'secondAction' => 'hasSecondAction',
		'secondActionNeedFailedFirst' => 'isSecondActionNeedFailedFirst',
		'secondActionName' => 'getSecondActionName',
		'secondActionPassive' => 'isSecondActionPassive',
		'secondActionNbTargets' => 'getSecondActionNbTargets',
		'secondActionTargetType' => 'getSecondActionTargetType',
		'loup' => 'isLoup',
		'villageois' => 'isVillageois',
		'tanneur' => 'isTanneur',
		'bootstrapClass' => 'getBootstrapClass',
	];
	/**
	 * @var int
	 */
	protected $roleUid;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $description;
	/**
	 * @var string
	 */
	protected $model;
	/**
	 * @var int
	 */
	protected $nb;
	/**
	 * @var bool
	 */
	protected $loup;
	/**
	 * @var bool
	 */
	protected $tanneur;
	/**
	 * @var bool
	 */
	protected $villageois;
	/**
	 * @var int
	 */
	protected $castingOrder;
	/**
	 * @var int
	 */
	protected $runningOrder;
	/**
	 * @var bool
	 */
	protected $firstAction;
	/**
	 * @var bool
	 */
	protected $firstActionPassive;
	/**
	 * @var string
	 */
	protected $firstActionName;
	/**
	 * @var int
	 */
	protected $firstActionNbTargets;
	/**
	 * @var string
	 */
	protected $firstActionTargetType;
	/**
	 * @var bool
	 */
	protected $secondAction;
	/**
	 * @var bool
	 */
	protected $secondActionPassive;
	/**
	 * @var bool
	 */
	protected $secondActionNeedFailedFirst;
	/**
	 * @var string
	 */
	protected $secondActionName;
	/**
	 * @var int
	 */
	protected $secondActionNbTargets;
	/**
	 * @var string
	 */
	protected $secondActionTargetType;

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return (string)$this->description;
	}

	/**
	 * @param string $description
	 * @return Role_model
	 */
	public function setDescription(string $description): Role_model
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getNb(): int
	{
		return (int)$this->nb;
	}

	/**
	 * @param int $nb
	 * @return Role_model
	 */
	public function setNb(int $nb): Role_model
	{
		$this->nb = $nb;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isVillageois(): bool
	{
		return $this->villageois == true;
	}

	/**
	 * @param bool $villageois
	 * @return Role_model
	 */
	public function setVillageois(bool $villageois): Role_model
	{
		$this->villageois = $villageois;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBootstrapClass(): string
	{
		if ($this->isLoup()) {

			$class = 'dark';

		} elseif ($this->isTanneur()) {

			$class = 'warning';

		} else {

			$class = 'info';

		}

		return $class;
	}

	/**
	 * @return bool
	 */
	public function isLoup(): bool
	{
		return $this->loup == true;
	}

	/**
	 * @param bool $loup
	 * @return Role_model
	 */
	public function setLoup(bool $loup): Role_model
	{
		$this->loup = $loup;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isTanneur(): bool
	{
		return $this->tanneur == true;
	}

	/**
	 * @param bool $tanneur
	 * @return Role_model
	 */
	public function setTanneur(bool $tanneur): Role_model
	{
		$this->tanneur = $tanneur;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCastingOrder(): int
	{
		return (int)$this->castingOrder;
	}

	/**
	 * @param int $castingOrder
	 * @return Role_model
	 */
	public function setCastingOrder(int $castingOrder): Role_model
	{
		$this->castingOrder = $castingOrder;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRunningOrder(): int
	{
		return (int)$this->runningOrder;
	}

	/**
	 * @param int $runningOrder
	 * @return Role_model
	 */
	public function setRunningOrder(int $runningOrder): Role_model
	{
		$this->runningOrder = $runningOrder;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isFirstActionPassive(): bool
	{
		return $this->firstActionPassive == true;
	}

	/**
	 * @return int
	 */
	public function getFirstActionNbTargets(): int
	{
		return (int)$this->firstActionNbTargets;
	}

	/**
	 * @return bool
	 */
	public function isSecondActionNeedFailedFirst(): bool
	{
		return $this->secondActionNeedFailedFirst == true;
	}

	/**
	 * @param bool $secondActionNeedFailedFirst
	 * @return Role_model
	 */
	public function setSecondActionNeedFailedFirst(bool $secondActionNeedFailedFirst): Role_model
	{
		$this->secondActionNeedFailedFirst = $secondActionNeedFailedFirst;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSecondActionPassive(): bool
	{
		return $this->secondActionPassive == true;
	}

	/**
	 * @return int
	 */
	public function getSecondActionNbTargets(): int
	{
		return (int)$this->secondActionNbTargets;
	}

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function firstAction($arguments): array
	{
		$return = [];

		if ($this->getSubmodel()->hasFirstAction()) {
			$return = $this->getSubmodel()->firstAction($arguments);
		}

		return $return;
	}

	/**
	 * @return bool
	 */
	public function hasFirstAction(): bool
	{
		return $this->firstAction == true;
	}

	/**
	 * @return Role_model
	 */
	public function getSubmodel(): Role_model
	{

		if (empty($this->subModel)) {

			$this->load->model('Roles/' . ucfirst($this->getModel()) . '_model', 'subModel');
			$this->subModel->init($this->getRoleUid());

		}

		return $this->subModel;

	}

	/**
	 * @return string
	 */
	public function getModel(): string
	{
		return (string)$this->model;
	}

	/**
	 * @param string $model
	 * @return Role_model
	 */
	public function setModel(string $model): Role_model
	{
		$this->model = $model;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRoleUid(): int
	{
		return (int)$this->roleUid;
	}

	/**
	 * @param int $roleUid
	 * @return Role_model
	 */
	public function setRoleUid(int $roleUid): Role_model
	{
		$this->roleUid = $roleUid;
		return $this;
	}

	/**
	 * @param array $arguments
	 * @return array
	 */
	public function secondAction($arguments): array
	{
		$return = [];

		if ($this->getSubmodel()->hasSecondAction()) {
			$return = $this->getSubmodel()->secondAction($arguments);
		}

		return $return;
	}

	/**
	 * @return bool
	 */
	public function hasSecondAction(): bool
	{
		return $this->secondAction == true;
	}

	/**
	 * @param int $actionNumber
	 * @param array $actionResponse
	 * @return string
	 */
	public function buildActionMessage(int $actionNumber, array $actionResponse): string
	{

			$actionName = $actionNumber === 1 ? $this->getSubmodel()->getFirstActionName() : ($actionNumber === 2 ? $this->getSubmodel()->getSecondActionName(): 'you_did_nothing');

			if ($this->getSubmodel()->getModel() !== 'voyante' || ($this->getSubmodel()->getModel() === 'voyante' && $actionNumber === 2)) {

				$this->logAction($actionNumber, $actionResponse);

			}

			if (isset($actionResponse['result'])) {

				$actionLangKey = $actionName . '_result' . ($actionResponse['result'] === 0 ? '_empty' : ($actionResponse['result'] === 0.5 ? '_half' : ''));
				$actionLang = $this->lang->line($actionLangKey);

				if ($actionResponse['result']) {
					$actionLang = str_replace(['*li_player_1*', '*player_1*'], isset($actionResponse['player_1']) ? ['<li>' . $actionResponse['player_1']['name'] . '</li>', $actionResponse['player_1']['name']] : '', $actionLang);
					$actionLang = str_replace(['*li_player_2*', '*player_2*'], isset($actionResponse['player_2']) ? ['<li>' . $actionResponse['player_2']['name'] . '</li>', $actionResponse['player_2']['name']] : '', $actionLang);
					$actionLang = str_replace(['*li_player_3*', '*player_3*'], isset($actionResponse['player_3']) ? ['<li>' . $actionResponse['player_3']['name'] . '</li>', $actionResponse['player_3']['name']] : '', $actionLang);
					$actionLang = str_replace('*card_1*', isset($actionResponse['card_1']) ? $actionResponse['card_1']['name'] : '', $actionLang);
					$actionLang = str_replace('*card_2*', isset($actionResponse['card_2']) ? $actionResponse['card_2']['name'] : '', $actionLang);
					$actionLang = str_replace('*role_1*', isset($actionResponse['role_1']) ? $actionResponse['role_1']['name'] : '', $actionLang);
					$actionLang = str_replace('*role_2*', isset($actionResponse['role_2']) ? $actionResponse['role_2']['name'] : '', $actionLang);
				}

			} else {

				$actionLang = $this->lang->line($actionName);

			}
			
		return $actionLang;
	}

	/**
	 * @return string
	 */
	public function getFirstActionName(): string
	{
		return (string)$this->firstActionName;
	}

	/**
	 * @return string
	 */
	public function getSecondActionName(): string
	{
		return (string)$this->secondActionName;
	}

	/**
	 * @param string $secondActionName
	 * @return Role_model
	 */
	public function setSecondActionName(string $secondActionName): Role_model
	{

		$this->secondActionName = $secondActionName;

		return $this;

	}

	/**
	 * @param int $actionNumber
	 * @param array $actionInfos
	 */
	protected function logAction(int $actionNumber, array $actionInfos)
	{

		$this->load->model('player_model', '_player_model');
		$gameUid = $actionInfos['gameUid'];

		$targetType = $actionNumber === 1 ? $this->getSubmodel()->getFirstActionTargetType() : $this->getSubmodel()->getSecondActionTargetType();
		$player1 = isset($actionInfos['player_1']['playerUid']) ? $actionInfos['player_1']['playerUid'] : 0;
		$player2 = isset($actionInfos['player_2']['playerUid']) ? $actionInfos['player_2']['playerUid'] : 0;
		$card1 = isset($actionInfos['card_1']['playerUid']) ? $actionInfos['card_1']['playerUid'] : 0;
		$card2 = isset($actionInfos['card_2']['playerUid']) ? $actionInfos['card_2']['playerUid'] : 0;

		$target1 = $targetType === 'player' ? $player1 : ($targetType === 'card' ? $card1 : 0);
		$target2 = $targetType === 'player' ? $player2 : ($targetType === 'card' ? $card2 : 0);

		$target1Role = isset($actionInfos['role_1']['roleUid']) ? $actionInfos['role_1']['roleUid'] : 0;
		$target2Role = isset($actionInfos['role_2']['roleUid']) ? $actionInfos['role_2']['roleUid'] : 0;

		/** @var Player_model $currentPlayer */
		$currentPlayer = $actionInfos['currentPlayer'];

		$this->load->model('log_model', '_log');

		$now = new DateTime();
		$timeStamp = $now->format('Y-m-d H:i:s');

		$this->_log
			->setGameUid($gameUid)
			->setPlayerUid($currentPlayer['playerUid'])
			->setRoleUid($this->getSubmodel()->getRoleUid())
			->setAction($actionNumber === 1 ? $this->getSubmodel()->getFirstActionName() : ($actionNumber === 2 ? $this->getSubmodel()->getSecondActionName(): 'did_nothing'))
			->setTarget1($target1)
			->setTarget2($target2)
			->setTarget1Role($target1Role)
			->setTarget2Role($target2Role)
			->setDate($timeStamp)
			->create();

		unset($this->_log);

	}

	/**
	 * @return string
	 */
	public function getFirstActionTargetType(): string
	{
		return (string)$this->firstActionTargetType;
	}

	/**
	 * @return string
	 */
	public function getSecondActionTargetType(): string
	{
		return (string)$this->secondActionTargetType;
	}

	/**
	 * @param string $actionName
	 * @param Player_model $player
	 * @param Role_model $playerRole
	 * @param Player_model $target1
	 * @param Player_model $target2
	 * @param Role_model $target1Role
	 * @param Role_model $target2Role
	 * @return string
	 */
	public function buildActionSummary(string $actionName, Player_model $player, Role_model $playerRole, Player_model $target1, Player_model $target2, Role_model $target1Role, Role_model $target2Role): string
	{

		$actionLangKey = $actionName . ($actionName ? '_summary' . (!$target1->getPlayerUid() && $this->getModel() !== 'insomniaque' ? '_empty' : '') : '');
		$actionLang = $this->lang->line($actionLangKey);


		$actionLang = str_replace(['*li_player_1*', '*player_1*'], $player->getPlayerUid() ? ['<li>' . $player->getName() . '</li>', $player->getName()] : '', $actionLang);
		$actionLang = str_replace(['*li_player_2*', '*player_2*', '*card_1*'], $target1->getPlayerUid() ? ['<li>' . $target1->getName() . '</li>', $target1->getName(), $target1->getName()] : '', $actionLang);
		$actionLang = str_replace(['*li_player_3*', '*player_3*', '*card_2*'], $target2->getPlayerUid() ? ['<li>' . $target2->getName() . '</li>', $target2->getName(), $target2->getName()] : '', $actionLang);
		$actionLang = str_replace('*role_1*', $playerRole->getRoleUid() ? $playerRole->getName() : '', $actionLang);
		$actionLang = str_replace('*role_2*', $target1Role->getRoleUid() ? $target1Role->getName() : '', $actionLang);
		$actionLang = str_replace('*role_3*', $target2Role->getRoleUid() ? $target2Role->getName() : '', $actionLang);

		return $actionLang;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return (string)$this->name;
	}

	/**
	 * @param string $name
	 * @return Role_model
	 */
	public function setName(string $name): Role_model
	{
		$this->name = $name;
		return $this;
	}


}