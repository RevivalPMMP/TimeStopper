<?php
/**
 * Copyright (C) 2018 RevivalPMMP
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace RevivalPMMP\TimeStopper;

use pocketmine\command\defaults\TimeCommand as PMTimeCommand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\GameRuleType;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use RevivalPMMP\TimeStopper\command\TimeCommand;
use RevivalPMMP\TimeStopper\data\ConfigKeys;

class TimeStopper extends PluginBase implements Listener{

	/** @var TimeStopper $instance */
	private static $instance;

	/** @var int */
	private $defaultTime = Level::TIME_DAY;

	/** @var bool */
	private $timeStopped = false;

	public function onEnable() {
		self::$instance = $this;

		$config = $this->getConfig();
		$this->defaultTime = $config->get(ConfigKeys::DEFAULT_TIME, Level::TIME_DAY);
		$this->timeStopped = $config->get(ConfigKeys::STOP_TIME, false);
		$this->registerCommands();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if($this->timeStopped){
			$this->setTime($this->defaultTime);
		}
	}

	public function setTime($time, bool $add = false) : void{
		switch(strtolower($time)){
			case "day":
				$value = Level::TIME_DAY;
				break;
			case "noon":
				$value = Level::TIME_NOON;
				break;
			case "sunset":
				$value = Level::TIME_SUNSET;
				break;
			case "night":
				$value = Level::TIME_NIGHT;
				break;
			case "midnight":
				$value = Level::TIME_MIDNIGHT;
				break;
			case "sunrise":
				$value = Level::TIME_SUNRISE;
				break;
			default:
				if(!is_numeric($time) or ($time < 0 and !$add)){
					$value = 0;
				}else{
					$value = (int) $time;
				}
				break;
		}

		$this->getConfig()->set(ConfigKeys::DEFAULT_TIME, $value);
		foreach($this->getServer()->getLevels() as $level){
			if($add){
				$level->setTime($level->getTime() + $value);
			}else{
				$level->setTime($value);
			}
			if($this->timeStopped){
				$level->stopTime();
			}
		}
	}

	public function startTime() : void{
		if(!$this->timeStopped){
			return;
		}
		$this->timeStopped = false;
		$this->getConfig()->set(ConfigKeys::STOP_TIME, false);
		foreach($this->getServer()->getLevels() as $level){
			$level->startTime();
		}
		$this->sendGameRuleUpdate($this->getServer()->getOnlinePlayers());
	}

	public function stopTime() : void{
		if($this->timeStopped){
			return;
		}
		$this->timeStopped = true;
		$this->getConfig()->set(ConfigKeys::STOP_TIME, false);
		foreach($this->getServer()->getLevels() as $level){
			$level->stopTime();
		}
		$this->sendGameRuleUpdate($this->getServer()->getOnlinePlayers());
	}

	public static function getInstance() : self {
		return self::$instance;
	}

	public function onJoin(PlayerJoinEvent $event): void{
		$player = $event->getPlayer();
		$this->sendGameRuleUpdate([$player]);
	}

	/**
	 * @param Player[] $players
	 */
	private function sendGameRuleUpdate(array $players) : void{
		$pk = new GameRulesChangedPacket();
		$pk->gameRules = ["dodaylightcycle" => [GameRuleType::BOOL, !$this->timeStopped]];
		$this->getServer()->broadcastPacket($players, $pk);
	}

	private function registerCommands() : void{
		$map = $this->getServer()->getCommandMap();
		if(($baseCommand = $map->getCommand("time")) instanceof PMTimeCommand){
			$map->unregister($baseCommand);
		}
		$map->register("timestopper", new TimeCommand());
	}

	public function onDisable(){
		if(!$this->getConfig()->get("Save-Changes")){
			return;
		}
		if($this->getConfig()->hasChanged()){
			$this->saveConfig();
		}
	}
}