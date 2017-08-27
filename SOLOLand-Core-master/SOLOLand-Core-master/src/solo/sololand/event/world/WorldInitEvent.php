<?php

namespace solo\sololand\event\world;

use pocketmine\event\Cancellable;
use solo\sololand\world\World;

class WorldInitEvent extends WorldEvent implements Cancellable{
	
	public static $handlerList = null;
	
	public function __construct(World $world){
		$this->world = $world;
	}
}