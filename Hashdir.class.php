//
// F. Rincker
// class hashdir allows you to
// manage encrypted data as a tree
// of arrays, navigated as you would 
// an object. 
//


Class Hashdir {

    private $dir = "dirname";
  	private $parentKey="";
  	private $parentHash;
   	private $root = "/";
    private $hash = "";
    private $key = "";
    private $hashArray = [];
    private $hashkeys = [];

    public function __construct($key, $hash,$var=NULL) {

        $this->hashkeys[$hash] = $key;
        $this->hash = $hash;
        $this->key = $key;
      	if(isset($var)) $this->root = $var;

        $hashpath = $this->dir . "/" . $hash;

        if (!file_exists($hashpath)) {
          
            $this->store(array(),$this->key,$this->hash);
          
            $this->hashkeys = load($this->key,$this->hash);
        
        } else {
            
            $this->hashArray = $this->load($key, $hash);
        }

    }
  
      public function ls() {
      
      	echo "<br>hashdir :" . $this->root;
      
      	foreach($this->hashArray as $key=>$value) {
          
          $out = $value;
          
          if(is_array($value)) {
            $out = "array " . count($value);
          	if(isset($value['key'])) $out = "hashdir " . $value['hash']; 
          } 

          echo "<br>" . $key . " " . $out; 
          
        }

    }
  
  	public function link($var,$cryptdir) {
     
    	  $key = $cryptdir->key;	
    	  $hash = $cryptdir->hash;	
        $hashArray = $cryptdir->hashArray;
        $this->setSub($var,$hashArray, $key,$hash=NULL); 

    }  

    public function mkdir($var, $value, $key = NULL,$hash = NULL) {
      
      	// add an encrypted subarray
      
      	if(!is_array($value)) return false;
      	if(isset($this->hashArray[$var]['key'])) return false;

        $newhash = $this->mkhash();
        $newkey = $this->mkkey();
        $this->store($value, $newkey, $newhash);
        $this->hashArray[$var] = array('key' => $newkey, 'hash' => $newhash);
        $this->store($this->hashArray,$this->key,$this->hash);
      
    }
  
  	public function cd($var=NULL) {
      
        if($var==NULL) return cd_up();
      
      	if($this->parentKey !="") return false;
      	if(!isset($this->hashArray[$var])) return false;
      
      	$this->root = $var;
      	$this->parentHash = $this->hash;
        $this->parentKey = $this->key;
      	$this->hash =  $this->hashArray[$var]['hash'];
      	$this->key =  $this->hashArray[$var]['key'];
      
      	$this->hashArray =  $this->load($key,$hash);
      
      	return $var;
      
    } 
  
  	public function cd_up() {
      
      	if($this->parentKey =="") return false;
      
      	$this->hash = $this->parentHash;
      	$this->parentHash = "";
        $this->key = $this->parentKey;
      	$this->parentKey = "";
      
      	$this->hashArray=  $this->load($this->key,$this->hash);
      
    }  

    public function mkhash() {
        return md5(rand(0, 10000));
    }

    public function hash($string) {
        return md5($string);
    }

    public function mkkey() {
        return substr(md5(rand(0, 10000)),0,10);
    }

    public function __call($method, $arguments) {

        if (!function_exists($method)) {

            $this->addVar($method);
        } else {

            call_user_func($method);
        }
    }

    public function __get($var) {

        if (isset($this->hashArray[$var])) {
          
            if (isset($this->hashArray[$var]['key'])) {
				
                $key = $this->hashArray[$var]['key'];
                $hash = $this->hashArray[$var]['hash'];
              
              	$this->getremote($hash); 

                $temp = new Hashdir($key, $hash,$var);

                return $temp;
              
            } else {

                return $this->hashArray[$var];
            }
          
        } else {

            return false;
        }
    }
  
    public function __set($var,$value) {

	    if(isset($this->hashArray[$var]['key'])) return false;
    
      $this->hashArray[$var] = $value;
      $this->store($this->hashArray, $this->key, $this->hash);
  	}
  
    public function ll() {

        echo "<br>Hash ";
        echo $this->hash;
        echo "<br>Key ";
        echo $this->key;
        echo "<br>hashArray <br>";
        var_dump($this->hashArray);
        echo "<br>";
    }

    private function store($data_array, $akey, $hash) {
      
      	if($akey == "" && $hash == "") return;

        $data_file = $this->dir . '/' . $hash;

        $data_json = json_encode($data_array);

        if (file_exists($data_file)) {

            unlink($data_file);
        }

        $iv = substr(md5('iv' . $akey, true), 0, 8);

        $key = substr(md5('pass1' . $akey, true) .
                md5('pass2' . $key, true), 0, 24);

        $opts = array('iv' => $iv, 'key' => $key);
      
        $fp = fopen($data_file, 'wb');

        stream_filter_append($fp, 'mcrypt.tripledes', STREAM_FILTER_WRITE, $opts);

        fwrite($fp, $data_json);

        fclose($fp);

        return $data_array;
    }

    public function load($akey, $hash) {
        // loads data from file hash and decrypts it with key akey

        $data_file = $this->dir . '/' . $hash;

        $iv = substr(md5('iv' . $akey, true), 0, 8);

        $key = substr(md5('pass1' . $akey, true) .
                md5('pass2' . $key, true), 0, 24);

        $opts = array('iv' => $iv, 'key' => $key);

        $fp = fopen($data_file, 'rb');

        stream_filter_append($fp, 'mdecrypt.tripledes', STREAM_FILTER_READ, $opts);

        $decrypted = rtrim(stream_get_contents($fp));

        fclose($fp); 
      
        $data_array = json_decode($decrypted, true);

        return $data_array;
    }

}
