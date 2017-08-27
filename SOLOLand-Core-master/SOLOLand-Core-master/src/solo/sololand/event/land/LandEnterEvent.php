<?php

namespace solo\sololand\event\land;

use pocketmine\Player;
use pocketmine\event\Cancellable;
use solo\sololand\land\Land;

class LandEnterEvent extends LandEvent implements Cancellable{
	
	public static $handlerList = null;

	protected $player;
	protected $welcomeMessage;
	protected $welcomeParticle;

	public function __construct(Player $player, Land $land, string $welcomeMessage = "", int $welcomeParticle = 0){
		$this->player = $player;
		$this->land = $land;
		$this->welcomeMessage = $welcomeMessage;
		$this->welcomeParticle = $welcomeParticle;
	}
	
	public function getWelcomeMessage() : string{
		return $this->welcomeMessage;
	}
	
	public function getWelcomeParticle() : int{
		return $this->welcomeParticle;
	}
	
	public function getPlayer() : Player{
		return $this->player;
	}
	
	public function setWelcomeMessage(string $welcomeMessage){
		$this->welcomeMessage = $welcomeMessage;
	}
	
	public function setWelcomeParticle(int $particle){
		$this->welcomeParticle = $particle;
	}
}
