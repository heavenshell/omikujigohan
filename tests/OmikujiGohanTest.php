<?php
/**
 * Omikujigohan tests.
 *
 * PHP version 5.3
 *
 * Copyright (c) 2011 Shinya Ohyanagi, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Shinya Ohyanagi nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @use       \Omikujigohan
 * @category  \Omikujigohan
 * @package   \Omikujigohan
 * @version   $id$
 * @copyright (c) 2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

require_once 'prepare.php';

use Silex\WebTestCase;

/**
 * Omikuji tests.
 *
 * @use       \Omikujigohan
 * @category  \Omikujigohan
 * @package   \Omikujigohan
 * @version   $id$
 * @copyright (c) 2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class OmikujiGohanTest extends WebTestCase
{
    private static $_client = null;
    public function setUp()
    {
    }
    public function createApplication()
    {
    }

    private function _client($uri, $method = 'GET')
    {
        $client = $this->createClient();
        $client->request($method, $uri);
        return $client->getResponse();
    }

    public function testShouldDispatchToActions()
    {
        $this->app = require_once dirname(__DIR__) . '/www/index.php';
        ob_start();
        $response = $this->_client('/');
        ob_end_clean();
        $this->assertEquals($response->getStatusCode(), 200);
        ob_start();
        $response = $this->_client('/result', 'POST');
        ob_end_clean();
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testShouldGetAMenuFromTextFile()
    {
        $menu   = new \Omikujigohan\Model\Menu();
        $result = $menu->choice();
        $this->assertTrue(in_array($result, explode("\n", file_get_contents('../data/menu.txt'))));
    }

    public function testShouldGetAMenuFromDefaultValues()
    {
        $menu   = new \Omikujigohan\Model\Menu();
        $result = $menu->choice(array(0 => 'foo'));
        $this->assertEquals($result, 'foo');
    }

    public function testShouldFormatStringToVerticalString()
    {
        require_once '../www/index.php';
        $menu   = new \Omikujigohan\Model\Menu();
        $result = $menu->format('ハンバーガー');
        $this->assertEquals($result, 'ハ<br />ン<br />バ<br />|<br />ガ<br />|<br />');
    }
}
