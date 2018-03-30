<?php
final class Validation {
	
	private $fieldErrors;
	private $active;
	
	function __construct($active=true) {
		$this->active = $active;
	}
	
	public function addError($fieldName, $message) {
		if($this->active) 
			$this->fieldErrors .= "<field name=\"$fieldName\"><![CDATA[$message]]></field>";
	}
	
	public function hasErrors() {
		return !empty($this->fieldErrors);
	}
	
	public function getValidationXML() {
		return "<valid>$this->fieldErrors</valid>";
	}
	
	/**
	 * Valida um email
	 * @param String $email Email a ser validado
	 * @return boolean (true/false)
	 */
	public function validEmail($email) {
		if(!$this->active) return true;
		
		$exp = "/(.+)@(.+)\.(.+)/";
		if(strpos(trim($email), ' ') != '') return false;
		if(preg_match($exp,$email)) return true;
		else return false;
	}
	
	/**
	 * Valida um CNPJ
	 * @param String $cnpj CNPJ a ser validado
	 * @return boolean (true/false)
	 */
	public function validCNPJ($cnpj) {
		if(!$this->active) return true;
		
		$cnpj = ereg_replace("[' '-./ t]", '', $cnpj);
		
		if(strlen($cnpj) == 14) {
			$soma = 0;
		
			$soma += ($cnpj[0] * 5);
			$soma += ($cnpj[1] * 4);
			$soma += ($cnpj[2] * 3);
			$soma += ($cnpj[3] * 2);
			$soma += ($cnpj[4] * 9); 
			$soma += ($cnpj[5] * 8);
			$soma += ($cnpj[6] * 7);
			$soma += ($cnpj[7] * 6);
			$soma += ($cnpj[8] * 5);
			$soma += ($cnpj[9] * 4);
			$soma += ($cnpj[10] * 3);
			$soma += ($cnpj[11] * 2); 
			
			$d1 = $soma % 11; 
			$d1 = $d1 < 2 ? 0 : 11 - $d1; 
			
			$soma = 0;
			$soma += ($cnpj[0] * 6); 
			$soma += ($cnpj[1] * 5);
			$soma += ($cnpj[2] * 4);
			$soma += ($cnpj[3] * 3);
			$soma += ($cnpj[4] * 2);
			$soma += ($cnpj[5] * 9);
			$soma += ($cnpj[6] * 8);
			$soma += ($cnpj[7] * 7);
			$soma += ($cnpj[8] * 6);
			$soma += ($cnpj[9] * 5);
			$soma += ($cnpj[10] * 4);
			$soma += ($cnpj[11] * 3);
			$soma += ($cnpj[12] * 2); 
			
			$d2 = $soma % 11; 
			$d2 = $d2 < 2 ? 0 : 11 - $d2; 
			
			return ($cnpj[12] == $d1 && $cnpj[13] == $d2);
		}
		
		return false;
	}
	
	/**
	 * Valida um CPF
	 * @param String $valor CPF a ser validado
	 * @return boolean (true/false)
	 */
	public function validCPF($valor) {
		if(!$this->active) return true;
		
		$valor = ereg_replace("[' '-./ t]", '', $valor);
		$tamanho = strlen($valor);
		
		switch ($valor) {
			case 0: return false; break;
			case 11111111111: return false; break;
			case 22222222222: return false; break;
			case 33333333333: return false; break;
			case 44444444444: return false; break;
			case 55555555555: return false; break;
			case 66666666666: return false; break;
			case 77777777777: return false; break;
			case 88888888888: return false; break;
			case 99999999999: return false; break;
		}
		
		if ($tamanho == 11) { // CPF
			$cpf = $valor;
			$digito = array();
			
			// Pega o digito verifiacador
			$dv = substr($cpf, 9,2);
			
			for($i=0; $i<=8; $i++) $digito[$i] = substr($cpf, $i,1);
			
			// Calcula o valor do 10º digito de verificaçâo
			$pos = 10; $sum = 0;
			for($i=0; $i<=8; $i++) {
				$sum += $digito[$i] * $pos;
				$pos -= 1;
			}
			
			$digito[9] = $sum % 11;
			if($digito[9] < 2) $digito[9] = 0; 
			else $digito[9] = 11 - $digito[9];
			
			// Calcula o valor do 11º digito de verificação
			$pos = 11; $sum = 0;
			for ($i=0; $i<=9; $i++) {
				$sum += $digito[$i] * $pos;
				$pos -= 1;
			}
			
			$digito[10] = $sum % 11;
			if ($digito[10] < 2) $digito[10] = 0;
			else $digito[10] = 11 - $digito[10];
			
			// Verifica se o dv calculado é igual ao informado
			$dvc = $digito[9] * 10 + $digito[10];
			return $dvc == $dv;
		}
		return false;
	}
		
	/**
	 * Valida um Telefone
	 * @param String $tel Telefone a ser validado
	 * @return boolean (true/false)
	 */
	public function validPhone($tel) {
		if(!$this->active) return true;
		
		$exp = "/\([0-9]{2}\) ([0-9]{4}|[0-9]{5})-[0-9]{4}/";
		
		if(preg_match($exp,$tel)) return true;
		else return false;
	} 
		
	/**
	 * Valida um CEP
	 * @param String $cep CEP a ser validado
	 * @param String $mask Com/Sem máscara
	 * @return boolean (true/false)
	 */
	public function validCEP($cep,$mask='S') {
		if(!$this->active) return true;
		
		$exp = "/[0-9]{5}\-[0-9]{3}/";
		if($mask == 'S')
			if (preg_match($exp,$cep)) return true;
		else
			if (strlen($cep) == 8) return true;
		return false;
	} 
	
	/**
	 * Valida uma data
	 * @param Date $data Data a ser validada no formato DD/MM/AAAA
	 * @return boolean (true/false)
	 */
	public function validDate($data) {
		if(!$this->active) return true;
		
		$exp = "/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/";
		if (!preg_match($exp,$data)) return false;
		$data = explode('/',$data);
		return checkdate($data[1],$data[0],$data[2]);
	}
}
?>