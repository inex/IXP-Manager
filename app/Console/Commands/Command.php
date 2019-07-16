<?php namespace IXP\Console\Commands;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Symfony\Component\Console\Output\OutputInterface;
use Entities\IXP;

abstract class Command extends \Illuminate\Console\Command {

     /**
      * Returns true if verbosity is EXACTLY: VERBOSITY_QUIET
      * @return bool
      */
     protected function isVerbosityQuiet() {
         return $this->getOutput()->getVerbosity() == OutputInterface::VERBOSITY_QUIET;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_NORMAL
      * @return bool
      */
     protected function isVerbosityNormal() {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_VERBOSE
      * @return bool
      */
     protected function isVerbosityVerbose() {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_VERY_VERBOSE
      * @return bool
      */
     protected function isVerbosityVeryVerbose() {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_DEBUG
      * @return bool
      */
     protected function isVerbosityDebug() {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG;
     }

}
