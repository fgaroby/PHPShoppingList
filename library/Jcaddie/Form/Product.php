<?php
/**
 * Formulaire de création/modification d'un produit
 * 
 * @package jcaddie
 * @package form
 */
class Jcaddie_Form_Product extends Zend_Form
{


	/**
	 * Initialisation du formulaire (méthode obligatoire)
	 *
	 * @return Zend_Form
	 */
	public function init()
	{
		// Champ hidden "id" (contenant l'identifiant du produit, dans le cas d'une modification)
		$id = new Zend_Form_Element_Hidden( 'id' );
		$id->addValidator( new Zend_Validate_Uuid() );
		$this->addElement( $id );
		
		// Champ texte "name"
		$usage = new Zend_Form_Element_Text( 'name' );
		$usage->addFilter( new Jcaddie_Filter_StripSlashes() );
		$usage->addValidator( new Zend_Validate_StringLength( 0, 75 ) );
		$usage->setLabel( "Nom :" );
		$usage->setRequired( true );
		$this->addElement( $usage );
		
		// Champ texte "poids" (méthode simple avec setters)
		$usage = new Zend_Form_Element_Text( 'poids' );
		$usage->addFilter( new Jcaddie_Filter_StripSlashes() );
		$usage->addValidator( new Zend_Validate_Float() );
		$a = new Zend_Validate_Float();
		$usage->setLabel( "Poids :" );
		$usage->setRequired( true );
		$this->addElement( $usage );
		
		$unitModel = new Application_Model_DbTable_Unit();
		$units = $unitModel->fetchAll( null, "name ASC" );
		$unitsTab = array();
		foreach ($units as $unit )
			$unitsTab[$unit->id] = $unit->name;
			
		// Liste déroulante des unités
		$unitSelect = new Zend_Form_Element_Select( 'xunit' );
		$unitSelect->setMultiOptions( $unitsTab );
		$unitSelect->setLabel( "Unité :" );
		$unitSelect->setRequired( true );
		$unitSelect->addValidator(new Zend_Validate_Uuid() );
		$this->addElement( $unitSelect );
		
		$categoryModel = new Application_Model_DbTable_Category();
		$categories = $categoryModel->fetchAll( null, "name ASC" );
		$categoriesTab = array();
		foreach( $categories as $cat )
			$categoriesTab[$cat->id] = $cat->name;
		
		// Liste déroulante des catégories (méthode avec setters)
		// déclaration, options, validateurs et filtres
		$catSelect = new Zend_Form_Element_Select( 'xcategory' );
		$catSelect->setMultiOptions( $categoriesTab );
		$catSelect->setLabel( "Catégories :" );
		$catSelect->setRequired( true );
		$catSelect->addValidator( new Zend_Validate_Uuid() );
		$this->addElement( $catSelect );
		
		// Bouton de validation
		$submitButton = new Zend_Form_Element_Submit( 'submit_product' );
		$submitButton->setLabel( "Valider" );
		$submitButton->setValue( "Valider" );
		$submitButton->style = 'margin-left: 80px';
		$this->addElement( $submitButton );
		
		// jeton
		$token = new Zend_Form_Element_Hash( 'token', array( 'salt' => 'unique' ) );
		$this->addElement( $token );
	}
}