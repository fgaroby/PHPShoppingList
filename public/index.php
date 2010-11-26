<?php
echo session_save_path();


/**
 * Bootstrap de l'application (contrôleur frontal)
 * 
 * @author Guillaume Ponçon
 * @author Julien Pauli
 * @author Francescu Garoby
 * @package application
 */

/**
 * fonction de renvoi vers une page d'erreur
 * en cas d'erreur bloquante
 */
//set_exception_handler( 'bootstrapError' );



function bootstrapError()
{
	exit( "Une erreur grave est survenue" );
}

// définition des chemins utiles
$rootPath = dirname( dirname( __FILE__ ) );
$appPath = $rootPath . DIRECTORY_SEPARATOR . 'application';
$confPath = $appPath . DIRECTORY_SEPARATOR . 'configs';


// ******************** CONSTANTES *******************************



define( 'APP_MODE', 'dev' );
define( 'CACHE_LIFETIME', 3600 );

// configuration de l'include_path PHP pour l'autoload
set_include_path( 
get_include_path() . PATH_SEPARATOR . $appPath . PATH_SEPARATOR . $rootPath . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR . $appPath . DIRECTORY_SEPARATOR .
 'models' );

/** Zend_Loader_Autoloader */
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace( 'Jcaddie_' );

$loader = new Zend_Loader_Autoloader_Resource( array( 'basePath' => $appPath, 'namespace' => 'Application' ) );
$loader->addResourceType( 'model', 'models', 'Model' );

// ******************** CACHE *******************************



// création du cache pour les composants ZF l'acceptant
Jcaddie_Cache::setup( CACHE_LIFETIME );
// cache automatique des fichiers de configuration
$cacheInstance = Jcaddie_Cache::getCacheInstance();
Jcaddie_Config::setBackendCache( $cacheInstance->getBackend() );


// ********************* CONFIG *****************************



// Récupération des objets de configuration
$configMain = new Zend_Config_Ini( $confPath . DIRECTORY_SEPARATOR . 'config.ini', APP_MODE );
$configRoutes = new Jcaddie_Config_Ini( $confPath . DIRECTORY_SEPARATOR . 'routes.ini' );
$configSession = new Jcaddie_Config_Ini( $confPath . DIRECTORY_SEPARATOR . 'session.ini', APP_MODE );


// ************************** LOG ******************************



$log = new Zend_Log( $writer = new Zend_Log_Writer_Stream( $appPath . DIRECTORY_SEPARATOR . $configMain->logdir . DIRECTORY_SEPARATOR . $configMain->logfile ) );

// Ajout de paramètres à enregistrer, adresse ip et navigateur
$log->setEventItem( 'user_agent', $_SERVER['HTTP_USER_AGENT'] );
$log->setEventItem( 'client_ip', $_SERVER['REMOTE_ADDR'] );

$log->addPriority( 'USER', 8 );
// Ajout des paramètres enregistrés dans le format du journal à écrire
$format = '%client_ip% %user_agent%' . Zend_Log_Formatter_Simple::DEFAULT_FORMAT;

// Ajout du format du journal au log
$writer->setFormatter( new Zend_Log_Formatter_Simple( $format ) );

Zend_Registry::set( 'log', $log );


// ************************* SESSION ***********************************



// Configuration de la session (impérativement avant son démarrage)
Zend_Session::setOptions( $configSession->toArray() );
Zend_Session::setOptions( array( 'save_path' => $appPath . $configSession->save_path ) );

// Partage (et création ou restauration) de l'objet de session dans le registre
// Ce premier appel à new Zend_Session_Namespace démarre la session PHP
Zend_Registry::set( 'session', $session = new Zend_Session_Namespace( $configSession->name ) );


// ************************** LOCALE ********************************



// locale pour la gestion de la langue



//Zend_Locale::$compatibilityMode = false;
$locale = new Zend_Locale( 'fr_FR' ); // locale par défaut : navigateur utilisé, sinon machine hôte
Zend_Registry::set( 'Zend_Locale', $locale );
// on attache le composant cache à Zend_Locale
Zend_Locale::setCache( $cacheInstance );

// *************************** DATES *********************************
date_default_timezone_set( 'Europe/Paris' );
Zend_Date::setOptions( array( 'cache' => $cacheInstance ) );


// ************************** DATABASE *******************************



