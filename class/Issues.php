<?php

namespace XoopsModules\Tag;

/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
 /**
 * Module: Tag
 *
 * @package   XoopsModules\Tag
 * @author    ZySpec <zyspec@yahoo.com>
 * @copyright Copyright (c) 2001-2019 {@link https://xoops.org XOOPS Project}}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */

/**
 * Issues class to collect information from GitHub
 *
 */
 class Issues {
    /**
     * @var array $hdrs
     */
    protected $hdrs;
    /**
     * @var string $dirname module directory name
     */
    protected $dirname;
    /**
     * @var string $curl_respones response from involking curl
     */
    protected $curl_response;
    /**
     * @var int $hdrSize Curl response header size
     */
    protected $hdrSize;
    /**
     * @var string $serviceUrl Service URL for curl
     */
    protected $serviceUrl;
    /**
     * @var string $sessPrefix prefix for all SESSION vars
     */
    protected $sessPrefix;
    /**
     * @var string $err class error text
     */
    protected $err;

    /**
     * Constructor
     *
     * return void
     */
    public function __construct()
    {
        $this->hdrs          = [];
        $this->curl_response = '';
        $this->hdrSize       = 0;
        $this->dirname       = basename(dirname(__DIR__));
        //$this->serviceUrl    = 'https://api.github.com/repos/xoops/xoopscore25/issues?state=open';
        //$this->serviceUrl    = 'https://github.com/zyspec/' . $this->dirname . '/issues?state=open';
        $this->serviceUrl    = 'https://api.github.com/repos/XoopsModules25x/' . $this->dirname . '/issues?state=open';
        $this->setSessPrefix($this->dirname);
        $this->err           = '';
    }

     /**
      * Function to put HTTP headers in an array
      *
      * @param        $curl
      * @param string $hdrLine
      *
      * @return int length of header line put into array
      */
    public function handleHeaderLine($curl, $hdrLine)
    {
        $this->hdrs[] = trim($hdrLine);
        return strlen($hdrLine);
    }

     /**
      * Function to get a header from the header array
      *
      * @param string $hdr
      * @param bool   $asArray
      *
      * @return array|false array($hdr => value) or false if not found
      */
    public function getHeaderFromArray($hdr, $asArray = false)
    {
        $val = '';
        foreach ($this->hdrs as $thisHdr) {
            if (preg_match("/^{$hdr}/i", $thisHdr)) {
                $val = substr($thisHdr, strlen($hdr));
                break;
            }
        }
        return (bool)$asArray ? [$hdr => trim($val)] : trim($val);
    }
    /**
     * Returns response from involking Curl
     *
     * @param bool $serialized (default = false)
     *
     * @return string
     */
    public function getCurlResponse($serialized = false)
    {
        return (bool)$serialized ? serialize(base64_encode($this->curl_response)) : $this->curl_response;
    }
    /**
     * Get the size of curl response headers
     *
     * @return int size of header in bytes
     */
    public function getHdrSize()
    {
        return $this->hdrSize;
    }
    /**
     * Get the URL for curl
     *
     * @return string
     */
    public function getServiceUrl()
    {
        return $this->serviceUrl;
    }
    /**
     * Get the Prefix for SESSION variable
     *
     * @return string
     */
    public function getSessPrefix()
    {
        return $this->sessPrefix();
    }
    /**
     * Set the Prefix for SESSION variable
     *
     * @param string $prefix string to prepend to session variable
     *
     * @return string prefix
     */
    public function setSessPrefix($prefix)
    {
        $this->sessPrefix = htmlspecialchars($prefix) . '_';
        return $this->sessPrefix;
    }
    /**
     * Get the SESSION variable name for Etag key
     *
     * @return string
     */
    public function getsKeyEtag()
    {
        return $this->sessPrefix . 'github_etag';
    }
    /**
     * Get the SESSION variable name for Header Size key
     *
     * @return string
     */
    public function getsKeyHdrSize()
    {
        return $this->sessPrefix . 'github_hdr_size';
    }
    /**
     * Get the SESSION variable name for Response key
     *
     * @return string
     */
    public function getsKeyResponse()
    {
        return $this->sessPrefix . 'github_curl_response';
    }

     /**
      * Get the SESSION variable name for Array key
      *
      * @return array
      */
    public function getsKeyArray()
    {
        return [$this->getsKeyEtag(), $this->getsKeyHdrSize(), $this->getsKeyResponse()];
    }
    /**
     * Get the SESSION cached Etag key contents
     *
     * @return string|bool Etag key or false if tag not set
     */
    public function getCachedEtag()
    {
        return isset($_SESSION[$this->getsKeyEtag()]) ? base64_decode(unserialize($_SESSION[$this->getsKeyEtag()])) : false;
    }
    /**
     * Set the error message associated with the latest Curl operation
     *
     * @param string $msg the error message to save
     *
     * @return void
     */
    public function setError($msg)
    {
        $this->err = $msg;
    }
    /**
     * Get the error message associated with the latest Curl operation
     *
     * @return string the current error message
     */
    public function getError()
    {
        return $this->err;
    }
    /**
     * Execute a curl operation to retrieve data from GitHub server
     *
     * Also sets the SESSION vars for the curl operation
     *
     * @return int the current header size from curl
     */
    public function execCurl()
    {
        $curl = curl_init($this->getServiceUrl());
        curl_setopt_array($curl, [
                                   CURLOPT_RETURNTRANSFER => true,
                                   CURLOPT_HEADER         => true,
                                   CURLOPT_VERBOSE        => true,
                                   CURLOPT_TIMEOUT        => 5,
                                   CURLOPT_HTTPGET        => true,
                                   CURLOPT_USERAGENT      => 'XOOPS-' . $this->dirname,
                                   CURLOPT_HTTPHEADER     => [
                                       'Content-type:application/json',
                                                                       'If-None-Match: ' . $this->getCachedEtag()
                                   ],
                                   CURLINFO_HEADER_OUT    => true,
                                   CURLOPT_HEADERFUNCTION => [$this, 'handleHeaderLine']
                               ]
        );
        // execute the session
        $this->curl_response = curl_exec($curl);
        // get the header size and finish off the session
        $this->hdrSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $this->hdrSize = (int)$this->hdrSize;
        curl_close($curl);

        $hdrEtag = $this->getHeaderFromArray('Etag: ');

        $_SESSION[$this->getsKeyEtag()]     = serialize(base64_encode($hdrEtag));
        $_SESSION[$this->getsKeyHdrSize()]  = serialize($this->hdrSize);
        $_SESSION[$this->getsKeyResponse()] = serialize(base64_encode($this->curl_response));
        return $this->hdrSize;
    }
}
