<?php

namespace solo\sololand\command\defaults\land\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\land\Land;
use solo\sololand\land\Room;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class LandInfo extends SubCommand{

	public function __construct(){
		parent::__construct("정보", "현재 위치에 있는 땅의 정보를 확인합니다.", ["확인", "정보보기"], [
				[],
				["번호"]
		]);
		$this->setPermission("sololand.command.land.info");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$land = null;
		
		if(isset($args[0]) && is_numeric($args[0])){
			$land = $world->getLandProvider()->getLandById((int) $args[0]);
			if($land === null){
				Message::alert($sender, $args[0] . "번 땅은 존재하지 않습니다.");
				return true;
			}
		}else{
			$land = $world->getLandProvider()->getLand($sender);
			if($land === null){
				Message::alert($sender, "현재 위치에서 땅을 찾을 수 없습니다.");
				return true;
			}
		}
		
		$information = [];
		if($land->isSail()){
			$information[] = "이 땅은 현재 판매중입니다.";
			if($land->hasOwner()){
				$information[] = "땅 판매자 : " . $land->getOwner();
			}
			$information[] = "땅 가격 : " . $land->getPrice();
		}else if($land->hasOwner()){
			$information[] = "땅 주인 : " . $land->getOwner();
		}
		$information[] = "땅 크기 : " . $land->getWidth() . "x" . $land->getDepth() . " (넓이 : " . $land->getSize() . " 블럭)";
		$information[] = "땅 공유 목록 : " . (count($land->getMembers()) == 0 ? "없음" : implode(", ", $land->getMembers()));
		if($land->getWelcomeMessage() !== ""){
			$information[] = "땅 환영말 : " . $land->getWelcomeMessage();
		}
		$settingInfo = "땅 설정 정보 : ";
		$settingInfo .= $land->isAllowAccess() ? "§a(출입)  " : "§c(출입)  ";
		$settingInfo .= $land->isAllowPVP() ? "§a(PVP)  " : "§c(PVP)  ";
		$settingInfo .= $land->isAllowPickupItem() ? "§a(아이템줍기)" : "§c(아이템줍기)";
		$information[] = $settingInfo;
		
		if($land->hasRoom()){
			$roomList = [];
			foreach($land->getRooms() as $room){
				$roomList[] = $room->getId() . "번";
			}
			$information[] = "방 목록 : " . implode(", ", $roomList);
		}
		Message::info($sender, $land->getId() . "번 땅 정보", $information);
		return true;
	}
}