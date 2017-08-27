<?php

namespace solo\sololand\command\defaults\land;

use solo\sololand\command\MainCommand;
use solo\sololand\command\defaults\land\args\{
	LandAccess,
	LandAllowPVP,
	LandAllowPickupItem,
	LandBuy,
	LandCancel,
	LandCancelSell,
	LandCancelShare,
	LandCombine,
	LandCreate,
	LandExpand,
	LandGive,
	LandInfo,
	LandLeave,
	LandList,
	LandMove,
	LandReduce,
	LandRemove,
	LandSell,
	LandSellList,
	LandSetSpawn,
	LandShare,
	LandShareList,
	LandVisitor,
	LandWelcomeMessage,
	LandWelcomeParticle
};

class LandCommand extends MainCommand{

	public function __construct(){
		parent::__construct("땅", "땅을 관리하는 명령어 입니다.");
		$this->setPermission("sololand.command.land");
		
		
		
		$this->registerSubCommand(new LandCreate());
		$this->registerSubCommand(new LandInfo());
		$this->registerSubCommand(new LandMove());
		$this->registerSubCommand(new LandRemove());
		$this->registerSubCommand(new LandList());
		$this->registerSubCommand(new LandShare());
		$this->registerSubCommand(new LandLeave());
		$this->registerSubCommand(new LandWelcomeMessage());
		$this->registerSubCommand(new LandWelcomeParticle());
		$this->registerSubCommand(new LandSetSpawn());
		$this->registerSubCommand(new LandAccess());
		$this->registerSubCommand(new LandAllowPVP());
		$this->registerSubCommand(new LandAllowPickupItem());
		//$this->registerSubCommand(new LandExpand());
		//$this->registerSubCommand(new LandReduce());
		//$this->registerSubCommand(new LandCombine());
		$this->registerSubCommand(new LandBuy());
		$this->registerSubCommand(new LandSell());
		$this->registerSubCommand(new LandSellList());
		$this->registerSubCommand(new LandCancelSell());
		$this->registerSubCommand(new LandCancelShare());
		$this->registerSubCommand(new LandGive());
		$this->registerSubCommand(new LandShareList());
		$this->registerSubCommand(new LandVisitor());
		$this->registerSubCommand(new LandCancel());
	}
}