try
{
	$db = Zend_Db::factory( $configMain->database );
	$db->query( "SET NAMES 'UTF8'" );
	$db->setFetchMode( Zend_Db::FETCH_OBJ );
	
	// Ajout du profiler
	$profiler = new Zend_Db_Profiler_Firebug( 'Requêtes' );
	$profiler->setEnabled( true );
	$db->setProfiler( $profiler );
	
	// Passage de la connexion à toutes les classes passerelles
	Zend_Db_Table_Abstract::setDefaultAdapter( $db );
}
catch( Zend_Db_Exception $e )
{
	// on passe l'exception sous silence, elle sera gérée dans le système MVC plus tard 
}

// activation du cache des méta données des passerelles
Zend_Db_Table_Abstract::setDefaultMetadataCache( $cacheInstance );


//************************** ACL *************************************



// les ACLs n'existent pas en session, créons les
/*if( ! isset( $session->acl ) )
{
	$acl = new Zend_Acl();
	$acl->addRole( new Zend_Acl_Role( 'user' ) );
	$acl->addResource( new Zend_Acl_Resource( 'products' ) );
	$session->acl = $acl;
}*/


// ************************ PAGINATEUR *********************************



Zend_View_Helper_PaginationControl::setDefaultViewPartial( 'common/pagination_control.phtml' );


// ************************ MVC ****************************************



// Configuration du contrôleur frontal
$frontController = Zend_Controller_Front::getInstance();
$frontController->setControllerDirectory( $appPath . '/controllers' )/*
				->setBaseUrl( '/~windu/JCaddie-Web' )*/;

$frontController->throwExceptions( false ); // par défaut



// propagation de paramètres dans le système MVC
$frontController->setParam( 'debug', $configMain->debug );
$frontController->setParam( 'locale', $locale );
$frontController->setParam( 'config', $configMain );

// enregistrement du plugin de sauvegarde de la page précédente
$frontController->registerPlugin( new Jcaddie_Controller_Plugins_Session() );

// Ajout du chemin des aides d'action dans le gestionnaire d'aides MVC
Zend_Controller_Action_HelperBroker::addPrefix( 'Jcaddie_Controller_ActionHelpers' );

// Configuration d'un en-tête de réponse HTTP global
$response = new Zend_Controller_Response_Http();
$response->setRawHeader( 'Content-type: text/html; charset=utf-8' );

// passage de la réponse configurée au système MVC
$frontController->setResponse( $response );

// récupération du routeur
$router = $frontController->getRouter();

// définition et ajout de routes contact
$router->addConfig( $configRoutes->getConfigObject(), 'routes' );


// **************************** LAYOUTS ***********************************



Zend_Layout::startMvc( array( 'layoutPath' => $appPath . '/views/layouts' ) );


// ************************* TRANSLATE ************************************
/*
Zend_Translate::setCache( $cacheInstance );

//@todo Guillaume éditer les PO
$translate = new Zend_Translate( 'gettext', $appPath . '/languages', null, array( 'scan' => Zend_Translate::LOCALE_FILENAME ) );

$french = array( 'Value is empty, but a non-empty value is required' => 'Valeur vide', 'Tokens do not match' => 'Session expirée', 
"'%value%' is greater than %max% characters long" => "'%value%' dépasse 25 caractères", 
"'%value%' does not fit given date format" => "'%value%' n'a pas le format d'une date valide" );

$english = array( "Salle :" => "Room :", "Utilisation :" => "Usage :", "Du :" => "From :", "Au :" => "To :" );

$translateForm = new Zend_Translate( 'array', $french, 'fr' );
$translateForm->getAdapter()->addTranslation( $english, 'en' );

$langLocale = isset( $session->lang ) ? $session->lang : $locale;

// Passage de la locale en cours à Zend_Translate
$translate->setLocale( $langLocale );
$translateForm->setLocale( $langLocale );
Zend_Registry::set( 'Zend_Translate', $translate );
*/

// ************************* FORM ******************************************



//Zend_Form::setDefaultTranslator( $translateForm );



// ************************* VIEW ******************************************



$view = new Zend_View();
$view->setEncoding( 'UTF-8' );
$view->strictVars( ( bool ) $configMain->debug );

// Récupération de l'aide de rendu automatique de vues : viewRenderer
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper( 'ViewRenderer' );

// Passage de notre vue à ViewRenderer
$viewRenderer->setView( $view );


// ******************************** DISPATCH ******************************



try
{
	$frontController->dispatch();
}
catch( Zend_Exception $e )
{
	$log->crit( $e );
}