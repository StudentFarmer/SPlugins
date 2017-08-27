<?php

namespace solo\sololand\land;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use solo\sololand\math\Square;
use solo\sololand\util\Serializable;

class Land extends Square implements Serializable{

	private $id;
	public $owner = "";
	public $members = [];
	
	public $price = -1;
	
	public $spawnPoint;
	public $allowPVP = false;
	public $allowAccess = true;
	public $allowPickupItem = false;
	
	public $welcomeMessage = "";
	public $welcomeParticle = 0;
	
	public $rooms = [];

	
	public function __construct(int $id, Square $square = null){
		$this->id = $id;
		if($square !== null){
			$this->set($square);
		}
	}
	
	public function getId() : int{
		return $this->id;
	}

	public function isSail() : bool{
		return $this->price >= 0;
	}

	public function getPrice(){
		return $this->price;
	}
	
	public function setPrice($price){
		$this->price = $price < 0 ? -1 : $price;
	}
	
	public function hasOwner() : bool{
		return $this->owner !== "";
	}

	public function getOwner() : string{
		return $this->owner;
	}
	
	public function setOwner($player){
		if($player instanceof CommandSender){
			$player = $player->getName(); 
		}
		$player = strtolower($player);
		
		$this->owner = $player;
		if(isset($this->members[$this->owner])){
			unset($this->members[$this->owner]);
		}
	}
	
	public function isOwner($player) : bool{
		if($player instanceof CommandSender){
			$player = $player->getName();
		}
		$player = strtolower($player);
		
		return $this->owner === $player;
	}

	public function getMembers() : array{
		return $this->members;
	}
	
	public function setMembers(array $members){
		$arr = [];
		foreach($members as $member){
			if($member instanceof CommandSender){
				$member = $member->getName();
			}
			$arr[strtolower($member)] = $member;
		}
		$this->members = $arr;
	}
	
	public function isMember($player) : bool{
		if($player instanceof CommandSender){
			$player = $player->getName();
		}
		$player = strtolower($player);
		
		return isset($this->members[$player]);
	}
	
	public function addMember($player) : bool{
		if($player instanceof CommandSender){
			$player = $player->getName();
		}
		
		if(!isset($this->members[strtolower($player)])){
			$this->members[strtolower($player)] = $player;
			return true;
		}
		return false;
	}
	
	public function removeMember($player) : bool{
		if($player instanceof CommandSender){
			$player = $player->getName();
		}
		$player = strtolower($player);
		
		if(isset($this->members[$player])){
			unset($this->members[$player]);
			return true;
		}
		return false;
	}

	public function getSpawnPoint() : Vector3{
		return $this->spawnPoint;
	}
	
	public function setSpawnPoint(Vector3 $vec){
		$this->spawnPoint = new Vector3($vec->x, $vec->y, $vec->z);
	}

	public function setAllowPVP(bool $bool){
		$this->allowPVP = $bool;
	}
	
	public function isAllowPVP() : bool{
		return $this->allowPVP;
	}

	public function setAllowAccess(bool $bool){
		$this->allowAccess = $bool;
	}
	
	public function isAllowAccess() : bool{
		return $this->allowAccess;
	}

	public function setAllowPickupItem(bool $bool){
		$this->allowPickupItem = $bool;
	}
	
	public function isAllowPickupItem() : bool{
		return $this->allowPickupItem;
	}
	
	public function setWelcomeMessage(string $message){
		$this->welcomeMessage = $message;
	}
	
	public function getWelcomeMessage() : string{
		return $this->welcomeMessage;
	}

	public function setWelcomeParticle(int $particle){
		$this->welcomeParticle = $particle;
	}
	
	public function getWelcomeParticle() : int{
		return $this->welcomeParticle;
	}
	
	
	
	
	//Room part
	public function getNextRoomId() : int{
		$c = 0;
		while(isset($this->rooms[++$c])){
			//~~~~~~~~//
		}
		return $c;
	}
	
	public function addRoom(Room $room){
		$this->rooms[$room->getId()] = $room;
	}
	
