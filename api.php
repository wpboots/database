<?php

/**
 * Database
 *
 * @package Boots
 * @subpackage Database
 * @version 1.0.0
 * @license GPLv2
 *
 * Boots - The missing WordPress framework. http://wpboots.com
 *
 * Copyright (C) <2014>  <M. Kamal Khan>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

class Boots_Database
{
    private $Boots;

    private $term = null;
    private $default = null;
    private $id = null;
    private $single = null;

    public function __construct($Boots, $Settings, $dir, $url)
    {
        $this->Boots = $Boots;
    }

    private function reset()
    {
        $this->term = null;
        $this->default = null;
        $this->id = null;
        $this->single = null;
    }

    private function _get($what)
    {
        switch($what)
        {
            case 'option':
                $value = get_option($this->term, $this->default);
            break;
            case 'postmeta':
                $value = get_post_meta($this->id, $this->term, $this->single);
            break;
            default:
                $value = null;
            break;
        }

        $this->reset();
        return $value;
    }

    private function _update($what, $new_value, $prev_value = '')
    {
        switch($what)
        {
            case 'option':
                update_option($this->term, $new_value);
            break;
            case 'postmeta':
                update_post_meta($this->id, $this->term, $new_value, $prev_value);
            break;
        }

        $this->reset();
        return $this;
    }

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    public function single($single)
    {
        $this->single = $single;

        return $this;
    }

    public function term($term, $default = false)
    {
        $this->term = $term;
        $this->default = $default;

        return $this;
    }

    public function get($id = false, $single = null)
    {
        $this->id = ($id !== false)
            ? $id
            : ($this->id ? $this->id : false);

        $this->single = ($single !== null)
            ? $single
            : (($this->single !== null) ? $this->single : true);

        if(!$this->term)
        {
            $this->Boots->error($this->error());
            return false;
        }

        if($this->id)
        {
            return $this->_get('postmeta');
        }

        return $this->_get('option');
    }

    public function update($value, $prev_value = '')
    {
        if(!$this->term)
        {
            $this->Boots->error($this->error());
            return false;
        }

        if($this->id)
        {
            return $this->_update('postmeta', $value, $prev_value);
        }

        return $this->_update('option', $value);
    }

    private function error()
    {
        $err = 'Term was not set. ';
        $err .= 'Have you called <em>Database&rarr;term($term, $default = false)</em> ?';
        return $err;
    }
}
