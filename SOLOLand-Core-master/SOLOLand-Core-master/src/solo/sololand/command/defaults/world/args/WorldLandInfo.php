<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class WorldLandInfo extends SubCommand{
	
	public function __construct(){
		parent::__construct("땅정보", "월드 내 땅 관련 정보를 확인합니다.");
		$this->setPermission("sololand.command.world.landinfo");
	}
	
	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$information = [];
		$isToggled = function(bool $status) { return $status ? "§a" : "§7"; };
		$properties = $world->getLandProperties();
		$line = $isToggled($properties->isAllowCreate()) . "(생성)  ";
		$line .= $isToggled($properties->isAllowResize()) . "(확장/축소)  ";
		$line .= $isToggled($properties->isAllowCombine()) . "(합치기)";
		$information[] = "땅 설정값 : " . $line;
		$information[] = "기본 땅 가격 : " . $properties->getDefaultPrice();
		$information[] = "블럭당 가격 : " . $properties->getPricePerBlock();
		$information[] = "1인당 최대 갯수 : " . $properties->getMaxCountPerPlayer();
		$information[] = "한 변당 최소/최대 길이 : " . $properties->getMinLength() . " ~ " . $properties->getMaxLength();
		
		$information[] = "------------------------------------";
		
		$all = 0;
		$sail = 0;
		$notOwned = 0;
		$owned = 0;
		foreach($world->getLandProvider()->getLands() as $land){
			++$all;
			if($land->isSail()){
				++$sail;
			}
			if($land->hasOwner()){
				++$owned;
			}else{
				++$notOwned;
			}
		}
		$information[] = "전체 땅 갯수 : " . $all . "개";
		$information[] = "유저가 소유중인 땅 갯수 : " . $owned . "개";
		$information[] = "판매중인 땅 갯수 : " . $sail . "개";
		$information[] = "주인이 없는 땅 갯수 : " . $notOwned . "개";
		Message::info($sender, $world->getName() . " 월드 " . $this->getName(). " 정보", $information);
		return true;
	}
}