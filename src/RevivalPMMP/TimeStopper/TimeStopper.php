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

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use RevivalPMMP\TimeStopper\Data\ConfigKeys;
use RevivalPMMP\TimeStopper\Data\ConfigurationData;

class TimeStopper extends PluginBase{

	private $instance;

	/**
	 * @var ConfigurationData
	 */
	private $config;

	public function onLoad() {
		$this->instance = $this;
		$this->saveDefaultConfig();
		$this->config =new ConfigurationData($this);
		$this->getServer()->getLogger()->info(TextFormat::DARK_PURPLE . "Restore Loaded!");

	}

	public function onEnable() {
		if($this->config->getSetting(ConfigKeys::STOP_TIME)) {
			foreach ( $this->getServer()->getLevels() as $level ) {
				$level->checkTime();
				$level->setTime( $this->config->getSetting( ConfigKeys::DEFAULT_TIME ) );
				$level->checkTime();
				$level->stopTime();
				$level->checkTime();
			}
			$this->getServer()->getLogger()->info(TextFormat::DARK_PURPLE . "TimeStopper Enabled!");
		} else {
			$this->onDisable();
		}
	}

	public function getInstance() {
		return $this->instance;
	}
}