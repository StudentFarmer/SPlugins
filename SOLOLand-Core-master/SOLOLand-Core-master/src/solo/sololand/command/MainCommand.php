<?php

namespace solo\sololand\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use solo\solocore\util\Message;

abstract class MainCommand extends Command{

	private $subCommandAliases = [];
	private $subCommands = [];
	
	public function getSubCommandUsage(SubCommand $subCommand){
		$usages = [];
		if(count($subCommand->getParameters()) == 0){
			return "/" . $this->getName() . " " . $subCommand->getName();
		}else foreach($subCommand->getParameters() as $usage){
			$params = [];
			foreach($usage as $param){
				$params[] = "[" . $param ."]";
			}
			$usages[] = "/" . $this->getName() . " " . $subCommand->getName() . " " . implode(" ", $params);
		}
		return implode(" 또는 ", $usages);
	}
	
	public function getSubCommand($name){
		if(isset($this->subCommands[$name])){
			return $this->subCommands[$name];
		}else if(isset($this->subCommandAliases[$name]) && isset($this->subCommands[$name = $this->subCommandAliases[$name]])){
			return $this->subCommands[$name];
		}
		return null;
	}
	
	public function getSubCommands($condition = null){
		if($condition === null){
			return $this->subCommands;
		}else{
			$ret = [];
			foreach($this->subCommands as $cmd){
				if($condition($cmd)){
					$ret[$cmd->getName()] = $cmd;
				}
			}
			return $ret;
		}
	}

	public function execute(CommandSender $sender, $commandLabel, array $args){
		$find = array_shift($args);
		$subCommand = $this->getSubCommand($find);
		if($subCommand === null){ // get commands help part
			$lines = [];
			$condition = function(SubCommand $cmd) use ($sender) {
				return ($sender instanceof Player || !$cmd->isInGameOnly()) && $sender->hasPermission($cmd->getPermission());
			};
			foreach($this->getSubCommands($condition) as $cmd){
				$lines[$this->getSubCommandUsage($cmd)] = $cmd->getDescription();
			}
			$page = 1;
			if(is_numeric($find)){
				$page = (int) $find;
			}
			Message::commandHelp($sender, $this->getName() . " 명령어 도움말", $lines, $page);
		}else{ // command execute part
			if(!$sender instanceof Player && $subCommand->isInGameOnly()){
				Message::alert($sender, "인게임에서만 사용 가능합니다.");
				return true;
			}
			if(!$sender->hasPermission($subCommand->getPermission())){
				Message::alert($sender, "권한이 없습니다.");
				return true;
			}
			if(!$subCommand->execute($sender, $args)){
				Message::usage($sender, $this->getSubCommandUsage($subCommand));
			}
		}
		return true;
	}

	public function registerSubCommand(SubCommand $subCommand){
		$this->subCommands[$subCommand->getName()] = $subCommand;
		foreach($subCommand->getAliases() as $aliase){
			$this->subCommandAliases[$aliase] = $subCommand->getName();
		}
	}

	public function unregisterSubCommand(SubCommand $subCommand){
		unset($this->subCommands[$subCommand->getName()]);
		foreach($this->subCommandAliases as $aliase => $subCommandName){
			if($subCommandName === $subCommand->getName()){
				unset($this->subCommandAliases[$aliase]);
			}
		}
	}
}