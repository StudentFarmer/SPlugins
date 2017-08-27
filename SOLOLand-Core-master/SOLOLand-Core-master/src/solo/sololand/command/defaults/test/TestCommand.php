<?php

namespace solo\sololand\command\defaults\test;

use solo\sololand\command\MainCommand;
use solo\sololand\command\defaults\test\args\{
	TestSection,
	TestLand
};

class TestCommand extends MainCommand{

	public function __construct(){
		parent::__construct("테스트", "테스트 명령어입니다.");
		$this->setPermission("sololand.command.test");
		
		
		
		$this->registerSubCommand(new TestSection());
		$this->registerSubCommand(new TestLand());
	}
}