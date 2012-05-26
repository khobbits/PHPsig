<?php

define( 'malXML_KEEPTIME', 30 ); // how long to keep downloaded data before grabbing a new copy.
define( 'malXML_USERAGENT', 'PHP (Mozilla/5.0 capable)' ); // the 'browser' we are downloading from

class mal_data {

	var $username; // stores the currently used nickname
	var $type; // holds what type of feed is being parsed
	var $read_xml; // xml parser object
	var $xml_storage = array(); // stored data
	var $socket;

	/**
	 * Sets the username to use for downloading feeds
	 *
	 * @param string $username
	 */
	function SetUsername( $username )
	{
		$this->username = $username;
	}

	/**
	 * Gets the user's basic profile information
	 *e
	 * @return array Profile / false if failed
	 */	
	function GetProfile( $username ) // profile
	{
		if( $username )
		{
			$this->username = $username;
		}
		
		return $this->ParseFeed( 'profile' );
	}

	
	// the functions below should be left alone, unless you know exactly what you're doing
	// these are used to download data and parse it into a usable format
	function XML_StartTag( $parser, $tag, $params )
	{	
		if( $tag == 'mal' ) { return; }
		
		$this->xml_storage[ $this->username ][ $this->type ][ 'current_tag' ] = strtolower( $tag );

	}
	
	function XML_EndTag( $parser, $tag )
	{
        $this->xml_storage[ $this->username ][ $this->type ][ 'current_tag' ] = '';
	}
	
	function XML_CharData( $parser, $data )
	{
		$data = str_replace( array( '\t', '\n' ), '', $data );
		if( ( empty( $data ) || $data == '' || strlen( $data ) < 1 ) && !is_numeric( $data ) ) { return; }

		$this->xml_storage[ $this->username ][ $this->type ][ $this->xml_storage[ $this->username ][ $this->type ][ 'current_tag' ] ] .= $data;

	}
	
	/**
	 * Close the curl socket to the mal server
	 *
	 * @param none
	 */
	function CloseSocket( )
	{
		if( $this->socket )
		{
			return curl_close( $this->socket );
		}
		
		return false;
	}
	
	/**
	 * Downloads a fresh copy of an XML feed from the mal servers
	 *
	 * @param string $type
	 */
	function DownloadFeed( $type )
	{
		if( !$this->socket )
		{
			$this->socket = curl_init(); // we use curl for this
		}
		$download_url = 'http://myanimelist.net/malappinfo.php?u=' . $this->username; // construct the download url

		curl_setopt( $this->socket, CURLOPT_URL, $download_url ); // set the url
		curl_setopt( $this->socket, CURLOPT_HEADER, 0 ); // do not include the header in the downloaded content
		curl_setopt( $this->socket, CURLOPT_RETURNTRANSFER, true ); // yes....
		curl_setopt( $this->socket, CURLOPT_USERAGENT, malXML_USERAGENT ); // we need this so we don't get firewalled

		$xml = curl_exec( $this->socket ); // get what we need

		if ((stripos($xml,'<user_name>'.$this->username.'</user_name>') === false) ||
		 (stripos($xml,'</myanimelist>') === false)) {
			return ' (Invalid XML)';
		}
	
		if( $h = fopen( 'mal_cache/' . $this->username . '_' . $type . '.xml' , 'w' ) ) // can we write?
		{
			fwrite( $h, $xml ); // yes we can
			fclose( $h );
		}
		else
		{
			return ' (Unable to write to file)'; // no we can't
		}	
		return true;
	}
	
	/**
	 * Parses a specific feed, you can call this directly, or use some of the 'helper' functions above. Feed types: [profile, live]
	 *
	 * @param string $type
	 * @return array page / false if error
	 */
	function ParseFeed( $type )
	{
		if( empty( $this->xml_storage[ $this->username ][ $type ] ) )
		{
			
			// we're using XML Caching
			$timenow = date( 'U' ); // time now
			//if (( file_exists( 'mal_cache/' . $this->username . '_' . $type . '.xml' ) == true ) && 
			if ( $timenow < ( @filemtime( 'mal_cache/' . $this->username . '_' . $type . '.xml' ) + ( malXML_KEEPTIME * 60 ) ) )
			{
				$this->xml_storage[ $this->username ][ $type ][ 'socketed' ] = 'Used XML cache';
			}
			else
			{
				if (($msg = $this->DownloadFeed( $type )) === true) {
					$this->xml_storage[ $this->username ][ $type ][ 'socketed' ] = 'Socketed MAL';
				} else {
					$this->xml_storage[ $this->username ][ $type ][ 'error' ] = $msg;
					$this->xml_storage[ $this->username ][ $type ][ 'socketed' ] = 'Used XML cache';
				}
			}
			
			if ( file_exists( 'mal_cache/' . $this->username . '_' . $type . '.xml' ) == true ) {
				$this->type = $type;
				
				$this->xml_storage[ $this->username ][ $type ][ 'no' ] = 0;
				$this->read_xml = xml_parser_create();
				xml_set_object( $this->read_xml, $this );
				xml_set_element_handler( $this->read_xml, 'XML_StartTag', 'XML_EndTag' );
				xml_set_character_data_handler( $this->read_xml, 'XML_CharData' ); 
				xml_parse( $this->read_xml, @file_get_contents( 'mal_cache/' . $this->username . '_' . $type . '.xml' ) );
				xml_parser_free( $this->read_xml ); 
			} else {
				$this->xml_storage[ $this->username ][ $type ][ 'user_name' ] = 'Not Found';
				$this->xml_storage[ $this->username ][ $type ][ 'user_watching' ] = '404';
				$this->xml_storage[ $this->username ][ $type ][ 'user_completed' ] = 'Failure';
			}
		}
		
		unset( $this->xml_storage[ $this->username ][ $type ][ 'no' ] );
		unset( $this->xml_storage[ $this->username ][ $type ][ 'current_tag' ] );
		unset( $this->xml_storage[ $this->username ][ $type ][ 'current_gid' ] );
		return $this->xml_storage[ $this->username ][ $type ];
	}
	
	
}

?>