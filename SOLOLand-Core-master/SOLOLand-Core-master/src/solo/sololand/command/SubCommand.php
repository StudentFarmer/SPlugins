<?php

namespace solo\sololand\command;

use pocketmine\command\CommandSender;

abstract class SubCommand{

	private $name;
	public $inGameOnly = true;
	public $description;
	public $permission = null;
	public $aliases = [];
	public $params = [];
	
	public function __construct(string $name, string $description = null, array $aliases = null, array $params = null){
		$this->name = $name;
		$this->description = ($description === null) ? "" : $description;
		$this->aliases = ($aliases === null) ? [] : $aliases;
		$this->params = ($params === null) ? [] : $params;
	}
	
	public function getName() : string{
		return $this->name;
	}

	public function isInGameOnly() : bool{
		return $this->inGameOnly;
	}
	
	public function setInGameOnly(bool $inGameOnly){
		$this->inGameOnly = $inGameOnly;
	}
	
	public function getAliases() : array{
		return $this->aliases;
	}
	
	public function setAliases(array $aliases){
		$this->aliases = $aliases;
	}
	
	public function getParameters() : array{
		return $this->params;
	}
	
	public function setParameters(array $params){
		$this->params = $params;
	}
	
	public function getDescription() : string{
		return $this->description;
	}
	
	public function setDescription(string $description){
		$this->description = $description;
	}
	
	public function getPermission() : string{
		if($this->permission !== null){
			return $this->permission;
		}
		return "default";
	}
	
	public function setPermission(string $permission){
		$this->permission = $permission;
	}

	public abstract function execute(CommandSender $sender, array $args);

}