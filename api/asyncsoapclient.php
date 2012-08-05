<?php
/**
 *  Asynchronous SOAP api client.
 *  This is an extension to the normal soap client, because it can run multiple
 *  calls at the same time. It differs from the normal PomSoapClient class because
 *  the PxPomSoapClient::methodToCall() method does not return the result, but
 *  a handle that can be queried to see if it has already returned data.
 *
 *  Example use:
 *  
 *  $client = new PomAsyncSoapClient($url, $login, $account, $password);
 *  $req1 = $client->someSoapMethod(...);
 *  $req2 = $client->someSoapMethod(...);
 *  $req3 = $client->someSoapMethod(...);
 *  $answer1 = $client->result($req1);
 *  $answer2 = $client->result($req2);
 *  $answer3 = $client->result($req3);
 */
require_once(__DIR__.'/soapclient.php');
class PomAsyncSoapClient extends PomSoapClient
{
    /**
     *  The curl multi handle
     *  @var resource
     */
    private $curl = false;
    
    /**
     *  Set of pending requests ID's
     *  This is an assoc array: request ID maps to a array with handle and request
     *  @var array
     */
    private $pending = array();
    
    /**
     *  Set of requests for which the answer has been received
     *  This is an assoc array: request ID maps to the received answer
     *  @var array
     */
    private $completed = array();
     
    /**
     *  The last assigned request ID
     *  @var integer
     */
    private $freeID = 0;

    /**
     *  Is the object currently busy parsing an async answer?
     *  @var resource   CURL resource identifier of the request that is internally processed
     */
    private $internalRequest = false;

    /**
     *  The cookie used for authenticating
     *  @var string
     */
    private $cookie = '';

    /**
     *  Destructor
     */
    public function __destruct()
    {
        // skip if no calls are pending
        if(!count($this->pending)) return;

        // wait until everything is ready
        $this->run();
        
        // close the connections
        curl_multi_close($this->curl);
    }
    
    /**
     *  Method returns the ID's of all requests, both the pending ones and the
     *  ones that have already been completed
     *  @return array of int
     */
    public function allRequests()
    {
        return array_merge($this->pendingRequests(), $this->completedRequests());
    }
    
    /**
     *  Method to retrieve the ID's of all pending requests
     *  @return array
     */
    public function pendingRequests()
    {
        return array_keys($this->pending);
    }
    
    /**
     *  Method to retrieve the ID's of all completed requests
     *  @return array
     */
    public function completedRequests()
    {
        return array_keys($this->completed);
    }
    
    /**
     *  Get the result of a certain request
     *  This method will block until the request has been completed
     *  @param  integer     ID of a request
     *  @return mixed       The response from the request
     */
    public function result($requestID)
    {
        // if this a request for which the result was already found
        if (isset($this->completed[$requestID])) return $this->completed[$requestID];
        
        // skip if an invalid ID was supplied
        if (!isset($this->pending[$requestID])) return false;
    
        // wait for the next call
        $this->wait();
        
        // fetch the result (with recursion)
        return $this->result($requestID);
    }
    
    /**
     *  Wait for the next pending request to complete
     *  This method will block until a SOAP call completes
     *  It returns the request ID of the soap call that completed
     *  @param  float       Timeout in seconds
     *  @return integer     The ID of the request that was completed
     */
    public function wait($timeout = 1.0)
    {
        // not possible when nothing is pending
        if (count($this->pending) == 0) return false;
    
        // exec the connections
        $active = null;
        while(($execrun = curl_multi_exec($this->curl, $active)) == CURLM_CALL_MULTI_PERFORM) { /* do nothing */ }
        
        // run a select call to wait for a connection to become ready
        $ready = curl_multi_select($this->curl, $timeout);
        
        // find all requests that are ready
        while ($info = curl_multi_info_read($this->curl))
        {
            // find the request ID
            $requestID = $this->resource2id($info['handle']);
            $info = $this->pending[$requestID];
            
            // make an internal call to find the answer
            $this->internalRequest = $info['handle'];
            $answer = $this->__call($info['method'], $info['params']);
            $this->internalRequest = false;

            // Check if a cookie was set
            if($answer == 'Cookie set')
            {
                // get the method name
                $method = $info['method'];

                // Retry the failed call
                $res = $this->$method($info['params'][0]);

                // Overwrite the failed method with the new request
                $this->pending[$requestID] = $this->pending[$res];
                unset($this->pending[$res]);

                // Wait for the next answer
                continue;
            }

            // we have the answer
            $this->completed[$requestID] = $answer;

            // request is no longer pending
            unset($this->pending[$requestID]);
            
            // resource is no longer busy
            curl_multi_remove_handle($this->curl, $info['handle']);
            curl_close($info['handle']);
        
            // done
            return $requestID;
        }
        
        // not found
        return false;
    }
    
    /**
     *  Run all requests
     *  This method will process all requests, until none of them is pending
     */
    public function run()
    {
        // keep waiting until all requests are completed
        while (count($this->pending) > 0) $this->wait();
    }
    
