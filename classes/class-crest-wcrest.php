<?php

class Crest_WCREST {
	
	private $token = false;  
	
	public function get_people_ids_by_office( $office_id ){
		
		if ( $this->authenticate() ){
			
			$people_ids = $this->request_people_ids_by_office( $office_id );
			
			return array(
				'status' 	=> true,
				'msg' 		=> 'People Found',
				'response' => $people_ids,
			);
			
		} else {
			
			return array(
				'status' 	=> false,
				'msg' 		=> 'Could not authenticate',
				'response' => false,
			);
			
		} // end if
		
	} // end $office_id
	
	
	private function authenticate(){
		
		if ( isset( $_SESSION['crest_token'] ) ) {
			
			$this->token = $_SESSION['crest_token'];
			
			return true;
			
		} else {
			
			$user = get_option('_wcrest_user', false );
			
			$pwd = get_option('_wcrest_pwd', false );
			
			if ( ! $user || ! $pwd ) {
				
				return false;
				
			} // end if
			
			$xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Header/><soapenv:Body/></soapenv:Envelope>';

			$headers = array(
				'Content-Type: text/xml',
				'Accept-Encoding: gzip,deflate',
				'SOAPAction: "http://rfg.realogy.com/Btt/AuthenticationManagement/Services/2009/05/AuthenticationManagementServiceContract/Authenticate"',
				'Host: auth.ws.realogyfg.com',
				'Connection: Keep-Alive',
				'Cookie: OBBasicAuth=fromDialog; ObSSOCookie=loggedoutcontinue',
				'Authorization: Basic ' . base64_encode( $user . ':' . $pwd ),
			);
			
			$process = curl_init( 'https://auth.ws.realogyfg.com/AuthenticationService/AuthenticationMgmt.svc' );
			
			curl_setopt($process, CURLOPT_HTTPHEADER, $headers );
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_POST, 1);
			curl_setopt($process, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($process, CURLOPT_ENCODING, '');
			
			$response = curl_exec($process);
			
			preg_match( '/<a:Token>(.*)<\/a:Token>/', $response, $matches, PREG_OFFSET_CAPTURE );
			
			preg_match( '/<a:Expiration>(.*)<\/a:Expiration>/', $response, $expires, PREG_OFFSET_CAPTURE );
			
			if ( ! empty( $matches[1][0] ) && ! empty( $expires[1][0] )){
				
				$this->token = $matches[1][0];
				
				return true;
				
			} else {
				
				return false;
				
			}// end if
			
		} // end if
		
		return false;
		
	} // end authenticate
	
	
	private function request_people_ids_by_office( $office_id ){
		
		$xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://rfg.realogy.com/btt/brokerage/services/2013/01" xmlns:ns1="http://rfg.realogy.com/btt/common/types/2013/01" xmlns:arr="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:ns2="http://rfg.realogy.com/btt/brokerage/types/2013/01">';
   		$xml .= '<soapenv:Header/><soapenv:Body><ns:OfficeStaffSearchRequest><ns:SearchCriteriaPerson><ns2:BrandCode>SIR</ns2:BrandCode><ns2:Status>Active</ns2:Status><ns2:OfficeIdentifiers><ns1:OfficeIdentifier>';
        $xml .= '<ns1:OfficeId>' . $office_id . '</ns1:OfficeId>';
        $xml .= '</ns1:OfficeIdentifier></ns2:OfficeIdentifiers></ns:SearchCriteriaPerson></ns:OfficeStaffSearchRequest></soapenv:Body></soapenv:Envelope>';

		$headers = array(
			'Content-Type: text/xml;charset=UTF-8',
			'Accept-Encoding: gzip,deflate',
			'SOAPAction: "http://rfg.realogy.com/btt/brokerage/services/2013/01/OfficeStaffServiceContract/OfficeStaffSearch"',
			'Host: solows.realogyfg.com',
			'Connection: Keep-Alive',
			'Cookie: ' . $this->token,
		);
		
		$process = curl_init( 'http://solows.realogyfg.com/V1.3/BrokerageRW/OfficeStaffService.svc' );
		
		curl_setopt($process, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($process, CURLOPT_TIMEOUT, 300);
		curl_setopt($process, CURLOPT_POST, 1);
		curl_setopt($process, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($process, CURLOPT_ENCODING, '');
		
		$response = curl_exec($process);
		
		preg_match_all( '/<a:PersonId>(.*?)<\/a:PersonId>/', $response, $matches, PREG_OFFSET_CAPTURE );
		
		$people_ids = array();
		
		if ( is_array( $matches ) && ! empty( $matches[1] ) ){
			
			foreach( $matches[1] as $person ){
				
				$people_ids[] = $person[0];
				
			} // end foreach
			
		} // end if
		
		//var_dump( $matches[1] );
		
		return $people_ids;
		
	} // end request_people_ids_by_office
	
	
	public function get_person_by_id( $person_id ){
		
		if ( ! $this->authenticate() ){
			
			return array(
				'status' 	=> false,
				'msg' 		=> 'Could not authenticate',
				'response' => false,
			);
			
		} // end if
		
		$xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://rfg.realogy.com/btt/brokerage/services/2013/01" xmlns:ns1="http://rfg.realogy.com/btt/common/types/2013/01" xmlns:arr="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:urn="urn:rfg.brokerage.info">';
   		$xml .= '<soapenv:Header/><soapenv:Body><ns:PersonDetailGetRequest><ns:PersonIds><urn:guid>' . $person_id . '</urn:guid></ns:PersonIds></ns:PersonDetailGetRequest></soapenv:Body></soapenv:Envelope>';

		$headers = array(
			'Content-Type: text/xml;charset=UTF-8',
			'Accept-Encoding: gzip,deflate',
			'SOAPAction: "http://rfg.realogy.com/btt/brokerage/services/2013/01/OfficeStaffServiceContract/PersonDetailGet"',
			'Host: solows.realogyfg.com',
			'Connection: Keep-Alive',
			'Cookie: ' . $this->token,
		);
		
		$process = curl_init( 'http://solows.realogyfg.com/V1.3/BrokerageRW/OfficeStaffService.Svc' );
		
		curl_setopt($process, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($process, CURLOPT_TIMEOUT, 300);
		curl_setopt($process, CURLOPT_POST, 1);
		curl_setopt($process, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($process, CURLOPT_ENCODING, '');
		
		$response = curl_exec($process);
		
		 return array(
				'status' 	=> true,
				'msg' 		=> 'Query Successful',
				'response' => $response,
			);
		
	} // end get_person_by_id
	
	
	public function get_office_id_from_email( $email ) {
		
		if ( ! $this->authenticate() ){
			
			return array(
				'status' 	=> false,
				'msg' 		=> 'Could not authenticate',
				'response' => false,
			);
			
		} // end if
		
		$xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://rfg.realogy.com/btt/brokerage/services/2013/01" xmlns:ns1="http://rfg.realogy.com/btt/common/types/2013/01" xmlns:arr="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:ns2="http://rfg.realogy.com/btt/brokerage/types/2013/01">';
   		$xml .= '<soapenv:Header/><soapenv:Body><ns:OfficeStaffSearchRequest><ns:SearchCriteriaPerson><ns2:VanityEmail>' . $email . '</ns2:VanityEmail></ns:SearchCriteriaPerson></ns:OfficeStaffSearchRequest></soapenv:Body></soapenv:Envelope>';

		$headers = array(
			'Content-Type: text/xml;charset=UTF-8',
			'Accept-Encoding: gzip,deflate',
			'SOAPAction: "http://rfg.realogy.com/btt/brokerage/services/2013/01/OfficeStaffServiceContract/OfficeStaffSearch"',
			'Host: solows.realogyfg.com',
			'Connection: Keep-Alive',
			'Cookie: ' . $this->token,
		);
		
		$process = curl_init( 'http://solows.realogyfg.com/V1.3/BrokerageRW/OfficeStaffService.svc' );
		
		curl_setopt($process, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($process, CURLOPT_TIMEOUT, 300);
		curl_setopt($process, CURLOPT_POST, 1);
		curl_setopt($process, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($process, CURLOPT_ENCODING, '');
		
		$response = curl_exec($process);
		
		$office_id = $this->get_value_from_xml_unique( 'OfficeId', $response );
		
		if ( $office_id ){
		
			return array(
				'status' 	=> true,
				'msg' 		=> 'Office ID found',
				'response' => $office_id,
			);
			
		} else {
			
			return array(
				'status' 	=> false,
				'msg' 		=> 'Office ID not found',
				'response' => false,
			);
			
		} // end if
		
	} // end get_office_id_from_email
	
	
	private function get_value_from_xml_unique( $key, $xml ){ //'OfficeStaffId'
		
		$regex = '/<.*?:' . $key . '>(.*?)<\/.*?:' . $key . '>/';
		
		preg_match( $regex, $xml, $matches, PREG_OFFSET_CAPTURE);
		
		if ( is_array( $matches ) && ! empty( $matches[1][0] ) ){
			
			return $matches[1][0];
			
		} else {
			
			return '';
			
		}
		
	} // end get_value_from_xml_unique
	
	
	private function get_value_from_xml_nested( $parent_key, $key, $xml ){ //'OfficeStaffId'
		
		$regex = '/<.*?' . $parent_key . '.*?:' . $key . '>(.*?)<.*?:' . $key . '>/s';
		
		preg_match( $regex, $xml, $matches, PREG_OFFSET_CAPTURE);
		
		if ( is_array( $matches ) && ! empty( $matches[1][0] ) ){
			
			return $matches[1][0];
			
		} else {
			
			return '';
			
		}
		
	} // end get_value_from_xml_unique
	
	
} // end Crest_WCREST