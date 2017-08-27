<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\Server;
use pocketmine\command\CommandSender;

use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class WorldInfo extends SubCommand{
	
	public function __construct(){
		parent::__construct("정보", "월드의 정보를 확인합니다.", ["정보보기"]);
		$this->setPermission("sololand.command.world.info");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$level = $sender->getLevel();
		
		$isToggled = function(bool $status) { return $status ? "§a" : "§7"; };
		$information = [];

		$information[] = "Level 이름 : " . $level->getName() . ", Level 폴더 이름 : " . $level->getFolderName();
		$information[] = "제너레이터 : " . $level->getProvider()->getGenerator() . ", 시드 : " . $level->getSeed();
		$information[] = "전체 " . count(Server::getInstance()->getOnlinePlayers()) . "명의 플레이어 중 " . count($level->getPlayers()) . "명의 플레이어가 " . $world->getName() . " 월드에 있습니다.";
		
		$properties = $world->getWorldProperties();
		$line = $isToggled($properties->isProtected()) . "(보호)  ";
		$line .= $isToggled($properties->isInvenSave()) . "(인벤세이브)  ";
		$line .= $isToggled($properties->isAllowPVP()) . "(PVP)  ";
		$line .= $isToggled($properties->isAllowExplosion()) . "(TNT 블럭 파괴)";
		$information[] = "월드 설정값 : " . $line;
		
		//$properties = $world->getRoomProperties();
		//$line = $isToggled($properties->isAllowCreate()) . "(생성)  ";
		//$line .= "§a(블럭당 가격 : " . $properties->getPricePerBlock() . ")  ";
		//$line .= "§a(땅 1개당 최대 갯수 : " . $properties->getMaxCountPerLand() . ")  ";
		//$line .= "§a(한 변당 최소/최대 길이 : " . $properties->getMinLength() . "/" . $properties->getMaxLength() . ")";
		//$information[] = "방 설정값 : " . $line;
		
		Message::info($sender, $world->getName() . " 월드 정보", $information);
		return true;
	}
}