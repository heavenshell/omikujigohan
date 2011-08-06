<?php
/**
 * Omikuji for PHP5.3 - Refactor https://github.com/woopsdez to modern PHP5.3 -
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
 * @category  \Omikujigohan
 * @package   \Omikujigohan
 * @version   $id$
 * @copyright (c) https://github.com/woopsdez
 * @copyright (c) 2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      https://github.com/woopsdez
 */
namespace Omikujigohan;

/**
 * \Omikujigohan\Exception
 *
 * @category  \Omikujigohan
 * @package   \Omikujigohan
 * @version   $id$
 * @copyright (c) 2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Exception extends \Exception
{
}

namespace Omikujigohan\Model;

/**
 * Omikujigohan\Model\Menu
 *
 * @category  \Omikujigohan
 * @package   \Omikujigohan
 * @version   $id$
 * @copyright (c) 2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Menu
{
    /**
     * Randum choice from menu.txt
     *
     * <pre>
     *   If $default exists, use as default or use menu.txt.
     * </pre>
     *
     * @access public
     * @params array $default
     * @throws \Omikujigohan\Exception Menu file not found.
     * @return array Menu
     */
    public function choice(array $default = array())
    {
        $cnt    = 0;
        $result = array();
        if ($default !== array()) {
            array_map(function ($val) use (&$cnt, &$result) {
                if ($val !== '') {
                    $cnt ++;
                    $result[] = $val;
                }
            }, $default);
        }
       $choice = function (array $items) {
            $key = array_rand($items);
            return htmlspecialchars(
                trim($items[$key], "\n"), ENT_QUOTES, 'UTF-8'
            );
        };
        if ($cnt > 0) {
            return $choice($result);
        }

        if (file_exists(dirname(__DIR__) . '/data/menu.txt')) {
            $result = file(dirname(__DIR__) . '/data/menu.txt');
            if (is_array($result)) {
                return $choice($result);
            }
        }
        throw new \Omikujigohan\Exception('menu.txt nof found.');
    }

    /**
     * Format strings
     *
     * <pre>
     *   add br tag for each string.
     * </pre>
     *
     * @param  mixed $str Menu
     * @access public
     * @return string Format string
     */
    public function format($str)
    {
        $array  = preg_split('//u', str_replace('ー', '|', $str));
        $result = array_map(function ($val) {
            if ($val === '') {
                return '';
            }
            return $val . '<br />';
        }, $array);

        return implode('', $result);
    }
}

/**
 * Controller.
 */
require_once 'silex.phar';
$app = new \Silex\Application();

$app->get('/', function () use ($app) {
    // I know it's better to serve by web server it self.
    include_once dirname(__DIR__) . '/templates/index.html';
});

$app->post('/result', function () use ($app) {
    $req   = $app['request'];
    $model = new \Omikujigohan\Model\Menu();
    $items = $req->get('item') ? $req->get('item') : array();
    $raw   = $model->choice($items);
    $menu  = $model->format($raw);
    include_once dirname(__DIR__) . '/templates/result.html';
});

if (getenv('SILEX_TEST')) {
    return $app;
}
$app->run();
