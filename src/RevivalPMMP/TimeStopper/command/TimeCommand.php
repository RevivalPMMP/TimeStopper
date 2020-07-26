<?php
declare(strict_types=1);

namespace RevivalPMMP\TimeStopper\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\lang\TranslationContainer;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use RevivalPMMP\TimeStopper\TimeStopper;

class TimeCommand extends Command{

	public function __construct(){
		parent::__construct("time", "Start, stop, and set the time of a world.", "time <add|query|set|start|stop>");
	}

	/**
	 * @param string[] $args
	 *
	 * @return mixed
	 * @throws CommandException
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(count($args) < 1){
			$sender->sendMessage($this->usageMessage);
			return true;
		}
		$plugin = TimeStopper::getInstance();


		switch(strtolower($args[0])){
			case "add":
				if($sender->hasPermission("pocketmine.command.time.add")){
					if(!isset($args[1]) or !is_numeric($args[1])){
						$sender->sendMessage(TextFormat::GOLD . "Usage: /time add <integer>");
						return true;
					}
					$increase = $args[1] ?? 0;
					$plugin->setTime((int) $increase, true);
					Command::broadcastCommandMessage($sender, new TranslationContainer("commands.time.added", [$increase]));
					return true;
				}
				break;
			case "set":
				if($sender->hasPermission("pocketmine.command.time.set")){
					if(isset($args[1])){
						$plugin->setTime($args[1]);
						Command::broadcastCommandMessage($sender, new TranslationContainer("commands.time.set", [$args[1]]));
					}else{
						$sender->sendMessage(TextFormat::GOLD . "/time set <day|noon|sunset|night|midnight|sunrise|integer>");
					}
					return true;
				}
				break;
			case "start":
				if($sender->hasPermission("pocketmine.command.time.start")){
					$plugin->startTime();
					$sender->sendMessage("Time Started");
					return true;
				}
				break;
			case "stop":
				if($sender->hasPermission("pocketmine.command.time.stop")){
					$plugin->stopTime();
					$sender->sendMessage("Time Stopped");
					return true;
				}
				break;
			case "query":
				if($sender->hasPermission("pocketmine.command.time.query")){
					if($sender instanceof Player){
						$level = $sender->getLevelNonNull();
					}else{
						$level = $sender->getServer()->getDefaultLevel();
					}
					$sender->sendMessage($sender->getServer()->getLanguage()->translateString("commands.time.query", [$level->getTime()]));
					return true;
				}
		}
		$sender->sendMessage($sender->getServer()->getLanguage()->translateString(TextFormat::RED . "%commands.generic.permission"));
		return true;
	}
}