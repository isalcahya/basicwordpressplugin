<?php

 namespace App\lib;

 use App\Plugin;
 use Symfony\Component\Finder;
 use Symfony\Component\Finder\Exception as FinderExc;
 use hanneskod\classtools;
 /**
  *
  */
final class wcic_load_class
 {

 	private $plugin;

 	private $features;

 	private $class = [];

 	private static $on_init;

 	public $version = '2.4.2';

	public $capability = 'manage_options'; // default GIS capabilities

	public $plugin_domain = 'GIS';

	public $name = 'gis_donorejo';

 	public function __construct()

 	{
 		# code...
 		// parent::__construct();
 		$this->features = [];

 		$this->include();

 		$this->init();
 	}

 	private function include(){

 		try {
 			$finder = new Finder\Finder();

 			$path = $finder->in( PLUGIN_PATH.'inc/classes/' );

			$iter = new classtools\Iterator\ClassIterator( $path );

			$iter->enableAutoloading();

			foreach ($iter as $class) {

			    $this->collectFitur($class->getName());

			}

			// already register all fitur
			$this->plugin = new Plugin();
			$this->on_instance_classes();
 		} catch (FinderExc\DirectoryNotFoundException $e) {
 			echo '<pre>';
 			print_r( $e->getMessage() );
 			echo '</pre>';
 			exit();
 		}
 	}

 	private function init(){

 		add_action( 'plugins_loaded', array( $this , 'on_init' ), 10, 1 );

 	}

 	public function on_init(){

 		if ( self::$on_init === true ) {
 			return 'error confused';
 		}

 		$this->wcic_inits_class();

 		do_action( $this->name . '_loadeds' );
 	}

 	private function wcic_inits_class(){
 		if( count($this->features) && true !== self::$on_init ){
 			self::$on_init = true;
 			foreach ( $this->features as $fitur ) {
	 			# code...
	 			// $this->class[$fitur] = self::instance($fitur);
 				$className = $this->get_class_name($fitur);
	 			if( method_exists($this->class[$className], 'register') ){
	 				// feature has been added into plugin
	 				$this->plugin->add($this->class[$className]);
	 				// feature already registered
	 				$this->class[$className]->register();
	 			}

 			}

 		}
		// initialise the plugin
		$this->plugin->inits();
 	}

 	public function __call($class, $arguments){
 	 	if( count($this->features) ){
 			foreach ( $this->features as $fitur ) {
 				$className = $this->get_class_name($fitur);
 				if ( strtolower($className) === strtolower($class) ){
		            return new $fitur;
		        }
 			}
 		}
    }

 	public function on_instance_classes(){
 		if( count($this->features) ){

 			foreach ( $this->features as $fitur ) {
 				$className = $this->get_class_name($fitur);
 				$this->class[$className] = self::instance($fitur);
 			}

 		}

 	}

 	public static function instance($class){

 		if ( !class_exists( $class ) ) {
 			return false;
 		}

 		$classes = new $class;

 		return $classes;
 	}

 	private function get_class_name($fitur){
 		return (new \ReflectionClass($fitur))->getShortName();
 	}

 	public function collectFitur($obj){

 		array_push( $this->features, $obj );

 	}

 	Public function define($name='',$value){

		if ( !defined($name) ) {
			 define($name, $value);
		}

	}

 }