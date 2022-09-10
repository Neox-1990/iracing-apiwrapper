<?php

namespace Neox1990\IracingApiwrapper;

use Curl\Curl;
use DateTime;
use Exception;
use KeGi\NetscapeCookieFileHandler\Configuration\Configuration;
use KeGi\NetscapeCookieFileHandler\CookieFileHandler;

abstract class Request
{
    protected $iracingUsername;
    protected $iracingPassword; //Since 2022 Season 3, only the Hash of the Password
    protected $tempDir;

    protected $ratelimitLimit;
    protected $ratelimitRemaining;
    protected $ratelimitReset;

    protected $cookieExpiration;

    public const COOKIEJARNAME = 'iaw_jar.txt';

    /**
     * Constructor
     *
     * @param String $username Email of the account to be used with the api
     * @param String $password Password of the account to be used with the api, will be hashed
     * @param String $passwordHash Hash of the password, if clear password isn't provided, will only be used if $password isn't provided
     * @param String|null $tempDir Folder for temporary files like cookiejar or json fragments, defaults to the package folder     * 
     */
    public function __construct(String $username, String $password = null, String $passwordHash = null, String $tempDir = null)
    {
        $this->iracingUsername = $username;
        if(!is_null($password)){
            $this->iracingPassword = SELF::hashPassword($username, $password);
        }elseif(!is_null($passwordHash)){
            $this->iracingPassword = $passwordHash;
        }else{
            throw new Exception("No password provided");
        }

        $this->tempDir = $tempDir ?? __DIR__.'/';
        $this->readCookieExpiration();
    }

    /**
     * Deletes old cookie-data. Should be called at the start of a lot of consequetive calls.
     *
     * @return void
     */
    public function initReset(){
        @unlink($this->tempDir.self::COOKIEJARNAME);
        $safety = 0;
        while(file_exists($this->tempDir.self::COOKIEJARNAME) && $safety < 10000){
            echo "wait \r\n";
            $safety++;
        }
        $this->auth();
    }

    /**
     * Perfoms the api request to the given api endpoint with the given parameters
     *
     * @param String $apiUrl Api endpoint to be queried
     * @param array $parameter Parameter for the query as an associative array [key1 => value1, key2 => value2, ...]
     * @return Curl Curl object with the reponse and headers after the request
     */
    protected function perform(String $apiUrl, array $parameter)
    :Curl
    {
        if(!$this->checkSession()){
            $this->auth();
        }

        $curl = new Curl();
        $curl->setOpt(CURLOPT_COOKIEFILE, $this->tempDir.self::COOKIEJARNAME);
        $curl->setOpt(CURLOPT_COOKIEJAR, $this->tempDir.self::COOKIEJARNAME);
        $curl->get($apiUrl, $parameter);
        $this->updateRateLimit($curl);
        return $curl;
    }

    /**
     * Checks if the session is still usuable
     * Session could be not usuable due to not being authenticated
     *
     * @return boolean True if session is still usable
     */
    public function checkSession()
    :bool
    {
        return $this->cookieExpiration > new DateTime();
    }

    /**
     * Authenticates the user for the api with the provided credentials.
     * Session cookie will be saved for future requests
     *
     * @return void
     */
    protected function auth()
    {
        $data = [
            'email' => $this->iracingUsername,
            'password' => $this->iracingPassword
        ];
        $curl = new Curl();
        $curl->setOpt(CURLOPT_COOKIEJAR, $this->tempDir.self::COOKIEJARNAME);
        $curl->post('https://members-ng.iracing.com/auth', $data);

        if ($curl->isError()) {
            throw new \Exception("Error while authenticating", 1);
        }
        $curl->close();
        //echo "\nAuthed:\n".$curl->response."\n";

        //die($curl->response);
    }

    /**
     * Takes the used Curl Object and updates the rate limit values from the respons headers
     *
     * @param Curl $curl Curl\Curl object after the request
     * @return void
     */
    protected function updateRateLimit(Curl $curl)
    {
        $headers = $curl->response_headers;
        foreach ($headers as $h) {
            if (str_contains($h, 'x-ratelimit-limit:')) {
                $this->ratelimitLimit = \intval(\substr($h, 18));
            }
            if (str_contains($h, 'x-ratelimit-remaining:')) {
                $this->ratelimitRemaining = \intval(\substr($h, 22));
            }
            if (str_contains($h, 'x-ratelimit-reset:')) {
                $this->ratelimitReset = \DateTime::createFromFormat('U', \substr($h, 18));
            }
        }
    }

    public static function hashPassword(String $username, String $password)
    :String
    {
        $hash = hash('sha256',mb_convert_encoding(trim($password).strtolower(trim($username)),'UTF-8'), true);
        return base64_encode($hash);
    }

    /**
     * Reads the iRacing cookies from the jar and writes the oldest 
     * expiringdate into the class field
     *
     * @return void
     */
    protected function readCookieExpiration()
    :void
    {
        //Check if cookie even exists
        if(!file_exists($this->tempDir.self::COOKIEJARNAME)){
            $this->cookieExpiration = (new DateTime())->setTimestamp(0);
            return;
        }

        //Read cookie
        $configuration = (new Configuration())->setCookieDir($this->tempDir);
        $cookieJar = (new CookieFileHandler($configuration))->parseFile(self::COOKIEJARNAME);
        $cookies = $cookieJar->getAll()->getCookies();

        //Get only iRacing Domain cookies
        $cookies = array_filter($cookies, function($key){
            return str_contains($key, 'iracing.com');
        }, ARRAY_FILTER_USE_KEY);
        //extract all epirations
        $dates = array_reduce($cookies, function($carry, $cookieArray){
            foreach($cookieArray as $cookie){
                $carry[] = $cookie->getExpire();
            }
            return $carry;
        }, []);
        //Sort and save oldest date
        asort($dates);
        $this->cookieExpiration = array_pop($dates);
    }

    abstract protected function getJSON():String;
    abstract protected function getArray():array;
}
