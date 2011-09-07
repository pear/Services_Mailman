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
 * @link      http://pear.php.net/package/Mailman
 */
/**
 * Mailman Class
 *
 * @category  PHP
 * @package   Mailman
 * @author    James Wade <hm2k@php.net>
 * @copyright 2011 James Wade
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: $Id:$
 * @link      http://pear.php.net/package/Mailman
 */
class Mailman
{
    /**
     * The URL to the Mailman "Admin Links" page (no trailing slash)
     * @var string
     */
    public $adminurl = 'http://www.example.co.uk/mailman/admin';
    /**
     * Default name of the list
     * @var string
     */
    public $list = 'test_example.co.uk';
    /**
     * Default admin password for the list
     * @var string
     */
    public $adminpw = 'passwords-cannot-have-spaces';
    /**
     * Holder for any error messages
     * @var string
     */
    public $error;
    /**
     * The class constructor
     *
     * @param string $adminurl Set the URL to the Mailman "Admin Links" page
     * @param string $list     Set the name of the list
     * @param string $adminpw  Set admin password of the list
     *
     * @return void
     */
    public function __construct($adminurl, $list = '', $adminpw = '')
    {
        // Sanity checks
        if (!is_string($adminurl)) {
            user_error(
                'Mailman expects parameter 1 to be string, ' .
                gettype($adminurl) . ' given', E_USER_WARNING
            );
            return;
        }
        if (!is_string($list)) {
            user_error(
                'Mailman expects parameter 2 to be string, ' .
                gettype($list) . ' given', E_USER_WARNING
            );
            return;
        }
        if (!is_string($adminpw)) {
            user_error(
                'Mailman expects parameter 3 to be string, ' .
                gettype($adminpw) . ' given', E_USER_WARNING
            );
            return;
        }

        $this->adminurl = trim($adminurl, '/');
        if ($list) {
            $this->list = $list;
        }
        if ($adminpw) {
            $this->adminpw = $adminpw;
        }
    }
    /**
     * Fetches the HTML to be parsed
     *
     * @param string $url A valid URL to fetch
     *
     * @return string Return contents from URL (usually HTML)
     */
    private function _fetch($url)
    {
        $url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
        if (!$url) {
            $this->error='Invalid URL';
            return false;
        }
        $html = @file_get_contents($url);
        if ($html && preg_match('#<HTML>#i', $html)) {
            return $html;
        } else {
            $this->error='Unable to fetch HTML';
            return false;
        }
    }
    /**
     * List lists
     *
     * (ie: <domain.com>/mailman/admin)
     *
     * @param boolean $assoc Associated array (default) or not
     *
     * @return array   Return the list of lists
     */
    public function lists($assoc = true)
    {
        $html = $this->_fetch($this->adminurl);
        if (!$html) {
            return false;
        }
        $match = '#<tr.*?>\s+<td><a href="(.+?)"><strong>(.+?)</strong></a></td>\s+';
        $match .= '<td><em>(.+?)</em></td>\s+</tr>#i';
        $a = array();
        if (preg_match_all($match, $html, $m)) {
            if (!$m) {
                $this->error='Unable to match any lists';
                return false;
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
     * @return unknown Return description
     */
    public function member($email)
    {
        $path = '/%s/members?findmember=%s&setmemberopts_btn&adminpw=%s';
        $path = sprintf($path, $this->list, $email, $this->adminpw);
        $url = $this->adminurl . $path;
        $html = $this->_fetch($url);
        if (!$html) {
            return false;
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
     * @return boolean Returns whether it was successful or not
     */
    public function unsubscribe($email)
    {
        $path = '/%s/members/remove?send_unsub_ack_to_this_batch=0&send_unsub_notifications_to_list_owner=0&unsubscribees_upload=%s&adminpw=%s';
        $path = sprintf($path, $this->list, $email, $this->adminpw);
        $url = $this->adminurl . $path;
        $html = $this->_fetch($url);
        if (!$html) {
            return false;
        }
        if (preg_match('#<h5>Successfully Unsubscribed:</h5>#i', $html)) {
            $this->error = false;
            return true;
        } elseif (preg_match('#<h3>(.+?)</h3>#i', $html, $m)) {
            $this->error = trim(strip_tags($m[1]), ':');
            return false;
        }
    }
    /**
     * Subscribe
     *
     * (ie: <domain.com>/mailman/admin/<listname>/members/add?subscribe_or_invite=0
     *      &send_welcome_msg_to_this_batch=0&notification_to_list_owner=0
     *      &subscribees_upload=<email-address>&adminpw=<adminpassword>)
     *
     * @param string  $email  Valid email address to subscribe
     * @param boolean $invite Send an invite or not (default)
     *
     * @return boolean Returns whether it was successful or not
     */
    public function subscribe($email, $invite = false)
    {
        $path = '/%s/members/add?subscribe_or_invite=%d&send_welcome_msg_to_this_batch=0&notification_to_list_owner=0&subscribees_upload=%s&adminpw=%s';
        $path = sprintf($path, $this->list, (int)$invite, $email, $this->adminpw);
        $url = $this->adminurl . $path;
        $html = $this->_fetch($url);
        if (!$html) {
            return false;
        }
        if (preg_match('#<h5>Successfully subscribed:</h5>#i', $html)) {
            $this->error = false;
            return true;
        } elseif (preg_match('#<h5>(.+?)</h5>#i', $html, $m)) {
            $this->error = trim(strip_tags($m[1]), ':');
            return false;
        }
    }
    /**
     * Set digest. Note that the $email needs to be subsribed first (e.g. by using the {@link subsribe()} method)
     *
     * (ie: <domain.com>/mailman/admin/<listname>/members?user=<email-address>
     *      &<email-address>_digest=1&setmemberopts_btn=Submit%20Your%20Changes
     *      &allmodbit_val=0&<email-address>_language=en&<email-address>_nodupes=1
     *      &adminpw=<adminpassword>)
     *
     * @param string $email Valid email address of a member
     *
     * @return unknown Return description
     */
    public function setdigest($email)
    {
        $path = '/%s/members?user=
		%s&%s_digest=1&setmemberopts_btn=Submit%20Your%20Changes&allmodbit_val=0&%s_language=en&%s_nodupes=1&adminpw=%s';
        $path = sprintf($path, $this->list, $email, $email, $email, $email, $this->adminpw);
        $url = $this->adminurl . $path;
        $html = $this->_fetch($url);
        if (!$html) {
            return false;
        }
        //TODO:parse html
        return $html;
    }
    /**
     * List members
     *
     * @return array  Returns a lits of members names and email addresses
     */
    public function members()
    {
        //get the letters
        $url = $this->adminurl . sprintf('/%s/members?adminpw=%s', $this->list, $this->adminpw);
        $html = $this->_fetch($url);
        if (!$html) {
            return false;
        }
        $p = '#<a href=".*?letter=(.)">.+?</a>#i';
        if (!preg_match_all($p, $html, $m)) {
            $this->error='Unable to match any members';
            return false;
        }
        $letters = array_pop($m);
        //do the loop
        $members = array(array(), array());
        foreach ($letters as $letter) {
            $url = $this->adminurl . sprintf('/%s/members?letter=%s&adminpw=%s', $this->list, $letter, $this->adminpw);
            $html = $this->_fetch($url);
            //parse html
            $p = '#<td><a href=".+?">(.+?)</a><br><INPUT name=".+?_realname" type="TEXT" value="(.*?)" size="\d{2}" ><INPUT name="user" type="HIDDEN" value=".+?" ></td>#i';
            preg_match_all($p, $html, $m);
            array_shift($m);
            $members[0] = array_merge($members[0], $m[0]);
            $members[1] = array_merge($members[1], $m[1]);
        }
        return $members;
    }
    /**
     * Version
     *
     * @return string Returns the version of Mailman
     */
    public function version()
    {
        $url = $this->adminurl . sprintf('/%s/?adminpw=%s', $this->list, $this->adminpw);
        $html = $this->_fetch($url);
        if (!$html) {
            return false;
        }
        $p = '#<td><img src="/img-sys/mailman.jpg" alt="Delivered by Mailman" border=0><br>version (.+?)</td>#i';
        if (preg_match($p, $html, $m)) {
            return array_pop($m);
        }
    }
} //end
//eof