    /**
     *  Helper method to map a curl resource to a request ID
     *  @param  resource    CURL resource
     *  @return integer     Request ID
     */
    private function resource2id($resource)
    {
        // loop through all pending requests
        foreach ($this->pending as $request => $data)
        {
            // compare ID's
            if ($data['handle'] == $resource) return $request;
        }
        
        // not found
        return false;
    }

    /**
     *  Overridden implementation of the __doRequest call. This method filters 
     *  all calls, and adds them to the set of pending calls.
     *  @param  string      SOAP XML string to send to the server
     *  @param  string      URL to connect to
     *  @param  string      The SOAP action
     *  @param  integer     The SOAP version
     *  @param  integer     One way traffic, no result is expected
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        // find the method name
        $methodName = preg_match('/#(.*)$/', $action, $matches) ? $matches[1] : '';

        // is this an internal request and not a login request in that case we have
        // already started the connection, and only need to fetch the data
        if ($methodName != 'login' && $this->internalRequest) return curl_multi_getcontent($this->internalRequest);
    
        // create a new curl resource
        $curl = curl_init($location);

        // set all options for it
        curl_setopt_array($curl, array(
            CURLOPT_POST            =>  true,
            CURLOPT_RETURNTRANSFER  =>  true,
            CURLOPT_POSTFIELDS      =>  $request,
            CURLOPT_HEADERFUNCTION  =>  array($this, 'handleHeader'),
            CURLOPT_ENCODING        =>  '',           // Empty string sets all the supported encoding types
        ));

        // Check for a cookie
        if($this->cookie != '')
        {
            // Set the cookie
            curl_setopt($curl, CURLOPT_COOKIE, $this->cookie);
        }

        // Is this a call that should be done immediately, and not asynchronous?
        if (in_array($methodName, array('login'))) 
        {
            // the login call should not be postponed
            return curl_exec($curl);
        }
        else
        {
            // do we a resource for multiple connections?
            if (!$this->curl) $this->curl = curl_multi_init();
            
            // add the curl handle
            curl_multi_add_handle($this->curl, $curl);
            
            // store the handle in the array of pending requests
            $this->pending[$this->freeID] = array(
                'handle'    =>  $curl,
            );
            
            // return the handle
            return $this->freeID++;
        }
    }
    
    /**
     *  Method that handles the calls to the API
     *  @param  string  Name of the method
     *  @param  array   Associative array of parameters
     *  @return mixed
     */
    public function __call($methodname, $params)
    {
        // get all current pending requests
        $pending = $this->pendingRequests();
    
        try
        {
            // make the call
            return parent::__call($methodname, $params);
        }
        catch (SoapFault $e)
        {
            // do we have a new pending request
            $newPending = array_values(array_diff($this->pendingRequests(), $pending));
            if (count($newPending) < 1) return false;
            
            // we have the request ID
            $requestID = $newPending[0];
            
            // we must add the methodname and parameters to the internal data structure
            $this->pending[$requestID]['method'] = $methodname;
            $this->pending[$requestID]['params'] = $params;
            
            // return the result
            return $requestID;
        }
    }

    /**
     *  Helper function to handle the login cookie
     *  @param  bool  Force login?
     */
    protected function handleCookie($forceLogin = false)
    {
        // Try to set the cookie
        while(!$forceLogin && file_exists($this->cookieFile()) && ($fileContens = file_get_contents($this->cookieFile())) !== false)
        {
            // Get the cookie data
            $cookiedata = explode("\n", $fileContens);

            // Check for 2 elements
            if(count($cookiedata) != 2) break;

            // Check the cookie name
            if(substr($cookiedata[0], 0, 5) != 'soap_') break;

            // Set the coockie
            $this->setCookie($cookiedata[0], $cookiedata[1]);

            // And we're done
            return "Cookie set";
        }

        // try to login
        $result = $this->login(array(
            'username'  =>  $this->login,
            'password'  =>  $this->password,
            'account'   =>  $this->account,
        ));

        // Check the result
        if($result == 'DONE') return 'Cookie set';

        // return an error
        return false;
    }

    /**
     *  Helper function to set the cookie. The function is used to create a consistent interface
     *  @param  string
     *  @param  string
     */
    public function setCookie($name, $value)
    {
        $this->cookie = "$name=$value";
    }

    /**
     *  Helper function to handle the sessions cookie
     *  @param  bool  Force login?
     */
    public function handleHeader($curl, $header)
    {
        // Parse the header value
        $headers = $this->parseHeaders($header);

        // Check for the cookie index
        while(isset($headers['Set-Cookie']))
        {
            // Get the cookie
            $data = explode(' ', $headers['Set-Cookie'][0]);
            $cookie = $data[0];

            // split the cookie
            $cookieComponents = explode('=', $cookie);

            // Check the cookie name
            if(substr($cookieComponents[0], 0, 5) != 'soap_') break;

            // Set the umask
            $old = umask(0077);

            // The correct cookie was found. save it
            file_put_contents($this->cookieFile(), implode("\n", $cookieComponents));

            // Restore the umask
            umask($old);

            // Set the cookie
            $this->setCookie($cookieComponents[0], $cookieComponents[1]);

            // skip the loop
            break;
        }

        // Retrun the header lenth
        return strlen($header);
    }
}

