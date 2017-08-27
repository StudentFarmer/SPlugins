<?php

namespace solo\sololand\handler\player;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\level\particle\GenericParticle;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

use solo\sololand\world\World;
use solo\sololand\event\land\LandEnterEvent;
use solo\sololand\event\land\LandLeaveEvent;
use solo\sololand\event\room\RoomEnterEvent;
use solo\sololand\event\room\RoomLeaveEvent;
use solo\solocore\util\Message;
use solo\solocore\event\player\PlayerFloorMoveEvent;

class PlayerFloorMoveHandler implements Listener{

	public $previousLandList = [];
	public $previousRoomList = [];
	
	private function clear(Player $player){
		unset($this->previousLandList[$player->getName()]);
		unset($this->previousRoomList[$player->getName()]);
	}
	
	public function handleQuit(PlayerQuitEvent $event){
		$this->clear($event->getPlayer());
	}
	
	public function handleLevelChange(EntityLevelChangeEvent $event){
		if($event->getEntity() instanceof Player){
			$this->clear($event->getEntity());
		}
	}
	
	public function handle(PlayerFloorMoveEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$world = World::getWorld($player);
		
		$land = $world->getLandProvider()->getLand($event->getTo());
		$previousLand = $this->previousLandList[$name] ?? null;
	
		//land process START
		if($land !== null){ // if land is exist at player's position
				
			//room process START
			$room = $land->getRoom($event->getTo());
			$previousRoom = $this->previousRoomList[$name] ?? null;
			if($room !== null){ // if room is exist at player's position
				if($previousRoom === null || $previousRoom->getId() !== $room->getId()){
					
					//room welcomeMessage 
					$welcomeMessage = "[" . $room->getId() . "번 방] ";
					if(!$room->hasOwner()){
						$welcomeMessage .= "주인이 없습니다";
					}else if($room->isOwner($player)){
						$welcomeMessage .= "본인의 방입니다";
					}else if($room->isMember($player)){
						$welcomeMessage .= $room->getOwner() . "님으로 부터 공유받은 방입니다";
					}else{
						$welcomeMessage .= $room->getOwner() . "님의 방입니다";
					}
					if($room->getWelcomeMessage() !== ""){
						$welcomeMessage .= "\n" . $room->getWelcomeMessage();
					}
					if($room->isSail()){
						$welcomeMessage .= "\n현재 " . $room->getPrice() . "원에 판매중입니다";
					}
						
					//call RoomEnterEvent
					$roomEnterEv = new RoomEnterEvent($player, $room, $welcomeMessage);
					Server::getInstance()->getPluginManager()->callEvent($roomEnterEv);
					if(! $roomEnterEv->isCancelled()){
						Message::normal($player, $roomEnterEv->getWelcomeMessage(), Message::TYPE_TIP);
						$this->previousRoomList[$name] = $room;
					}else{
						$event->setCancelled();
					}
				}
			}else if($previousRoom !== null){
				$roomLeaveEv = new RoomLeaveEvent($player, $previousRoom, $previousRoom->getId() . "번 방에서 나갔습니다");
				Server::getInstance()->getPluginManager()->callEvent($roomLeaveEv);
				if(!$roomLeaveEv->isCancelled()){
					Message::normal($player, $roomLeaveEv->getLeaveMessage(), Message::TYPE_TIP);
					unset($this->previousRoomList[$name]);
				}else{
					$event->setCancelled();
				}
			}
			//room process END
				
				
				
			if($previousLand === null || $previousLand->getId() !== $land->getId()){
	
				//welcomeMessage part
				$welcomeMessage = "[" . $land->getId() . "번 땅] ";
				if(!$land->hasOwner()){
					$welcomeMessage .= "주인이 없습니다";
				}else if($land->isOwner($player)){
					$welcomeMessage .= "본인의 땅입니다";
				}else if($land->isMember($player)){
					$welcomeMessage .= $land->getOwner() . "님으로 부터 공유받은 땅입니다";
				}else{
					$welcomeMessage .= $land->getOwner() . "님의 땅입니다";
				}
				if($land->getWelcomeMessage() !== ""){
					$welcomeMessage .= "\n" . $land->getWelcomeMessage();
				}
				if($land->isSail()){
					$welcomeMessage .= "\n현재 " . $land->getPrice() . "원에 판매중입니다";
				}
	
				//call LandEnterEvent
				$landEnterEv = new LandEnterEvent($player, $land, $welcomeMessage, $land->getWelcomeParticle());
				Server::getInstance()->getPluginManager()->callEvent($landEnterEv);
	
				if(!$landEnterEv->isCancelled()){
					Message::normal($player, $landEnterEv->getWelcomeMessage(), Message::TYPE_TIP);
						
					//particle process
					$particleId = $landEnterEv->getWelcomeParticle();
					if($particleId > 0 && $particleId < 41){ // is valid particle...?
						$vec = new Vector3($event->getTo()->x + 0.5, $event->getTo()->y + 1, $event->getTo()->z + 0.5);
						$particle = new GenericParticle($vec, $particleId);
						$random = new Random((int) (microtime(true) * 1000) + mt_rand());
						for($i = 0; $i < 16; $i++){
							$particle->setComponents(
									$vec->x + ($random->nextSignedFloat() * 2 - 1) * 0.5,
									$vec->y + ($random->nextSignedFloat() * 2 - 1) * 1,
									$vec->z + ($random->nextSignedFloat() * 2 - 1) * 0.5
								);
							$player->getLevel()->addParticle($particle);
						}
					}
					$this->previousLandList[$name] = $land;
				}else{
					$event->setCancelled();
				}
			}
		}else if($previousLand !== null){
			$landLeaveEv = new LandLeaveEvent($player, $previousLand, $previousLand->getId() . "번 땅에서 나갔습니다");
			Server::getInstance()->getPluginManager()->callEvent($landLeaveEv);
			if(!$landLeaveEv->isCancelled()){
				Message::normal($player, $landLeaveEv->getLeaveMessage(), Message::TYPE_TIP);
				unset($this->previousLandList[$name]);
			}else{
				$event->setCancelled();
			}
		}
		//land process END
	}
}