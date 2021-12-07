<?php declare(strict_types=1);

//declare(strict_types=1); //mb do not use it here, as it generates conflict on line 280: base64_encode(): Argument #1 ($string) must be of type string, bool given

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
 * @author    ZySpec <zyspec@yahoo.com>
 * @copyright Copyright (c) 2001-2019 {@link https://xoops.org XOOPS Project}}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */

/**
 * Issues class to collect information from GitHub
 */
class Issues
{
    /**
     * @var array
     */
    protected $hdrs;
    /**
     * @var string module directory name
     */
    protected $dirname;
    /**
     * @var string response from involking curl
     */
    protected $curl_response;
    /**
     * @var int Curl response header size
     */
    protected $hdrSize;
    /**
     * @var string Service URL for curl
     */
    protected $serviceUrl;
    /**
     * @var string prefix for all SESSION vars
     */
    protected $sessPrefix;
    /**
     * @var string class error text
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
        $this->dirname       = \basename(\dirname(__DIR__));
        //$this->serviceUrl    = 'https://api.github.com/repos/xoops/xoopscore25/issues?state=open';
        //$this->serviceUrl    = 'https://github.com/zyspec/' . $this->dirname . '/issues?state=open';
        $this->serviceUrl = 'https://api.github.com/repos/XoopsModules25x/' . $this->dirname . '/issues?state=open';
        $this->setSessPrefix($this->dirname);
        $this->err = '';
    }

    /**
     * Function to put HTTP headers in an array
     *
     * @param resource $curl
     *
     * @return int length of header line put into array
     */
    public function handleHeaderLine($curl, string $hdrLine): int
    {
        $this->hdrs[] = \trim($hdrLine);

        return mb_strlen($hdrLine);
    }

    /**
     * Function to get a header from the header array
     *
     *
     * @return array|string array($hdr => value) or false if not found
     */
    public function getHeaderFromArray(string $hdr, bool $asArray = false)
    {
        $val = '';
        foreach ($this->hdrs as $thisHdr) {
            if (\preg_match("/^{$hdr}/i", $thisHdr)) {
                $val = mb_substr($thisHdr, mb_strlen($hdr));
                break;
            }
        }

        return $asArray ? [$hdr => \trim($val)] : \trim($val);
    }

    /**
     * Returns response from involking Curl
     *
     * @param bool $serialized (default = false)
     */
    public function getCurlResponse(bool $serialized = false): string
    {
        return $serialized ? \serialize(\base64_encode($this->curl_response)) : $this->curl_response;
    }

    /**
     * Get the size of curl response headers
     *
     * @return int size of header in bytes
     */
    public function getHdrSize(): int
    {
        return $this->hdrSize;
    }

    /**
     * Get the URL for curl
     */
    public function getServiceUrl(): string
    {
        return $this->serviceUrl;
    }

    /**
     * Get the Prefix for SESSION variable
     */
    public function getSessPrefix(): string
    {
        return $this->sessPrefix;
    }

    /**
     * Set the Prefix for SESSION variable
     *
     * @param string $prefix string to prepend to session variable
     *
     * @return string prefix
     */
    public function setSessPrefix(string $prefix): string
    {
        $this->sessPrefix = \htmlspecialchars($prefix, \ENT_QUOTES | \ENT_HTML5) . '_';

        return $this->sessPrefix;
    }

    /**
     * Get the SESSION variable name for Etag key
     */
    public function getsKeyEtag(): string
    {
        return $this->sessPrefix . 'github_etag';
    }

    /**
     * Get the SESSION variable name for Header Size key
     */
    public function getsKeyHdrSize(): string
    {
        return $this->sessPrefix . 'github_hdr_size';
    }

    /**
     * Get the SESSION variable name for Response key
     */
    public function getsKeyResponse(): string
    {
        return $this->sessPrefix . 'github_curl_response';
    }

    /**
     * Get the SESSION variable name for Array key
     */
    public function getsKeyArray(): array
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
        return isset($_SESSION[$this->getsKeyEtag()]) ? \base64_decode(\unserialize($_SESSION[$this->getsKeyEtag()]), true) : false;
    }

    /**
     * Set the error message associated with the latest Curl operation
     *
     * @param string $msg the error message to save
     */
    public function setError(string $msg): void
    {
        $this->err = $msg;
    }

    /**
     * Get the error message associated with the latest Curl operation
     *
     * @return string the current error message
     */
    public function getError(): string
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
    public function execCurl(): int
    {
        $curl = \curl_init($this->getServiceUrl());
        \curl_setopt_array(
            $curl,
            [
                \CURLOPT_RETURNTRANSFER => true,
                \CURLOPT_HEADER         => true,
                \CURLOPT_VERBOSE        => true,
                \CURLOPT_TIMEOUT        => 5,
                \CURLOPT_HTTPGET        => true,
                \CURLOPT_USERAGENT      => 'XOOPS-' . \mb_strtoupper($this->dirname),
                \CURLOPT_HTTPHEADER     => [
                    'Content-type:application/json',
                    'If-None-Match: ' . $this->getCachedEtag(),
                ],
                \CURLINFO_HEADER_OUT    => true,
                \CURLOPT_HEADERFUNCTION => [$this, 'handleHeaderLine'],
            ]
        );
        // execute the session
        $this->curl_response = \curl_exec($curl);
        // get the header size and finish off the session
        $this->hdrSize = \curl_getinfo($curl, \CURLINFO_HEADER_SIZE);
        $this->hdrSize = (int)$this->hdrSize;
        \curl_close($curl);

        $hdrEtag = $this->getHeaderFromArray('Etag: ');

        $_SESSION[$this->getsKeyEtag()]     = \serialize(\base64_encode($hdrEtag));
        $_SESSION[$this->getsKeyHdrSize()]  = \serialize($this->hdrSize);
        $_SESSION[$this->getsKeyResponse()] = \serialize(\base64_encode($this->curl_response));

        return $this->hdrSize;
    }
}