	public function hasRoom() : bool{
		return count($this->rooms) != 0;
	}
	
	public function getRoom($object){
		if(is_int($object)){
			return $this->rooms[$num] ?? null;
		}else if($object instanceof Vector3){
			foreach($this->rooms as $room){
				if($room->isInside($object)){
					return $room;
				}
			}
		}
		return null;
	}
	
	public function getRooms($condition = null) : array{
		if($condition === null){
			return $this->rooms;
		}else{
			$ret = [];
			foreach($this->rooms as $room){
				if($condition($room)){
					$ret[$room->getId()] = $room;
				}
			}
			return $ret;
		}
	}
	
	public function removeRoom($room) : bool{
		$id;
		if($room instanceof Room){
			$id = $room->getId();
		}else if(is_int($room)){
			$id = $room;
		}else{
			return false;
		}
		if(isset($this->rooms[$id])){
			unset($this->rooms[$id]);
			return true;
		}
		return false;
	}
	
	public function clearRooms(){
		$this->rooms = [];
	}
	
	public function clear(bool $all){
		$this->price = -1;
		$this->members = [];
		if($all){
			$this->owner = "";
		}
		
		$this->welcomeMessage = "";
		$this->welcomeParticle = 0;
		
		$this->allowPVP = false;
		$this->allowAccess = true;
		$this->allowPickUpItem = false;
		
		$this->clearRooms();
	}
	
	
	
	public function serialize() : array{
		$data = [];
		if(get_class($this) !== Land::class){
			$data["class"] = get_class($this);
		}
		if($this->owner !== ""){
			$data["owner"] = $this->owner;
		}
		if(!empty($this->members)){
			$data["members"] = $this->members;
		}
		$data["startX"] = $this->startX;
		$data["startZ"] = $this->startZ;
		$data["endX"] = $this->endX;
		$data["endZ"] = $this->endZ;
		if($this->price !== -1){
			$data["price"] = $this->price;
		}
		$data["spawnPoint"] = $this->spawnPoint->x . ":" . $this->spawnPoint->y . ":" . $this->spawnPoint->z;
		if($this->allowPVP){ // default value : false
			$data["pvp"] = $this->allowPVP;
		}
		if(!$this->allowAccess){ // default value : true
			$data["access"] = $this->allowAccess;
		}
		if($this->allowPickupItem){ // default value : false
			$data["pickupItem"] = $this->allowPickupItem;
		}
		if($this->welcomeMessage !== ""){
			$data["welcomeMessage"] = $this->welcomeMessage;
		}
		if($this->welcomeParticle !== 0){
			$data["welcomeParticle"] = $this->welcomeParticle;
		}
		if(!empty($this->rooms)){
			$data["rooms"] = [];
			foreach($this->getRooms() as $room){
				$data["rooms"][$room->getId()] = $room->serialize();
			}
		}
		return $data;
	}
	
	public function unserialize(array $data){
		$this->owner = $data["owner"] ?? "";
		$this->members = $data["members"] ?? [];
		$this->startX = $data["startX"];
		$this->startZ = $data["startZ"];
		$this->endX = $data["endX"];
		$this->endZ = $data["endZ"];
		$this->price = $data["price"] ?? -1;
		$v = explode(":", $data["spawnPoint"]);
		$this->spawnPoint = new Vector3($v[0], $v[1], $v[2]);
		$this->allowPVP = $data["pvp"] ?? false;
		$this->allowAccess = $data["access"] ?? true;
		$this->allowPickupItem = $data["pickupItem"] ?? false;
		$this->welcomeMessage = $data["welcomeMessage"] ?? "";
		$this->welcomeParticle = $data["welcomeParticle"] ?? 0;
		foreach($data["rooms"] ?? [] as $roomId => $roomData){
			$roomClass = Room::class;
			if(isset($roomData["class"])){
				$roomClass = $roomData["class"];
				if(!class_exists($roomClass)){
					$roomClass = Room::class;
				}
			}
			$room = new $roomClass($roomId);
			$room->unserialized($roomData);
			$this->rooms[$roomId] = $room;
		}
	}
}