<?php
/**
**  @file Html.class.php
**  @author immeëmosol (programmer dot willfris at nl) 
**  @date 2010-10-26
**  Created: Tue 2010-10-26, 11:08.28 CEST
**  Last modified: mer 2011-06-22, 21:19.32 CEST
**/

namespace output
{
	class Html
	{
		private static $container_elements  =  array(
				'ul' => array( 'li' ) ,
				'ol' => array( 'li' ) ,
				'li' => array( 'a' ) ,

				'a' => array( '' ) ,
				);
		private          function __construct ()
		{
		}
		public static function a ( $reference , $text )
		{
			//@TODO[~immeëmosol, mer 2010-11-03, 20:57.04 CET]
			//  implement someting that will add a class current if the uri/reference is part of the current request
			//  if so it gets class active
			//  if it equals the whole request it gets class current
			$args    =  array();
			$args[]  =  array( 'href' => $reference );
			$args[]  =  $text;
			return self::__callStatic( 'a' , $args );
		}
		public static function link ( $uri )
		{
			$args  =  func_get_args();
			$uri  =  $args[ 0 ];
			$attributes  =  array();
			$attributes[ 'href' ]  =  $uri;
			$attributes[ 'rel' ]  =  'stylesheet';
			$attributes[ 'type' ]  =  'text/css';
			$contents  =  array();
			$contents[]  =  $attributes;
			$link  =  new HtmlElement( 'link' , $contents );
			return $link;
		}
		public static function __callStatic ( $function_name , $args )
		{
			$element_name   =  $function_name;
			$object  =  new HtmlElement( $element_name , $args );
			return $object;
		}
		//method to group html-elements which are just siblings,
		//without a common parent in their current context, the one in which they are created
		public static function box ()
		{
			$args    =  func_get_args();
			return self::__callStatic( '' , $args );
		}
		//@note[~immeëmosol, sab 2010-12-25, 17:43.20 CET]
		//  "alias" for box
		public static function block ()
		{
			$args    =  func_get_args();
			return self::box( '' , $args );
		}

