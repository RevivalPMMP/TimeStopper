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

namespace RevivalPMMP\TimeStopper\data;


use RevivalPMMP\TimeStopper\TimeStopper;

class ConfigurationData{

	private $settings = [];

	/**
	 * @var TimeStopper
	 */
	private $instance;

	public function __construct(TimeStopper $instance) {
		$this->instance = $instance;
		$this->initializeDirectories();
		$this->loadSettings();
	}

	private function initializeDirectories() {
		if(!is_dir($this->instance->getDataFolder())){
			mkdir($this->instance->getDataFolder());
		}
	}

	private function loadSettings() {
		$this->settings = $this->instance->getConfig()->getAll();
	}

	public function reloadSettings(){
		$this->loadSettings();
	}

	public function getSetting(string $key){
			return $this->instance->getConfig()->get($key);
	}
}