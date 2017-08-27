<?php

namespace solo\sololand\command\defaults\world\args;

use pocketmine\Server;
use pocketmine\command\CommandSender;
use pocketmine\level\generator\Generator;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\solocore\util\Message;

class WorldCreate extends SubCommand{

	public function __construct(){
		parent::__construct("생성", "월드를 생성합니다.", null, [
				["월드 이름", "제너레이터(" . implode("/", Generator::getGeneratorList()) . ")", "프리셋"]
		]);
		$this->setInGameOnly(false);
		$this->setPermission("sololand.command.world.create");
	}
	
	public function execute(CommandSender $sender, array $args){
		if(!isset($args[1])){
			return false;
		}
		$worldName = $args[0];
		$generator = $args[1];
		$server = Server::getInstance();
		$level = $server->getLevelByName($worldName);

		if($server->loadLevel($worldName) || $level !== null){
			Message::alert($sender, $args[0] . " (" . $worldName . ") 월드는 이미 생성되어 있습니다.");
			return true;
		}

		//foreach(Generator::getGeneratorList() as $gen){
		//	if(strcasecmp($gen, $generator)){
		//		$generatorClass = Generator::getGenerator($gen);
		//		break;
		//	}
		//}

		$options = [];
		if(isset($args[2])){
			unset($args[0]);
			unset($args[1]);
			$options["preset"] = implode(" ", $args);
		}
		
		$isCreated = World::createWorld($worldName, $generator, $options);
		
		if(!$isCreated){
			Message::alert($sender, "월드 생성에 실패하였습니다. 이미 생성된 월드인지 확인해주세요.");
		}else{
			Message::normal($sender, "성공적으로 " . $worldName . " 월드를 생성하였습니다.");
		}
		return true;
	}
}