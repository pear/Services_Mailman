<?php

/**
 * Services Mailman
 *
 * Allows the integration of Mailman into a dynamic website without using
 *      Python or requiring permission to Mailman binaries
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
 * @category  Services
 * @package   Mailman
 * @author    James Wade <hm2k@php.net>
 * @copyright 2011 James Wade
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   SVN: @package_version@
 * @link      http://php-mailman.sf.net/
 */

require_once 'HTTP/Request2.php';

/**
 * Mailman Class
 *
 * @category  Services
 * @package   Mailman
 * @author    James Wade <hm2k@php.net>
 * @copyright 2011 James Wade
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: $Id:$
 * @link      http://php-mailman.sf.net/
 */
class Services_Mailman
{
    /**
     * Default URL to the Mailman "Admin Links" page (no trailing slash)
     *  For example: 'http://www.example.co.uk/mailman/admin'
     * @var string
     */
    protected $adminURL;
    /**
     * Default name of the list
     *  For example: 'test_example.co.uk'
     * @var string
     */
    protected $list;
    /**
     * Default admin password for the list
     *  For example: 'my-example-password'
     * @var string
     */
    protected $adminPW;
    /**
     * A HTTP request instance
     *
     * @var HTTP_Request2 $request
     */
    protected $request = null;
    /**
     * Constructor
     *
     * @param string $adminURL Set the URL to the Mailman "Admin Links" page
     * @param string $list     Set the name of the list
     * @param string $adminPW  Set admin password of the list
     * @param HTTP_Request2 $request  Provide your HTTP request instance
     *
     * @return Services_Mailman
     */
    public function __construct($adminURL, $list = '', $adminPW = '', HTTP_Request2 $request = null)
    {
        $this->setList($list);
        $this->setadminURL($adminURL);
        $this->setadminPW($adminPW);
        $this->setRequest($request);
    }

