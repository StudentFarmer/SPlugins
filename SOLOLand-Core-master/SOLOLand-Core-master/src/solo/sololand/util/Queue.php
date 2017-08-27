<?php

namespace solo\sololand\util;

use pocketmine\Player;

abstract class Queue{

	public static $list = [];
	
	public static function setQueue(Player $player, Queue $queue){
		self::$list[$player->getName()] = $queue;
	}
	
	public static function getQueue(Player $player){
		return self::$list[$player->getName()] ?? null;
	}
	
	public static function removeQueue(Player $player){
		unset(self::$list[$player->getName()]);
	}
	
	
	
	
	
	public $step = 0;
	public $data = [];
	
	public abstract function getName() : string;
	
	public function getStep() : int{
		return $this->step;
	}
	
	public function setStep(int $step){
		$this->step = $step;
	}
	
	public function set(string $key, $object){
		$this->data[$key] = $object;
	}
	
	public function get(string $key){
		return $this->data[$key] ?? null;
	}
	
	public function remove(string $key){
		unset($this->data[$key]);
	}
}