		public static function form ( $data )
		{
			$fieldsets  =  array();
			if ( isset( $data[ 'fieldsets' ] ) )
			{
				$fieldsets  =  $data[ 'fieldsets' ];
				unset( $data[ 'fieldsets' ] );
			}

			$formulier  =  self::__callStatic( 'form' , array( $data ) );

			//@todo[~immeëmosol, mer 2010-11-03, 11:18.35 CET]
			//  check if $fieldset_name has valid characters for the name-attribute
			foreach ( $fieldsets as $fieldset_name => $fields )
			{
				$fieldset   =  Html::fieldset();
				$legend     =  Html::legend( $fields[ 'label' ] );
				$dl         =  Html::dl();

				foreach ( $fields[ 'eisen' ] as $naam => $eigenschappen )
				{
					$form_element  =  self::newFormElement( $naam , $eigenschappen , $fieldset_name );
					$dl[]=Html::dt( $form_element[ 'label' ] );
					if ( is_array( $form_element[ 'element' ] ) )
					{
						$dd=Html::dd( array_shift( $form_element[ 'element' ] ) );
						$dd[]=Html::span( array_shift( $form_element[ 'element' ] ) );
						$dl[]=$dd;
					}
					else
					{
						$dl[]=Html::dd( $form_element[ 'element' ] );
					}
				}

				$fieldset[]   =  $legend;
				$fieldset[]   =  $dl;
				$formulier[]  =  $fieldset;
			}

			//@TODO[~immeëmosol, mer 2010-11-03, 05:35.17 CET]
			//  de submit-knop wat mooier afhandelen, eventueel met mogelijkheden in $eigenschappen mooier
			$input_attributes             =  array();
			$input_attributes[ 'type' ]   =  'submit';
			$input_attributes[ 'value' ]  =  'Verzend';
			$submit       =  Html::input( $input_attributes );

			$formulier[]  =  $submit;
			return $formulier;
		}
		private static function newFormElement( $naam , $eigenschappen , $fieldset_name = NULL )
		{
			static $input_types  =  array( 'text' , 'password' );
			static $types  =  array(
					'text' => 'input' ,
					'password' => 'input' ,
					'select' => 'dropdown' ,
					);
			switch ( $types[ $eigenschappen[ 'type' ] ] )
			{
				case 'input' :
					return self::newInputFormElement( $naam , $eigenschappen , $fieldset_name );
					break;
				case 'dropdown' :
					return self::newSelectFormElement( $naam , $eigenschappen , $fieldset_name );
					break;
			}
		}
		private static function newSelectFormElement ( $naam , $eigenschappen , $fieldset_name = NULL )
		{
			$naam  =  $fieldset_name . '_' . $naam;
			$select_attributes            =  array();
			$select_attributes[ 'type' ]  =  $eigenschappen[ 'type' ];
			$select_attributes[ 'name' ]  =  $naam;
			$mooie_naam  =  isset( $eigenschappen[ 'beschrijving' ] ) ? $eigenschappen[ 'beschrijving' ] : $naam;
			if ( isset( $eigenschappen[ 'auto' ] ) && TRUE === $eigenschappen[ 'auto' ] )
			{
				$select_attributes[ 'disabled' ]  =  'disabled';
			}

			$select  =  Html::select( $select_attributes );
			if ( isset( $_POST[ $naam ] ) )
			{
				$selected_option  =  1 * $_POST[ $naam ];
			}
			elseif ( isset( $_GET[ $naam ] ) )
			{
				$selected_option  =  1 * $_GET[ $naam ];
			}
			$options  =  $eigenschappen[ 'options' ];
			$option_attributes  =  array(
					'value' => '' ,
					'disabled' => 'disabled' ,
					);
			if ( !isset( $selected_option ) )
			{
				$option_attributes[ 'selected' ]  =  'selected';
			}
			$option  =  Html::option( $option_attributes , $eigenschappen[ 'hint' ] );
			$select[]  =  $option;
			foreach ( $options as $option_id => $option_naam )
			{
				$option_attributes  =  array();
				if ( isset( $selected_option ) && $selected_option === $option_id )
				{
					$option_attributes[ 'selected' ]  =  'selected';
				}
				$option_attributes[ 'value' ]  =  $option_id;
				$option  =  Html::option( $option_attributes , $option_naam );
				$select[]  =  $option;
			}

			return array( 'label' => $mooie_naam , 'element' => $select );
		}
		private static function newInputFormElement ( $naam , $eigenschappen , $fieldset_name = NULL )
		{
			$naam  =  $fieldset_name . '_' . $naam;
			$input_attributes            =  array();
			$input_attributes[ 'type' ]  =  $eigenschappen[ 'type' ];
			$input_attributes[ 'name' ]  =  $naam;
			if (
					'password' !== $input_attributes[ 'type' ]
					|| (
						// only give back type="password"-fields in a https-connection
						isset( $_SERVER[ 'HTTPS' ] )
						&& !empty( $_SERVER[ 'HTTPS' ] )
						&& 'off' !== $_SERVER[ 'HTTPS' ]
						)
				)
			{
				if ( isset( $_POST[ $naam ] ) )
				{
					$input_attributes[ 'value' ]  =  $_POST[ $naam ];
				}
				elseif ( isset( $_GET[ $naam ] ) )
				{
					$input_attributes[ 'value' ]  =  $_GET[ $naam ];
				}
			}
			$mooie_naam  =  isset( $eigenschappen[ 'beschrijving' ] ) ? $eigenschappen[ 'beschrijving' ] : $naam;
			if ( isset( $eigenschappen[ 'auto' ] ) && TRUE === $eigenschappen[ 'auto' ] )
			{
				$input_attributes[ 'disabled' ]  =  'disabled';
			}

			$input  =  Html::input( $input_attributes );
			if ( isset( $eigenschappen[ 'hint' ] ) )
			{
				$hint  =  $eigenschappen[ 'hint' ];
				return array( 'label' => $mooie_naam , 'element' => array( $input , $hint ) );
			}

			return array( 'label' => $mooie_naam , 'element' => $input );
		}
	}
}