    /**
     * Sets the list name
     *
     * @param string $string The name of the list
     *
     * @return Services_Mailman
     *
     * @throws Exception When incorrect string is given.
     */
    public function setList($string)
    {
        if (empty($string)) {
            throw new Exception(
                'setList() does not expect parameter 1 to be empty'
            );
        }
        if (!is_string($string)) {
            throw new Exception(
                'setList() expects parameter 1 to be string, ' .
                gettype($string) . ' given'
            );
        }
        $this->list = $string;
        return $this;
    }
    /**
     * Sets the URL to the Mailman "Admin Links" page
     *
     * @param string $string The URL to the Mailman "Admin Links" page (no trailing slash)
     *
     * @return Services_Mailman
     *
     * @throws Exception When incorrect string is given.
     */
    public function setadminURL($string)
    {
        if (empty($string)) {
            throw new Exception(
                'setadminURL() does not expect parameter 1 to be empty'
            );
        }
        if (!is_string($string)) {
            throw new Exception(
                'setadminURL() expects parameter 1 to be string, ' .
                gettype($string) . ' given'
            );
        }
        $string = filter_var($string, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
        if (!$string) {
            throw new Exception('Invalid URL');
        }
        $this->adminURL = trim($string, '/');
        return $this;
    }
    /**
     * Sets the admin password of the list
     *
     * @param string $string The password string
     *
     * @return Services_Mailman
     *
     * @throws Exception When incorrect string is given.
     */
    public function setadminPW($string)
    {
        if (!is_string($string)) {
            throw new Exception(
                'setadminPW() expects parameter 1 to be string, ' .
                gettype($string) . ' given'
            );
        }
        $this->adminPW = $string;
        return $this;
    }
    /**
     * Sets the request object
     *
     * @param HTTP_Request2 $object A HTTP request instance (otherwise one will be created)
     *
     * @return Services_Mailman
     *
     * @throws Exception If unable to create HTTP_Request2 instance.
     */
    public function setRequest(HTTP_Request2 $object = null)
    {
        if ($object === null) {
            $this->request = new HTTP_Request2();
        }
        return $this;
    }

    /**
     * Fetches the HTML to be parsed
     *
     * @param string $url A valid URL to fetch
     *
     * @return string Return contents from URL (usually HTML)
     *
     * @throws Exception When invalid URL is provided or no HTML content.
     */
    protected function fetch($url)
    {
        $url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
        if (!$url) {
            throw new Exception('Invalid URL');
        }
        $this->request->setUrl($url);
        $this->request->setMethod('GET');
        $html = $this->request->send()->getBody();
        if ($html && preg_match('#<HTML>#i', $html)) {
            return $html;
        }
        throw new Exception('No HTML content.');
    }

    /**
     * List lists
     *
     * (ie: <domain.com>/mailman/admin)
     *
     * @param boolean $assoc Associated array (default) or not
     *
     * @return array   Return an array of lists
     *
     * @throws Exception If it was unable to match any lists or failed to parse HTML
     */
    public function lists($assoc = true)
    {
        $html = $this->fetch($this->adminURL);
        if (!$html) {
            return false;
        }
        $match = '#<tr.*?>\s+<td><a href="(.+?)"><strong>(.+?)</strong></a></td>\s+';
        $match .= '<td><em>(.+?)</em></td>\s+</tr>#i';
        $a = array();
        if (preg_match_all($match, $html, $m)) {
            if (!$m) {
                throw new Exception('Unable to match any lists');
            }
            foreach ($m[0] as $k => $v) {
                $a[$k][] = $m[1][$k];
                $a[$k][] = $m[2][$k];
                $a[$k][] = $m[3][$k];
                if ($assoc) {
                    $a[$k]['path'] = basename($m[1][$k]);
                    $a[$k]['name'] = $m[2][$k];
                    $a[$k]['desc'] = $m[3][$k];
                }
            }
        } else {
            throw new Exception('Failed to parse HTML.');
        }
        return $a;
    }

    /**
     * List a member
     *
     * (ie: <domain.com>/mailman/admin/<listname>/members?findmember=<email-address>
     *      &setmemberopts_btn&adminpw=<adminpassword>)
     *
     * @param string $email A valid email address of a member to lookup
     *
     * @return string Returns unparsed HTML
     *
     * @throws Exception When unable to fetch HTML.
     */
    public function member($email)
    {
        $path  = '/' . $this->list . '/members';
        $query = array(
            'findmember'        => $email, 
            'setmemberopts_btn' => null,
            'adminpw'           => $this->adminPW
        );

        $query = http_build_query($query, '', '&');
        $url   = $this->adminURL . $path . '?' . $query;
        $html  = $this->fetch($url);
        if (!$html) {
            throw new Exception('Unable to fetch HTML.');
        }
        //TODO:parse html
        return $html;
    }
    
    /**
     * Unsubscribe
     *
     * (ie: <domain.com>/mailman/admin/<listname>/members/remove?send_unsub_ack_to_this_batch=0
     *      &send_unsub_notifications_to_list_owner=0&unsubscribees_upload=<email-address>&adminpw=<adminpassword>)
     *
     * @param string $email Valid email address of a member to unsubscribe
     *
     * @return Services_Mailman
     *
     * @throws Exception When unable to fetch HTML or action was unsuccessful or unable to parse HTML.
     */
    public function unsubscribe($email)
    {
        $path = '/' . $this->list . '/members/remove';
        $query = array(
            'send_unsub_ack_to_this_batch' => 0,
            'send_unsub_notifications_to_list_owner' => 0,
            'unsubscribees_upload' => $email,
            'adminpw' => $this->adminPW
        );
        $query = http_build_query($query, '', '&');
        $url = $this->adminURL . $path . '?' . $query;
        $html = $this->fetch($url);
        if (!$html) {
           throw new Exception('Unable to fetch HTML.');
        }
        if (preg_match('#<h5>Successfully Unsubscribed:</h5>#i', $html)) {
            return $this;
        }
        if (preg_match('#<h3>(.+?)</h3>#i', $html, $m)) {
            throw new Exception(trim(strip_tags($m[1]), ':'));
        }
        throw new Exception('Failed to parse HTML.');
    }

    /**
     * Subscribe
     *
     * (ie: http://example.co.uk/mailman/admin/test_example.co.uk/members/add
     * ?subscribe_or_invite=0&send_welcome_msg_to_this_batch=1
     * &send_notifications_to_list_owner=0&subscribees=test%40example.co.uk
     * &invitation=&setmemberopts_btn=Submit+Your+Changes)
     *
     * @param string  $email  Valid email address to subscribe
     * @param boolean $invite Send an invite or not (default)
     *
     * @return Services_Mailman
     *
     * @throws Exception When unable to fetch HTML or action was unsuccessful or unable to parse HTML.
     */
    public function subscribe($email, $invite = false)
    {
        $path = '/' . $this->list . '/members/add';
        $query = array('subscribe_or_invite' => (int)$invite,
                        'send_welcome_msg_to_this_batch' => 0,
                        'send_notifications_to_list_owner' => 0,
                        'subscribees' => $email,
                        'adminpw' => $this->adminPW);
        $query = http_build_query($query, '', '&');
        $url = $this->adminURL . $path . '?' . $query;
        $html = $this->fetch($url);
        if (!$html) {
           throw new Exception('Unable to fetch HTML.');
        }
        if (preg_match('#<h5>Successfully subscribed:</h5>#i', $html)) {
            return $this;
        }
        if (preg_match('#<h5>(.+?)</h5>#i', $html, $m)) {
            throw new Exception(trim(strip_tags($m[1]), ':'));
        }
        throw new Exception('Failed to parse HTML.');
    }

    /**
     * Set digest. Note that the $email needs to be subsribed first
     *  (e.g. by using the {@link subscribe()} method)
     *
     * (ie: <domain.com>/mailman/admin/<listname>/members?user=<email-address>
     *      &<email-address>_digest=1&setmemberopts_btn=Submit%20Your%20Changes
     *      &allmodbit_val=0&<email-address>_language=en&<email-address>_nodupes=1
     *      &adminpw=<adminpassword>)
     *
     * @param string $email Valid email address of a member
     *
     * @return string Returns unparsed HTML
     *
     * @throws Exception When unable to fetch HTML.
     */
    public function setDigest($email)
    {
        $path = '/' . $this->list . '/members';
        $query = array('user' => $email,
                        $email . '_digest' => 1,
                        'setmemberopts_btn' => 'Submit%20Your%20Changes',
                        'allmodbit_val' => 0,
                        $email . '_language' => 'en',
                        $email . '_nodupes' => 1,
                        'adminpw' => $this->adminPW);
        $query = http_build_query($query, '', '&');
        $url = $this->adminURL . $path . '?' . $query;
        $html = $this->fetch($url);
        if (!$html) {
           throw new Exception('Unable to fetch HTML.');
        }
        //TODO:parse html
        return $html;
    }
    /**
     * List members
     *
     * @return array  Returns a lits of members names and email addresses
     *
     * @throws Exception When unable to fetch HTML or unable to parse HTML.
     */
    public function members()
    {
        $path = '/' . $this->list . '/members';
        $query = array('adminpw' => $this->adminPW);
        $query = http_build_query($query, '', '&');
        $url = $this->adminURL . $path . '?' . $query;
        $html = $this->fetch($url);
        if (!$html) {
            throw new Exception('Unable to fetch HTML.');
        }
        $p = '#<a href=".*?letter=(.)">.+?</a>#i';
        if (preg_match_all($p, $html, $m)) {
            $letters = array_pop($m);
        } else {
            $letters = array(null);
        }
        $members = array(array(), array());
        foreach ($letters as $letter) {
            $query = array('adminpw' => $this->adminPW);
            if ($letter != null) {
                $query['letter'] = $letter;
            }
            $query = http_build_query($query, '', '&');
            $url = $this->adminURL . $path . '?' . $query;
            $html = $this->fetch($url);
            $p = '#<td><a href=".+?">(.+?)</a><br><INPUT name=".+?_realname" type="TEXT" value="(.*?)" size="\d{2}" ><INPUT name="user" type="HIDDEN" value=".+?" ></td>#i';
            if (preg_match_all($p, $html, $m)) {
                array_shift($m);
                $members[0] = array_merge($members[0], $m[0]);
                $members[1] = array_merge($members[1], $m[1]);
            } else {
                throw new Exception('Failed to parse HTML.');
            }
        }
        return $members;
    }
    /**
     * Version
     *
     * @return string Returns the version of Mailman
     *
     * @throws Exception When unable to fetch HTML or unable to parse HTML.
     */
    public function version()
    {
        $path = '/' . $this->list . '/';
        $query = array('adminpw' => $this->adminPW);
        $query = http_build_query($query, '', '&');
        $url = $this->adminURL . $path . '?' . $query;
        $html = $this->fetch($url);
        if (!$html) {
            throw new Exception('Unable to fetch HTML.');
        }
        $p = '#<td><img src="/img-sys/mailman.jpg" alt="Delivered by Mailman" border=0><br>version (.+?)</td>#i';
        if (preg_match($p, $html, $m)) {
            return array_pop($m);
        }
        throw new Exception('Failed to parse HTML.');
    }
} //end
//eof