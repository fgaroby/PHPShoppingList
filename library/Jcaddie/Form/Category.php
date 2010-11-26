<?php
/**
 * Formulaire de création/modification d'une catégorie
 * 
 * @package jcaddie
 * @package form
 */
class Jcaddie_Form_Category extends Zend_Form
{


	/**
	 * Initialisation du formulaire (méthode obligatoire)
	 *
	 * @return Zend_Form
	 */
	public function init()
	{
		//Champ hidden "id"
		$id = new Zend_Form_Element_Hidden( 'id' );
		$id->addValidator( new Zend_Validate_Uuid() );
		$this->addElement( $id );
		
		// Champ texte "nom" (méthode simple avec setters)
		$usage = new Zend_Form_Element_Text( 'name' );
		$usage->addFilter( new Jcaddie_Filter_StripSlashes() );
		$usage->addValidator( new Zend_Validate_StringLength( 0, 75 ) );
		$usage->setLabel( "Nom :" );
		$usage->setRequired( true );
		$this->addElement( $usage );
		
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