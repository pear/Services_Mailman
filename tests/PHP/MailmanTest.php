<?php
/**
 * PHP Mailman
 *
 * PHP Mailman allows the integration of Mailman into a dynamic website without
 *      using Python or requiring permission to Mailman binaries
 *
 * PHP version 5
 *
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * + Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * + Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * + Neither the name of the <ORGANIZATION> nor the names of its contributors
 * may be used to endorse or promote products derived
 * from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  PHP
 * @package   Mailman
 * @author    James Wade <hm2k@php.net>
 * @copyright 2011 James Wade
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   SVN: @package_version@
 * @link      http://php-mailman.sf.net/
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHP/Mailman.php';
require_once 'Validate.php';
require_once 'HTTP/Request2.php';
require_once 'HTTP/Request2/Adapter/Mock.php';

/**
 * Basic test cases that shorten/expand 
 *
 * @category PHP
 * @package  Mailman
 * @author   James Wade <hm2k@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link     http://php-mailman.sf.net/
 */             
class PHP_MailmanTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test creating and then expanding a URL
     *
     * @param string $service The service to test
     *
     * @dataProvider allServices
     * @return void
     */
    public function testGnuLists($service)
    {
        $url = 'https://lists.gnu.org/mailman/admin';
        $mailman = new Mailman($url);

        // Get the lists and do some sanity checking
        $lists = $mailman->lists();
        $this->assertType('array', $lists);
    }
}//eof