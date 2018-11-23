<?php

/**
 * Page Class
 */
class Page
{
	public $title;
	public $keywords;
	public $description;
	public $TJavascript;
	public $Tcss;
 
  /**
   * Constructeur
   *
   * @param void
   * @return void
   * @access private
   */
  public function __construct()
  {
    $this->title = 'Titre par défaut';
    $this->keywords = 'keyword,par,defaut';
    $this->description = 'Titre par défaut';
    $this->TJavascript = array();
    $this->Tcss = array();
  }
 
   /**
* Crée et retourne l'objet SPDO
*
* @access public
* @static
* @param void
* @return SPDO $instance
*/
  public static function getInstance()
  {  
    if(is_null(self::$instance))
    {
      self::$instance = new SPDO();
    }
    return self::$instance;
  }
 
  /**
   * Exécute une requête SQL avec PDO
   *
   * @param string $query La requête SQL
   * @return PDOStatement Retourne l'objet PDOStatement
   */
  public function query($query)
  {
    return $this->PDOInstance->query($query);
  }
  
}