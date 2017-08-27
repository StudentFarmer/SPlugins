<?php

namespace solo\sololand\command\defaults\island;

use solo\sololand\world\World;
use solo\sololand\command\MainCommand;
use solo\sololand\command\defaults\island\args\{
	IslandBuy,
	IslandList,
	IslandMove
};

class IslandCommand extends MainCommand{
	
	public function __construct(World $world){
		parent::__construct($world->getName(), "섬 월드 명령어입니다.");
		$this->setPermission("sololand.command.island");
		
		
		
		$this->registerSubCommand(new IslandBuy($world));
		$this->registerSubCommand(new IslandList($world));
		$this->registerSubCommand(new IslandMove($world));
	}
}