<?php

namespace solo\sololand\command\defaults\test\args;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use solo\sololand\Main;
use solo\sololand\command\SubCommand;
use solo\sololand\world\World;
use solo\sololand\land\Land;
use solo\solocore\util\Debug;
use solo\solocore\util\Message;

class TestLand extends SubCommand{

	public function __construct(){
		parent::__construct("땅", "땅을 대량 생산합니다. 주의! 최초 1회만 사용하시기 바랍니다.");
		$this->setPermission("sololand.command.test.land");
		$this->setInGameOnly(false);
	}

	public function execute(CommandSender $sender, array $args){
		$world =  World::getWorld($sender instanceof Player ? $sender : Server::getInstance()->getDefaultLevel());

		$players = [
				"alex",
				"steve",
				"solo",
				"nvidia",
				"intel",
				"amd",
				"samsung",
				"lg",
				"sony",
				"pantech",
				"hitachi",
				"lenovo",
				"dell",
				"hp",
				"asus",
				"toshiba",
				"gigabyte",
				"sharp",
				"abko",
				"help_this_company",
				"google",
				"apple",
				"facebook",
				"oracle",
				"alphago",
				"pocketmine",
				"genisys",
				"nukkit"
		];
		$playersEndIndex = count($players) - 1;
		
		$count = 0;
		for($x = 1; $x < 100; $x++){
			for($z = 1; $z < 100; $z++){
				$land = new Land($world->getLandProvider()->getNextLandId());
				$land->startX = $x * 6 - 5;
				$land->startZ = $z * 6 - 5;
				$land->endX = $x * 6;
				$land->endZ = $z * 6;
				$land->setOwner($players[mt_rand(0, $playersEndIndex)]);
				$land->setSpawnPoint(new Vector3($x + 3, 128, $z + 3));
				$world->getLandProvider()->addLand($land);
				
				++$count;
				if($count % 500 == 0){
					Debug::normal(Main::getInstance(), "땅 생성중... " . $count . "개");
				}
			}
		}
		Message::normal($sender, "땅이 " . $count . "개 생성되었습니다.");
		Debug::normal(Main::getInstance(), "땅이 " . $count . "개 생성되었습니다.");
		return true;
	}
}