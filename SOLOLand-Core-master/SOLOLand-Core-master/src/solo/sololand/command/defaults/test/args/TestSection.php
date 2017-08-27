<?php

namespace solo\sololand\command\defaults\test\args;

use pocketmine\command\CommandSender;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class TestSection extends SubCommand{

	public function __construct(){
		parent::__construct("섹션", "섹션의 정보를 확인합니다.");
		$this->setPermission("sololand.command.test.section");
	}

	public function execute(CommandSender $sender, array $args){
		$world = World::getWorld($sender);
		$section = $world->getLandProvider()->getSection($sender);
		
		$information = [];
		$information[] = "섹션의 크기 : " . $section->getWidth() . "x" . $section->getDepth();
		$information[] = "섹션의 위치 : startX:" . $section->getStartX() . ", startZ:" . $section->getStartZ() . ", endX:" . $section->getEndX() . ", endZ" . $section->getEndZ();
		$information[] = "섹션에 등록된 땅 목록 : " . implode(", ", $section->getLands());
		Message::info($sender, "섹션 정보", $information);
		return true;
	}
}