
/**
 * @file sem.js
 * @author giorno
 * @package N7
 * @subpackage SEM
 * @license Apache License, Version 2.0, see LICENSE file
 *
 * Objects and routines handling client side events of Settings Editor Model
 * (SEM).
 */

/**
 * Client side representation of SEM model.
 */
function sem ( )
{
	/**
	 * Array of collections registered in SEM.
	 */
	this.colls = new Array( );

	/**
	 * Registers new collection.
	 * 
	 * @param coll collection, instance of sem_collection
	 */
	this.add = function ( coll )
	{
		this.colls[this.colls.length] = coll;
	};
	
	/**
	 * Disables or enables collection HTML elements.
	 * 
	 * @param disabled controls rendering of disabling
	 */
	this.set_disabled = function ( disabled )
	{
		for ( i = 0; i < this.colls.length; ++i )
			this.colls[i].set_disabled( disabled );
	};

	/**
	 * Encodes structures into XML.
	 */
	this.encode = function ( )
	{
		writer = new XMLWriter( 'UTF-8', '1.0' );

		writer.writeStartDocument( false );

			writer.writeStartElement( 'sem' );
				
				for ( var i = 0; i < this.colls.length; ++i )
					this.colls[i].encode( writer );

			writer.writeEndElement( );

		writer.writeEndDocument( );


		var data = waPlusSignWaEncode( writer.flush() );

		writer.close( );
		
		return data;
	};
}

/**
 * Client side representation of SEM collection instance.
 * 
 * @param id collection identifier
 */
function sem_collection ( id )
{
	/**
	 * Identifier of this collection.
	 */
	this.id = id;

	/**
	 * Array of atoms registered in this collection.
	 */
	this.atoms = new Array( );

	/**
	 * Registers new atom into collection.
	 * 
	 * @param atom instance of sem_atom
	 */
	this.add = function ( atom )
	{
		this.atoms[this.atoms.length] = atom;
	};
	
	/**
	 * Disables or enables collection HTML elements.
	 * 
	 * @param disabled controls rendering of disabling
	 */
	this.set_disabled = function ( disabled )
	{
		for ( i = 0; i < this.atoms.length; ++i )
			this.atoms[i].set_disabled( disabled );
	};

	/**
	 * Encodes collection using parent writer.
	 */
	this.encode = function ( writer )
	{
		writer.writeStartElement( 'c' );
		
		writer.writeAttributeString( 'id', Base64.encode( this.id ) );

			for ( var i = 0; i < this.atoms.length; ++i )
				this.atoms[i].encode( writer );

		writer.writeEndElement( );
	};
}

/**
 * Client side representation of SEM atom.
 * 
 * @param type type of atom, describes UI element
 * @param id identifier of atom
 * @param html_id identifier of HTML element providing UI
 */
function sem_atom ( type, id, html_id )
{
	/**
	 * Type of atom.
	 */
	this.type		= type;
	
	/**
	 * Identifier of atom in collection.
	 */
	this.id			= id;
	
	/**
	 * Identifier of UI element.
	 */
	this.html_id	= html_id;
	
	
	/**
	 * Disables or enables collection HTML elements.
	 * 
	 * @param disabled controls rendering of disabling
	 */
	this.set_disabled = function ( disabled )
	{
		var el = document.getElementById( this.html_id );
			if ( el )
				el.disabled = disabled;
	};

	/**
	 * Subatom structure (particles) is not used as server side SemAcceptor
	 * interface implementations do not need such a structure. Atom is well
	 * enought identified but its key/ID, byt which it is accessed there.
	 */
	this.encode = function ( writer )
	{
		var val = '';
		var el = document.getElementById( this.html_id );
		if ( el )
			switch ( this.type )
			{
				/**
				 * Select box.
				 */
				case 1: val = el.options[el.selectedIndex].value; break;

				/**
				 * Text field.
				 */
				case 2: val = el.value; break;
			}
		writer.writeStartElement( 'a' );

		writer.writeAttributeString( 'id', Base64.encode( this.id ) );
		writer.writeAttributeString( 'val', Base64.encode( val ) );

		writer.writeEndElement( );
	};
}
