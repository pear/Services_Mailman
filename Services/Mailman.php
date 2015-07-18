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
 * @author    James Wade
 * @copyright 2011 James Wade
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   GIT: $Id:$
 * @link      http://php-mailman.sourceforge.net/
 */

require_once 'HTTP/Request2.php';
require_once 'Services/Mailman/Exception.php';

/**
 * Mailman Class
 *
 * @category  Services
 * @package   Services_Mailman
 * @author    James Wade
 * @copyright 2011 James Wade
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: @package_version@
 * @link      http://php-mailman.sourceforge.net/
 */
class Services_Mailman
{
    /**
     * Default URL to the Mailman "Admin Links" page (no trailing slash)
     *  For example: 'http://www.example.co.uk/mailman/admin'
     * @var string
     */
    protected $adminUrl;
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
    protected $adminPw;
    /**
     * A HTTP request instance
     *
     * @var HTTP_Request2 $request
     */
    public $request = null;
    /**
     * Constructor
     *
     * @param string        $adminUrl Set the URL to the Mailman "Admin Links" page
     * @param string        $list     Set the name of the list
     * @param string        $adminPw  Set admin password of the list
     * @param HTTP_Request2 $request  Provide your HTTP request instance
     */
    public function __construct($adminUrl, $list = '', $adminPw = '', HTTP_Request2 $request = null)
    {
        $this->setList($list);
        $this->setAdminUrl($adminUrl);
        $this->setAdminPw($adminPw);
        $this->setRequest($request);
    }

