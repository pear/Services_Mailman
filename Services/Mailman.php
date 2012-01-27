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
 * @package   Services_Mailman
 * @author    James Wade <hm2k@php.net>
 * @copyright 2011 James Wade
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   GIT: $Id:$
 * @link      http://php-mailman.sf.net/
 */

require_once 'HTTP/Request2.php';
require_once 'Services/Mailman/Exception.php';

/**
 * Mailman Class
 *
 * @category  Services
 * @package   Services_Mailman
 * @author    James Wade <hm2k@php.net>
 * @copyright 2011 James Wade
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: @package_version@
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
    public $request = null;
    /**
     * Constructor
     *
     * @param string        $adminURL Set the URL to the Mailman "Admin Links" page
     * @param string        $list     Set the name of the list
     * @param string        $adminPW  Set admin password of the list
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
     * @throws {@link Services_Mailman_Exception}
     */
    public function setList($string)
    {
        if (empty($string)) {
            throw new Services_Mailman_Exception(
                'setList() does not expect parameter 1 to be empty'
            );
        }
        if (!is_string($string)) {
            throw new Services_Mailman_Exception(
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
     * @throws {@link Services_Mailman_Exception}
     */
    public function setadminURL($string)
    {
        if (empty($string)) {
            throw new Services_Mailman_Exception(
                'setadminURL() does not expect parameter 1 to be empty'
            );
        }
        if (!is_string($string)) {
            throw new Services_Mailman_Exception(
                'setadminURL() expects parameter 1 to be string, ' .
                gettype($string) . ' given'
            );
        }
        $string = filter_var($string, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
        if (!$string) {
            throw new Services_Mailman_Exception('Invalid URL');
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
     * @throws {@link Services_Mailman_Exception}
     */
    public function setadminPW($string)
    {
        if (!is_string($string)) {
            throw new Services_Mailman_Exception(
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
     * @throws {@link Services_Mailman_Exception}
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
     * @throws {@link Services_Mailman_Exception}
     */
    protected function fetch($url)
    {
        $url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
        if (!$url) {
            throw new Services_Mailman_Exception('Invalid URL');
        }
        $this->request->setUrl($url);
        $this->request->setMethod('GET');
        $html = $this->request->send()->getBody();
        if (strlen($html)>5) {
            return $html;
        }
        throw new Services_Mailman_Exception('Could not fetch HTML.');
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
     * @throws {@link Services_Mailman_Exception}
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
                throw new Services_Mailman_Exception('Unable to match any lists');
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
            throw new Services_Mailman_Exception('Failed to parse HTML.');
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
     * @throws {@link Services_Mailman_Exception}
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
            throw new Services_Mailman_Exception('Unable to fetch HTML.');
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
     * @throws {@link Services_Mailman_Exception}
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
            throw new Services_Mailman_Exception('Unable to fetch HTML.');
        }
        if (preg_match('#<h5>Successfully Unsubscribed:</h5>#i', $html)) {
            return $this;
        }
        if (preg_match('#<h3>(.+?)</h3>#i', $html, $m)) {
            throw new Services_Mailman_Exception(trim(strip_tags($m[1]), ':'));
        }
        throw new Services_Mailman_Exception('Failed to parse HTML.');
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
     * @throws {@link Services_Mailman_Exception}
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
            throw new Services_Mailman_Exception('Unable to fetch HTML.');
        }
        if (preg_match('#<h5>Successfully subscribed:</h5>#i', $html)) {
            return $this;
        }
        if (preg_match('#<h5>(.+?)</h5>#i', $html, $m)) {
            throw new Services_Mailman_Exception(trim(strip_tags($m[1]), ':'));
        }
        throw new Services_Mailman_Exception('Failed to parse HTML.');
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
     * @param bool $digest Set the Digest on (1) or off (0)
     *
     * @return string Returns unparsed HTML
     *
     * @throws {@link Services_Mailman_Exception}
     */
    public function setDigest($email,$digest = 1)
    {
        return setOption($email, 'digest', $digest ? 1 : 0);
    }

    /**
     * Set options
     *
     * @param string $email Valid email address of a member
     *
     * @param string $option A valid option
     *
     * @param string $value A value for the given option
     *
     * @return string Returns unparsed HTML
     *
     * @throws {@link Services_Mailman_Exception}
     */
    public function setOption($email, $option, $value)
    {
        if (!is_string($email)) {
            throw new Services_Mailman_Exception(
                'setOption() expects parameter 1 to be string, ' .
                gettype($email) . ' given'
            );
        }
        $path = '/options/' . $this->list . '/' . $email;
        $query = array( 'options-submit' => 'Submit+My+Changes',
                        'adminpw' => $this->adminPW);
        $options = array( 'new-address',
            'fullname',
            'newpw',
            'disablemail',
            'digest','mime',
            'dontreceive',
            'ackposts',
            'remind',
            'conceal',
            'rcvtopic',
            'nodupes');
        if ($option == 'new-address') {
            $query['new-address'] = $value;
            $query['confirm-address'] = $value;
        }
        elseif ($option == 'fullname') {
            $query['fullname'] = $value;
        }
        elseif ($option == 'newpw') {
            $query['newpw'] = $value;
            $query['confpw'] = $value;
        }
        elseif ($option == 'disablemail') {
            $query['disablemail'] = $value;
        }
        elseif ($option == 'digest') {
            $query['digest'] = $value;
        }
        elseif ($option == 'mime') {
            $query['mime'] = $value;
        }
        elseif ($option == 'dontreceive') {
            $query['dontreceive'] = $value;
        }
        elseif ($option == 'ackposts') {
            $query['ackposts'] = $value;
        }
        elseif ($option == 'remind') {
            $query['remind'] = $value;
        }
        elseif ($option == 'conceal') {
            $query['conceal'] = $value;
        }
        elseif ($option == 'rcvtopic') {
            $query['rcvtopic'] = $value;
        }
        elseif ($option == 'nodupes') {
            $query['nodupes'] = $value;
        }
        else {
            throw new Services_Mailman_Exception('Invalid option.');
        }

        $query = http_build_query($query, '', '&');
        $url = dirname($this->adminURL) . $path . '?' . $query;
        $html = $this->fetch($url);
        if (!$html) {
            throw new Services_Mailman_Exception('Unable to fetch HTML.');
        }
        //TODO:parse html
        return $html;
    }

    /**
     * List members
     *
     * @return array  Returns a lits of members names and email addresses
     *
     * @throws {@link Services_Mailman_Exception}
     */
    public function members()
    {
        $path = '/' . $this->list . '/members';
        $query = array('adminpw' => $this->adminPW);
        $query = http_build_query($query, '', '&');
        $url = $this->adminURL . $path . '?' . $query;
        $html = $this->fetch($url);
        if (!$html) {
            throw new Services_Mailman_Exception('Unable to fetch HTML.');
        }
        //echo $html; die();
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $letters = $xpath->query('/html/body/form/center[1]/table/tr[2]/td/center/a');
        libxml_clear_errors();

        if ($letters->length>0) {
            $letters = range('a', 'z');
        } else {
            $letters = array(null);
        }
        $members = array(array(), array());
        foreach ($letters as $letter) {
            $query = array('adminpw' => $this->adminPW);
            if ($letter != null) {
                $query['letter'] = $letter;
                $query = http_build_query($query, '', '&');
                $url = $this->adminURL . $path . '?' . $query;
                $html = $this->fetch($url);
            }
            if (!$html) {
                throw new Services_Mailman_Exception('Unable to fetch HTML.');
            }
            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->loadHTML($html);
            $xpath = new DOMXPath($doc);
            $emails = $xpath->query('/html/body/form/center[1]/table/tr/td[2]/a');
            $names = $xpath->query('/html/body/form/center[1]/table/tr/td[2]/input[1]/@value');
            $count = $emails->length;
            for ($i=0;$i <= $count;$i++) {
                if ($emails->item($i)) { $members[0][]=$emails->item($i)->nodeValue; }
                if ($names->item($i)) { $members[1][]=$names->item($i)->nodeValue; }
            }
            libxml_clear_errors();
        }
        return $members;
    }
    /**
     * Version
     *
     * @return string Returns the version of Mailman
     *
     * @throws {@link Services_Mailman_Exception}
     */
    public function version()
    {
        $path = '/' . $this->list . '/';
        $query = array('adminpw' => $this->adminPW);
        $query = http_build_query($query, '', '&');
        $url = $this->adminURL . $path . '?' . $query;
        $html = $this->fetch($url);
        if (!$html) {
            throw new Services_Mailman_Exception('Unable to fetch HTML.');
        }
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $content = $xpath->query('//table[last()]')->item(0)->textContent;
        libxml_clear_errors();
        if (preg_match('#version ([\d-.]+)#is', $content, $m)) {
            return array_pop($m);
        }
        throw new Services_Mailman_Exception('Failed to parse HTML.');
    }
} //end
//eof