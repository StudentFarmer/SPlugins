<?php

namespace solo\sololand\command\defaults\world;

use solo\sololand\command\MainCommand;
use solo\sololand\command\defaults\world\args\{
	WorldAllowCombineLand,
	WorldAllowCreateLand,
	WorldAllowExplosion,
	WorldAllowPVP,
	WorldAllowResizeLand,
	WorldCreate,
	WorldDefaultLandPrice,
	WorldInfo,
	WorldInvensave,
	WorldLandInfo,
	WorldList,
	WorldLoad,
	WorldMaxLandCount,
	WorldMaxLandLength,
	WorldMinLandLength,
	WorldMove,
	WorldPricePerBlock,
	WorldProtect,
	WorldSetSpawn
};

class WorldCommand extends MainCommand{
	
	public function __construct(){
		parent::__construct("월드", "월드를 관리하는 명령어 입니다.");
		$this->setPermission("sololand.command.world");
		
		

		$this->registerSubCommand(new WorldCreate());
		$this->registerSubCommand(new WorldLoad());
		$this->registerSubCommand(new WorldList());
		$this->registerSubCommand(new WorldInfo());
		$this->registerSubCommand(new WorldLandInfo());
		$this->registerSubCommand(new WorldMove());
		$this->registerSubCommand(new WorldSetSpawn());
		$this->registerSubCommand(new WorldProtect());
		$this->registerSubCommand(new WorldAllowExplosion());
		$this->registerSubCommand(new WorldAllowPVP());
		$this->registerSubCommand(new WorldInvensave());
		$this->registerSubCommand(new WorldAllowCreateLand());
		$this->registerSubCommand(new WorldDefaultLandPrice());
		$this->registerSubCommand(new WorldMaxLandCount());
		$this->registerSubCommand(new WorldMaxLandLength());
		$this->registerSubCommand(new WorldMinLandLength());
		$this->registerSubCommand(new WorldPricePerBlock());
		$this->registerSubCommand(new WorldAllowResizeLand());
		$this->registerSubCommand(new WorldAllowCombineLand());
	}

}