    /**
     * Sets the list name
     *
     * @param string $string The name of the list
     *
     * @return Services_Mailman
     *
     * @throws Services_Mailman_Exception
     */
    public function setList($string)
    {
        if (!is_string($string)) {
            throw new Services_Mailman_Exception(
                'setList() expects parameter 1 to be string, ' .
                gettype($string) . ' given',
                Services_Mailman_Exception::USER_INPUT
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
     * @throws Services_Mailman_Exception
     */
    public function setAdminUrl($string)
    {
        if (empty($string)) {
            throw new Services_Mailman_Exception(
                'setAdminUrl() does not expect parameter 1 to be empty',
                Services_Mailman_Exception::USER_INPUT
            );
        }
        if (!is_string($string)) {
            throw new Services_Mailman_Exception(
                'setAdminUrl() expects parameter 1 to be string, ' .
                gettype($string) . ' given',
                Services_Mailman_Exception::USER_INPUT
            );
        }
        $string = filter_var($string, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
        if (!$string) {
            throw new Services_Mailman_Exception(
                'Invalid URL',
                Services_Mailman_Exception::INVALID_URL
            );
        }
        $this->adminUrl = trim($string, '/');
        return $this;
    }
    /**
     * Sets the admin password of the list
     *
     * @param string $string The password string
     *
     * @return Services_Mailman
     *
     * @throws Services_Mailman_Exception
     */
    public function setAdminPw($string)
    {
        if (!is_string($string)) {
            throw new Services_Mailman_Exception(
                'setAdminPw() expects parameter 1 to be string, ' .
                gettype($string) . ' given',
                Services_Mailman_Exception::USER_INPUT
            );
        }
        $this->adminPw = $string;
        return $this;
    }
    /**
     * Sets the request object
     *
     * @param HTTP_Request2 $object A HTTP request instance (otherwise one will be created)
     *
     * @return Services_Mailman
     */
    public function setRequest(HTTP_Request2 $object = null)
    {
        $this->request = ($object instanceof HTTP_Request2) ? $object : new HTTP_Request2();
        return $this;
    }

    /**
     * Fetches the HTML to be parsed
     *
     * @param string $url A valid URL to fetch
     *
     * @return string Return contents from URL (usually HTML)
     *
     * @throws Services_Mailman_Exception
     */
    protected function fetch($url)
    {
        $url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
        if (!$url) {
            throw new Services_Mailman_Exception(
                'Invalid URL',
                Services_Mailman_Exception::INVALID_URL
            );
        }
        try {
            $this->request->setUrl($url);
            $this->request->setMethod('GET');
            $html = $this->request->send()->getBody();
        } catch (HTTP_Request2_Exception $e) {
            throw new Services_Mailman_Exception(
                $e,
                Services_Mailman_Exception::HTML_FETCH
            );
        } 
        if (strlen($html)>5) {
            return $html;
        }
        throw new Services_Mailman_Exception(
            'Could not fetch HTML',
            Services_Mailman_Exception::HTML_FETCH
        );
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
     * @throws Services_Mailman_Exception
     */
    public function lists($assoc = true)
    {
        $html = $this->fetch($this->adminUrl);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $paths = $xpath->query('/html/body/table[1]/tr/td[1]/a/@href');
        $names = $xpath->query('/html/body/table[1]/tr/td[1]/a/strong');
        $descs = $xpath->query('/html/body/table[1]/tr/td[2]');
        $count = $names->length;
        if (!$count) {
            throw new Services_Mailman_Exception(
                'Failed to parse HTML',
                Services_Mailman_Exception::HTML_PARSE
            );
        }
        $a = array();
        for ($i=0;$i < $count;$i++) {
            if ($paths->item($i)) {
                $a[$i][0]=$paths->item($i)?basename($paths->item($i)->nodeValue):'';
                $a[$i][1]=$names->item($i)?$names->item($i)->nodeValue:'';
                $a[$i][2]=$descs->item($i)?$descs->item($i+2)->textContent:'';
                if ($assoc) {
                    $a[$i]['path'] = $a[$i][0];
                    $a[$i]['name'] = $a[$i][1];
                    $a[$i]['desc'] = $a[$i][2];
                }
            }
        }
        libxml_clear_errors();
        return $a;
    }

    /**
     * Parse and Return General List info
     *
     * (ie: <domain.com>/mailman/admin/<listname>)
     *
     * @param string $string list name
     *
     * @return string Return an array of list information 
     *
     * @throws Services_Mailman_Exception
     */
    public function listinfo($string)
    {
        if (!is_string($string)) {
            throw new Services_Mailman_Exception(
                'member() expects parameter 1 to be string, ' .
                gettype($string) . ' given',
                Services_Mailman_Exception::USER_INPUT
            );
        }
        $path  = '/' . $string;
        $query = array(
            'adminpw'           => $this->adminPw
        );

        $query = http_build_query($query, '', '&');
        $url   = $this->adminUrl . $path . '?' . $query;
        $html  = $this->fetch($url);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);

        $a = array();
        $queries = array();
        $queries[] = $xpath->query("//input");
        $queries[] = $xpath->query("//textarea");

        $ignore_types = array(
            'submit',
            'hidden',
        );

         //get inputs
         foreach ($queries as $query) {
            foreach ($query as $item){
               $type = strtolower($item->getAttribute('type'));
               $type = (empty($type)) ? 'textarea' : $type;
               if(in_array($type,$ignore_types)) continue; //ignore defined types

               $name = $item->getAttribute('name');
               $value = ($type === 'textarea') ? $item->nodeValue : $item->getAttribute('value');
               $checked = $item->getAttribute('checked');

               //initialize checkbox array if it's not set
               if($type === 'checkbox' and !isset($a[$name])) $a[$name] = array();

               //skip non checked values
               if($type === 'radio' and $checked !== 'checked') continue;
               if($type === 'checkbox' and $checked !== 'checked') continue;


               if($type === 'checkbox') {
                  $a[$name][] = $value;
               } else {
                  $a[$name] = $value;
               }
            }
         }
         ksort($a);
         return $a;
    }

    /**
     * Find a member
     *
     * (ie: <domain.com>/mailman/admin/<listname>/members?findmember=<email-address>
     *      &setmemberopts_btn&adminpw=<adminpassword>)
     *
     * @param string $string A search string for member
     *
     * @return string Return an array of members (and their options) that match the string
     *
     * @throws Services_Mailman_Exception
     */
    public function member($string)
    {
        if (!is_string($string)) {
            throw new Services_Mailman_Exception(
                'member() expects parameter 1 to be string, ' .
                gettype($string) . ' given',
                Services_Mailman_Exception::USER_INPUT
            );
        }
        $path  = '/' . $this->list . '/members';
        $query = array(
            'findmember'        => $string, 
            'setmemberopts_btn' => null,
            'adminpw'           => $this->adminPw
        );

        $query = http_build_query($query, '', '&');
        $url   = $this->adminUrl . $path . '?' . $query;
        $html  = $this->fetch($url);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $queries = array();
        $queries['address'] = $xpath->query('/html/body/form/center/table/tr/td[2]/a');
        $queries['realname'] = $xpath->query('/html/body/form/center/table/tr/td[2]/input[type=TEXT]/@value');
        $queries['mod'] = $xpath->query('/html/body/form/center/table/tr/td[3]/center/input/@value');
        $queries['hide'] = $xpath->query('/html/body/form/center/table/tr/td[4]/center/input/@value');
        $queries['nomail'] = $xpath->query('/html/body/form/center/table/tr/td[5]/center/input/@value');
        $queries['ack'] = $xpath->query('/html/body/form/center/table/tr/td[6]/center/input/@value');
        $queries['notmetoo'] = $xpath->query('/html/body/form/center/table/tr/td[7]/center/input/@value');
        $queries['nodupes'] = $xpath->query('/html/body/form/center/table/tr/td[8]/center/input/@value');
        $queries['digest'] = $xpath->query('/html/body/form/center/table/tr/td[9]/center/input/@value');
        $queries['plain'] = $xpath->query('/html/body/form/center/table/tr/td[10]/center/input/@value');
        $queries['language'] = $xpath->query('/html/body/form/center/table/tr/td[11]/center/select/option[@selected]/@value');
        libxml_clear_errors();
        $count = $queries['address']->length;
        if (!$count) {
            throw new Services_Mailman_Exception(
                'No match',
                Services_Mailman_Exception::NO_MATCH
            );
        }
        $a = array();
        for ($i=0;$i < $count;$i++) {
            foreach ($queries as $key => $query) {
                $a[$i][$key] = $query->item($i) ? $query->item($i)->nodeValue : '';
            }
        }
        return $a;
    }
    
    /**
     * Unsubscribe
     *
     * (ie: <domain.com>/mailman/admin/<listname>/members/remove?send_unsub_ack_to_this_batch=0
     *      &send_unsub_notifications_to_list_owner=0&unsubscribees=<email-address>&adminpw=<adminpassword>)
     *
     * @param string $email Valid email address of a member to unsubscribe
     *
     * @return Services_Mailman
     *
     * @throws Services_Mailman_Exception
     */
    public function unsubscribe($email)
    {
        $path = '/' . $this->list . '/members/remove';
        $query = array(
            'send_unsub_ack_to_this_batch' => 0,
            'send_unsub_notifications_to_list_owner' => 0,
            'unsubscribees' => $email,
            'adminpw' => $this->adminPw
        );
        $query = http_build_query($query, '', '&');
        $url = $this->adminUrl . $path . '?' . $query;
        $html = $this->fetch($url);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $h5 = $xpath->query('/html/body/h5');
        $h3 = $xpath->query('/html/body/h3');
        libxml_clear_errors();
        if ($h5->item(0) && $h5->item(0)->nodeValue == 'Successfully Unsubscribed:') {
            return $this;
        }
        if ($h3) {
            throw new Services_Mailman_Exception(
                trim($h3->item(0)->nodeValue, ':'),
                Services_Mailman_Exception::HTML_PARSE
            );
        }
        throw new Services_Mailman_Exception(
            'Failed to parse HTML',
            Services_Mailman_Exception::HTML_PARSE
        );
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
     * @throws Services_Mailman_Exception
     */
    public function subscribe($email, $invite = false)
    {
        $path = '/' . $this->list . '/members/add';
        $query = array('subscribe_or_invite' => (int)$invite,
                        'send_welcome_msg_to_this_batch' => 0,
                        'send_notifications_to_list_owner' => 0,
                        'subscribees' => $email,
                        'adminpw' => $this->adminPw);
        $query = http_build_query($query, '', '&');
        $url = $this->adminUrl . $path . '?' . $query;
        $html = $this->fetch($url);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $h5 = $xpath->query('/html/body/h5');
        libxml_clear_errors();
        if (!is_object($h5) || $h5->length == 0) {
            return false;
        }
        if ($h5->item(0)->nodeValue) {
            if ($h5->item(0)->nodeValue == 'Successfully subscribed:') {
                return $this;
            } else {
                throw new Services_Mailman_Exception(
                    trim($h5->item(0)->nodeValue, ':'),
                    Services_Mailman_Exception::HTML_PARSE
                );
            }
        }
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
     * @param string $email  Valid email address of a member
     * 
     * @param bool   $digest Set the Digest on (1) or off (0)
     *
     * @return string Returns 1 if set on, or 0 if set off.
     *
     * @throws Services_Mailman_Exception
     */
    public function setDigest($email, $digest = 1)
    {
        return $this->setOption($email, 'digest', $digest ? 1 : 0);
    }

    /**
     * Set an option
     *
     * @param string $email  Valid email address of a member
     *
     * @param string $option A valid option (new-address, fullname, newpw, disablemail, digest, mime, dontreceive, ackposts, remind, conceal, rcvtopic, nodupes)
     *
     * @param string $value  A value for the given option
     *
     * @return string Returns resulting value, if successful.
     *
     * @throws Services_Mailman_Exception
     */
    public function setOption($email, $option, $value)
    {
        if (!is_string($email)) {
            throw new Services_Mailman_Exception(
                'setOption() expects parameter 1 to be string, ' .
                gettype($email) . ' given',
                Services_Mailman_Exception::USER_INPUT
            );
        }
        $path = '/options/' . $this->list . '/' . str_replace('@', '--at--', $email);
        $query = array('password' => $this->adminPw);
        if ($option == 'new-address') {
            $query['new-address'] = $value;
            $query['confirm-address'] = $value;
            $query['change-of-address'] = 'Change+My+Address+and+Name';
            $xp = "//input[@name='$option']/@value";
        } elseif ($option == 'fullname') {
            $query['fullname'] = $value;
            $query['change-of-address'] = 'Change+My+Address+and+Name';
            $xp = "//input[@name='$option']/@value";
        } elseif ($option == 'newpw') {
            $query['newpw'] = $value;
            $query['confpw'] = $value;
            $query['changepw'] = 'Change+My+Password';
            $xp = "//input[@name='$option']/@value";
        } elseif ($option == 'disablemail') {
            $query['disablemail'] = $value;
            $query['options-submit'] = 'Submit+My+Changes';
            $xp = "//input[@name='$option' and @checked]/@value";
        } elseif ($option == 'digest') {
            $query['digest'] = $value;
            $query['options-submit'] = 'Submit+My+Changes';
            $xp = "//input[@name='$option' and @checked]/@value";
        } elseif ($option == 'mime') {
            $query['mime'] = $value;
            $query['options-submit'] = 'Submit+My+Changes';
            $xp = "//input[@name='$option' and @checked]/@value";
        } elseif ($option == 'dontreceive') {
            $query['dontreceive'] = $value;
            $query['options-submit'] = 'Submit+My+Changes';
            $xp = "//input[@name='$option' and @checked]/@value";
        } elseif ($option == 'ackposts') {
            $query['ackposts'] = $value;
            $query['options-submit'] = 'Submit+My+Changes';
            $xp = "//input[@name='$option' and @checked]/@value";
        } elseif ($option == 'remind') {
            $query['remind'] = $value;
            $query['options-submit'] = 'Submit+My+Changes';
            $xp = "//input[@name='$option' and @checked]/@value";
        } elseif ($option == 'conceal') {
            $query['conceal'] = $value;
            $query['options-submit'] = 'Submit+My+Changes';
            $xp = "//input[@name='$option' and @checked]/@value";
        } elseif ($option == 'rcvtopic') {
            $query['rcvtopic'] = $value;
            $query['options-submit'] = 'Submit+My+Changes';
            $xp = "//input[@name='$option' and @checked]/@value";
        } elseif ($option == 'nodupes') {
            $query['nodupes'] = $value;
            $query['options-submit'] = 'Submit+My+Changes';
            $xp = "//input[@name='$option' and @checked]/@value";
        } else {
            throw new Services_Mailman_Exception(
                'Invalid option',
                Services_Mailman_Exception::INVALID_OPTION
            );
        }
        $query = http_build_query($query, '', '&');
        $url = dirname($this->adminUrl) . $path . '?' . $query;
        $html = $this->fetch($url);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $query = $xpath->query($xp);
        libxml_clear_errors();
        if ($query->item(0)) {
            return $query->item(0)->nodeValue;
        }
        throw new Services_Mailman_Exception(
            'Failed to parse HTML',
            Services_Mailman_Exception::HTML_PARSE
        );
    }

    /**
     * List members
     *
     * @return array  Returns two nested arrays, the first contains email addresses, the second contains names
     */
    public function members()
    {
        $path = '/' . $this->list . '/members';
        $query = array('adminpw' => $this->adminPw);
        $query = http_build_query($query, '', '&');
        $url = $this->adminUrl . $path . '?' . $query;
        $html = $this->fetch($url);
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
            $query = array('adminpw' => $this->adminPw);
            if ($letter != null) {
                $query['letter'] = $letter;
                $query = http_build_query($query, '', '&');
                $url = $this->adminUrl . $path . '?' . $query;
                $html = $this->fetch($url);
            }
            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->loadHTML($html);
            $xpath = new DOMXPath($doc);
            $emails = $xpath->query('/html/body/form/center[1]/table/tr/td[2]/a');
            $names = $xpath->query('/html/body/form/center[1]/table/tr/td[2]/input[1]/@value');
            $count = $emails->length;
            for ($i=0;$i < $count;$i++) {
                if ($emails->item($i)) {
                    $members[0][]=$emails->item($i)->nodeValue;
                }
                if ($names->item($i)) {
                    $members[1][]=$names->item($i)->nodeValue;
                }
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
     * @throws Services_Mailman_Exception
     */
    public function version()
    {
        $path = '/' . $this->list . '/';
        $query = array('adminpw' => $this->adminPw);
        $query = http_build_query($query, '', '&');
        $url = $this->adminUrl . $path . '?' . $query;
        $html = $this->fetch($url);
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
        throw new Services_Mailman_Exception(
            'Failed to parse HTML',
            Services_Mailman_Exception::HTML_PARSE
        );
    }
} //end
//eof

