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
use RevivalPMMP\TimeStopper\data\ConfigKeys;
use RevivalPMMP\TimeStopper\data\ConfigurationData;

class TimeStopper extends PluginBase{

	/** @var TimeStopper $instance */
	private static $instance;

	/** @var ConfigurationData $config */
	private $config;

	public function onLoad() {
		self::$instance = $this;
		$this->saveDefaultConfig();
		$this->config = new ConfigurationData($this);

	}

	public function onEnable() {
		if($this->config->getSetting(ConfigKeys::STOP_TIME)) {
			foreach ( $this->getServer()->getLevels() as $level ) {
				$level->setTime( $this->config->getSetting( ConfigKeys::DEFAULT_TIME ) );
				$level->stopTime();
			}
		} else {
			$this->setEnabled(false);
		}
	}

	public static function getInstance() : self {
		return self::$instance;
	}
}