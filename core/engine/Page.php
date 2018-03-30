<?php
ob_start();
final class Page {
	
	private $nl    = "\n";
	private $type  = 'DFT';
	private $theme = 'default';
	
	private $loader;
	private $version, $formId, $formAjax, $formFilter, $pager, $javaScript;
	public $dirTheme, $rootPath, $themePath, $urlSite, $urlTheme;
	
	function __construct($loader, $theme=null, $type=null) {
		
		$this->loader = $loader;
		
		if($type) $this->type = $type;
		if($theme) $this->theme = $theme;
		
		$this->loader->requireObject('Session', 'library');
		$this->loader->requireObject('DataBase', 'database');
		$this->loader->requireObject('Functions', 'library');
		$this->loader->requireObject('Image', 'library');
		
		$this->version   = $this->loader->get('config')->get('VERSION');
		$this->dirTheme  = $this->loader->get('config')->get('DIR_THEME');
		$this->rootPath  = $this->loader->getRootPath();
		$this->themePath = "$this->rootPath/$this->dirTheme/$this->theme";
		$this->urlSite   = $this->loader->getUrlSite();
		$this->urlTheme  = "$this->urlSite/$this->dirTheme/$this->theme";
		
		$file = "$this->themePath/functions.inc";
		if(file_exists($file)) include_once($file);

	}
	
	function __get($key) {
		return $this->get($key);
	}
	
	function __set($key, $value) {
		$this->set($key, $value);
	}

	function __call($name, $param) {
		if(function_exists($name)) call_user_func_array($name, $param);
	}
	
	function get($key) {
		return $this->loader->get($key);
	}
	
	function set($key, $value) {
		$this->loader->set($key, $value);
	}
	
	public function isLogged($redir=true) {
		return $this->session->isLogged($redir);
	}
	
	public function isIndex() {
		return stripos($_SERVER['SCRIPT_NAME'], 'index.php') !== false;
	}
	
	public function setVersion($version) {
		$this->version = $version;
	}
	
	public function getDynamicFormCallback($match) {
		$NAME=$PARAM=null;
		$content = '';
	
		$prms = explode(' ', $match[1]);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		$fileName = $this->loader->getRootPath() . '/cms/form/' . $NAME . '.php';
		if(file_exists($fileName)) {
			ob_start();
	
			$page = $this;
			require($fileName);
		
			$content = ob_get_contents();
			ob_end_clean();
		}
		
		return $content;
	}
	
	public function getDynamicForm($content) {
		return preg_replace_callback('|\[FORM (.*?)\]|', array(&$this, 'getDynamicFormCallback'), $content);
	}
	
	public function widgetIni($title, $desc, $prm='') {
		$prms = explode('|', $prm);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		$class = isset($CLASS) ? " $CLASS" : '';
		$html = '';
		$html .= "<div class=\"$p[0]\">";
		$html .= "<div class=\"x_panel \">";
		$html .= "<div class=\"x_title\">";
		$html .= "<h2>$title<small>$desc</small></h2>";
		$html .= "<div class=\"clearfix\"></div>";
		$html .= "</div>";
		$html .= "<div class=\"x_content\">";
		echo $html;
	}
	
	public function widgetEnd(){
		$html .= "</div>";
		$html .= "</div>";
		echo $html;
	}
	
	public function getPageData($id) {
		$database = $this->loader->get('database');
		$image    = $this->loader->get('image');
		
		$data = null;
	
		$sql = "select " .
				"	type as type, " .
				"	title as title, " .
				"	content as content " .
				"from " .
				"	page " .
				"where " .
				"	status = 1 " .
				"	and page_id = " . (int) $id;

		$dbq = $database->query($sql);
		
		if($database->getNumRows($dbq) > 0) {
			$data = $database->getArray($dbq);
			$data['content'] = $image->getDynamicImages($data['content']);
			$data['content'] = $this->getDynamicForm($data['content']);
		}
		
		return $data;
	}
	
	public function pager($nrl, $scr, $prms='') {
		$nl = $this->nl;
		
		$prms = explode('|', $prms);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		// Formato do html
		$format = isset($FORMAT) ? $FORMAT : $this->loader->get('config')->get('PAGER_FORMAT');
		$nrp    = isset($NRP) ? $NRP : 13; 
		$maxct  = (isset($MAXCT) ? $MAXCT : 11) - 1;
		$hash   = isset($HASH) ? "#$HASH" : '';
		$novars = isset($NOVARS) ? true : false;
		
		$pager = null; 
		if($nrl > $nrp) {
			
			$p = isset($_GET['p']) ? $_GET['p'] : 0;
			
			$pager = array(!empty($p) ? ($p - 1) * $nrp : 0, $nrp);
			
			$qry = '';
			if(!$novars) {
				$qry .= '?';
				foreach($_GET as $k => $v) {
					$qry .= ($k != 'p') ? $k . '=' . urlencode($v) . '&amp;' : '';
				}
			}
			else {
				$qry .= strpos($scr, '?') ? '&' : '?'; 
			}
			
			// Primeira página
			$href = $scr . $qry . "p=1" . $hash;
			$format = str_replace('{first}', $href, $format);
			
			// Página anterior
			$href = $scr . $qry . "p=" . (($p == 1 or $p == 0) ? 1 : ($p - 1)) . $hash;
			$format = str_replace('{prev}', $href, $format);
			
			// Número da página
			$pagini = 0;
			$qtdpag = ceil($nrl / $nrp);
			if($p > 0 and $qtdpag > $maxct) {
				$pagini = $p - 1;
				
				if($pagini <= floor($maxct / 2)) $pagini = 0;
				else { 
					$pagini -= floor($maxct / 2);
					$pagdif = $qtdpag - $pagini;
					if($pagdif <= $maxct) $pagini -= ($maxct - $pagdif + 1);
				}
				
				$pagini *= $nrp;
			}
			
			// Numeração das pages
			$ct = 0;
			$numbers = '';
			for($i = $pagini; $i < $nrl and $ct <= $maxct; $i+=$nrp) {
				$pagn = ($i/$nrp) + 1;
				
				$href = $scr . $qry . "p=$pagn" . $hash;
				$act = ($p == $pagn or ($i == 0 and $p == 0)) ? " active" : '';
				$numbers .= "<a href=\"$href\" class=\"number$act\"$act>$pagn</a>";
				
				$ct++;
			}
			
			$format = str_replace('{numbers}', $numbers, $format);
			
			// Próxima página
			if($p == 0) $p = 1;
			$href = $scr . $qry . "p=" . (($p == $pagn) ? $pagn : ($p + 1)) . $hash;
			$format = str_replace('{next}', $href, $format);
			
			// Última página
			$href = $scr . $qry . "p=" . $qtdpag . $hash;
			$format = str_replace('{last}', $href, $format);
			
			$html = "<div class=\"pager\">{$nl}{$format}</div>";;	
		}
		
		$this->pager = $html;
		
		return $pager;
	}
	
	public function getPager() {
		return $this->pager;
	}

	/**
	 * @param String $val
	 * @param String $prm [A=align|W=width]
	 * @return void
	 */
	public function cell($val, $prm='') {
		$prms = explode('|', $prm);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		$align = (isset($A)) ? " align=\"$A\"" : '';
		$width = (isset($W)) ? " width=\"$W\"" : '';
		$colsp = (isset($COLSPAN)) ? " colspan=\"$COLSPAN\"" : '';
		
		echo "<td$align$width$colsp>$val</td>$this->nl";
	}
	
	public function lineIni($prm='') {
		$prms = explode('|', $prm);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		$class = (isset($HEAD)) ? " class=\"headings\"" : '';
		$link = (isset($LINK)) ? " onclick=\"fnLink('$LINK')\"" : '';
		echo "<thead>";
		echo "<tr$class$link>$this->nl";
	}
	
	public function lineEnd() {
		echo "</tr>$this->nl";
	}
	
	public function tableIni($prm='') {
		$prms = explode('|', $prm);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		$class = isset($CLASS) ? " $CLASS" : '';
		$html = '';
		
		$html .= "<table class=\"table table-bordered\">$this->nl";
		
		echo $html;
	}
	
	public function tableEnd() {
		$html = '';
		
		$html .= "</table>$this->nl";
		echo $html;
	}
	
	public function formIni($id, $prm='') {
		
		$action = '#'; 
		$method = 'post';
		$filter = false;
		$ajax   = true;
		
		$prms = explode('|', $prm);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		if(isset($METHOD)) $method = $METHOD;
		if(isset($ACTION)) $action = $ACTION;
		if(isset($FILTER)) $filter = $FILTER;
		if(isset($NOAJAX)) $ajax = false;
		
		$this->formId     = 'form-' . ($filter ? 'filter-' : '') . $id;
		$this->formFilter = $filter;
		$this->formAjax   = $ajax;
		
		$class = 'form' . ($filter ? ' formFilter' : '');
		
		echo "<form action=\"$action\" id=\"$this->formId\" method=\"$method\" class=\" field $class\">$this->nl";
	}
	
	public function formEnd() {
		$this->addJavaScriptBody('FORM', 'form.js');
		$this->formFilter = false;
		$this->formAjax = true;
		echo "</form>$this->nl";	
	}
	
	public function button($btns, $prm='') {
		$nl = $this->nl;
		$html = '';
		
		$prms = explode('|', $prm);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		$class = '';
		if($this->formFilter) $class .= ' buttonFilter';
		if(!empty($CLASS)) $class .= " $CLASS";
		
		
		
		foreach($btns as $btn) {
			$SBM = $DEL = false;
			$VAL = $RTN = $LINK = $JS = $TITLE = $ID = '';
			$SCROLL = 0;
			
			$prms = explode('|', $btn);
			foreach($prms as $p) {
				$p = explode('=', $p, 2);
				if(count($p) > 1) ${$p[0]} = $p[1];
				else ${$p[0]} = true;
			}
			
			$op = isset($_GET['op']) ? $_GET['op'] : '';
			
			if(!$DEL or ($DEL and $op == 'A')) {
				
				$js = '';
				$class = "btn-default";
				
				if($SBM) {	
					$class = "btn-primary";
					$ajax = $this->formAjax ? 'true' : 'false';
					$js = "fnSubmit('$this->formId','$op','$SCROLL',$ajax)";
				}
				else if($DEL) {
					$class = "btn-danger";
					$js = "fnDelete('$this->formId','$RTN')";
				}
				else if(!empty($LINK)) {
					$class = "btn-info";
					$js = "fnLink('$LINK')";
				}
					 
				else if(!empty($JS)) 
					$js = $JS;
					
				$title = empty($TITLE) ? $VAL : $TITLE;
				$id = empty($ID) ? '' : " id=\"button-$ID\"";
				
				$html .= "<input type=\"button\" value=\"$VAL\" title=\"$title\" class=\"btn $class\" onclick=\"$js;\"$id />$nl";
				
			}

		}
		
		if(!isset($NOECHO)) echo $html;
		return $html;
	}
	
	public function field($type, $label, $name, $val='', $prm='', $sql='') {
		$nl         = $this->nl;
		$html       = '';
		$javascript = '';
		$field      = '';
		
		$prms = explode('|', $prm);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		$fieldId = 'in-' . str_replace('[]', '', $name);
		$inputId = 'fd-' . str_replace('[]', '', $name);
		
		if(!isset($ACCEPT_HTML)) $val = htmlspecialchars($val);
		
		if($type != 'H') {
			$css = '';
			$cls = '';
			if(isset($LEFT)) $css .= "float:left;";
			if(isset($FW)) $css .= "width:$FW;";
			if(isset($CLEAR)) $css .= "clear:both;";
			if(isset($CSS)) $css .= "$CSS";
			if(isset($RO)) $cls .= ' field-ro';
			if(isset($INLINE)) $cls .= ' field-inline';
			if(isset($INVERSE)) $cls .= ' field-inverse';
			if(isset($CLASS)) $cls .= " $CLASS";
			if(isset($FOCUS)) $javascript .= "$('#$inputId" . ($type == 'R' ? '-1' : '') . "').focus();";
			
			if($css != '') $css = " style=\"$css\"";
			
			$html .= "<div class=\" $cls\" id=\"$fieldId\"$css>$nl";
			if(!empty($label)) $label = "<label class=\"control-label\" for=\"$inputId\">$label</label>$nl";
		}
		else $label = '';
		
		if($type == 'A') { // Textarea
			
			$cols = isset($COLS) ? $COLS : '50';
			$rows = isset($ROWS) ? $ROWS : '5'; 
			
			$attr = '';
			if(isset($PLACEHOLDER)) $attr .= " placeholder=\"$PLACEHOLDER\"";
			
			$field = "<textarea class=\"form-control\" name=\"$name\" id=\"$inputId\" cols=\"$cols\" rows=\"$rows\"$attr>$val</textarea>$nl";
			
		}
		else if($type == 'S') { // Select
			
			$field = "<select name=\"$name\" class=\"form-control\" id=\"$inputId\">$nl";
			if(isset($VAL)) {
				
				$v = explode(':', $VAL);
				$t = explode(':', isset($TXT) ? $TXT : $VAL);
				
				for($i = 0; $i < count($v); $i++) {
					$sel = ($val == $v[$i]) ? ' selected="selected"' : '';
					$field .= "<option value=\"$v[$i]\"$sel>$t[$i]</option>$nl";
				}
				
			}
			
			if(!empty($sql)) {
				
				$db = $this->database;
				$dbq = $db->query($sql);
				
				while($row = $db->getRow($dbq)) {
					$sel = ($val == $row[0]) ? ' selected="selected"' : '';
					$field .= "<option value=\"$row[0]\"$sel>$row[1]</option>$nl";
				}
				
			}
			$field .= "</select>$nl";
			
		}
		else if($type == 'FI') { // File - Image
			
			$w = isset($W) ? $W : '100%';
			$h = isset($H) ? $H : '80';
			
			$prm = isset($MAX) ? "&max=$MAX" : '';
			$prm .= isset($RESIZE) ? "&rsz=$RESIZE" : '';
			
			$src = "$this->urlSite/upload/image.php?ver=$this->version&src=$name&sid=$val$prm&theme=$this->theme";
			$field = "<iframe src=\"$src\" frameborder=\"0\" width=\"$w\" height=\"$h\" id=\"$inputId\" " .
					"style=\"overflow: hidden;\" scrolling=\"no\"></iframe>";
			
		}
		else if($type == 'T' or $type == 'P' or $type == 'C' or $type == '@' or $type == 'URL') {
			
			$attr = '';
			if(isset($W)) $attr .= " size=\"$W\"";
			
			if($type == 'T' or $type == '@' or $type == 'URL') {
				
				if($type == 'T') $t = 'text';
				else if($type == '@') $t = 'email';
				else if($type == 'URL') $t = 'url';
				
				if(isset($MASK)) {
					$javascript .= "$('#$fieldId input').setMask('$MASK');";
					$this->addJavaScriptBody('MASK', 'jquery.mask.js');
				}
				
				if(isset($ML)) $attr .= " maxlength=\"$ML\"";
				else if(isset($W)) $attr .= " maxlength=\"$W\"";
				
			}
			else if($type == 'P') $t = 'password';
			else if($type == 'C') { 
				$t = 'checkbox';
				if($val == 'S') $attr .= ' checked="checked"';
				$val = "S";
			}
			
			if(isset($PLACEHOLDER)) $attr .= " placeholder=\"$PLACEHOLDER\"";
			if(isset($RO)) $attr .= ' readonly="readonly"';
			
			$field = "<input type=\"$t\" name=\"$name\" id=\"$inputId\" class=\"form-control\" value=\"$val\"$attr/>$nl";
			
		}
		else if($type == 'H') { // Hidden
		
			$field = "<input type=\"hidden\" name=\"$name\" id=\"$inputId\" value=\"$val\"/>$nl";
		
		}
		else if($type == 'R') { // Radio
			
			$index = 0;
			
			if(isset($VAL)) {
				
				$v = explode(':', $VAL);
				$t = explode(':', isset($TXT) ? $TXT : $VAL);
				
				for($i = 0; $i < count($v); $i++) {
					$index++;
					$sel = ($val == $v[$i]) ? ' checked="checked"' : '';
					$field .= "<input type=\"radio\" name=\"$name\" id=\"$inputId-$index\" value=\"$v[$i]\"$sel />" .
							"<label for=\"$inputId-$index\" class=\"radio\">$t[$i]</label>$nl";
				}
				
			}
			
			if(!empty($sql)) {
				
				$db = $this->database;
				$dbq = $db->query($sql);
				
				while($row = $db->getRow($dbq)) {
					$index++;
					$sel = ($val == $row[0]) ? ' checked="checked"' : '';
					$field .= "<input type=\"radio\" name=\"$name\" id=\"$inputId-$index\" value=\"$row[0]\"$sel />" .
							"<label for=\"$inputId-$index\" class=\"radio\">$row[1]</label>$nl";
				}
				
			}
			
		}		
		else if($type == 'I') { // informação
			
			$field = "<strong id=\"$inputId\">$val</strong>$nl";
						
		}
		
		if(isset($WRAP)) $field = "<div class=\"element\">$field</div>";
		
		$html .= isset($INVERSE) ? $field . $label : $label . $field;
		
		if($type != 'H') $html .= (isset($HTML) ? $HTML.$nl : '') . "</div>$nl";
		
		$this->addJavaScriptBody(false, $javascript);
		
		echo $html;	
	}
	
	public function addJavaScriptBody($key, $value) {
		if(!$this->javaScript) $this->javaScript = array();
		
		if($key) $this->javaScript['SRC'][$key] = $value;
		else {
			if(!isset($this->javaScript['COD'])) $this->javaScript['COD'] = '';
			$this->javaScript['COD'] .= $value;
		}
	}
	
	public function getCssHtml($css, $media=null, $urlBase=null) {
		if(is_null($css)) return '';
		
		if(is_null($media)) $media='all';
		if(is_null($urlBase)) $urlBase = "$this->urlTheme/css/";
		
		$script = '';
		$format = "<link rel=\"stylesheet\" type=\"text/css\" href=\"$urlBase%s?$this->version\" media=\"$media\" />$this->nl";
		
		if(is_array($css)) 
			foreach($css as $s) $script .= sprintf($format, $s);
		else 
			$script .= sprintf($format, $css);
		
		return $script; 
	}
	
	public function getJavaScriptHtml($js, $urlBase=null) {
		if(is_null($js)) return '';
		
		if(is_null($urlBase)) $urlBase = "$this->urlTheme/js/";
		
		$script = '';
		$format = "<script type=\"text/javascript\" src=\"$urlBase%s?$this->version\"></script>$this->nl";
		
		if(is_array($js)) 
			foreach($js as $s) $script .= sprintf($format, $s);
		else 
			$script .= sprintf($format, $js);
		
		return $script;
	}
	
	public function header($title='', $css=null, $js=null, $prm=null) {
		$nl = $this->nl;
		$ver = $this->version;
		
		if(!is_null($prm)) {
		
			if(is_array($prm)) 
			
				foreach($prm as $k => $v) ${$k} = $v;
				
			else {
			
				$prms = explode('|', $prm);
				foreach($prms as $p) {
					$p = explode('=', $p, 2);
					if(count($p) > 1) ${$p[0]} = $p[1];
					else ${$p[0]} = true;
				}
			
			}
		
		}
		
		$dir = "$this->rootPath/$this->dirTheme";
		if(!file_exists("$dir/$this->theme/header.inc")) $this->theme = 'default';
		require("$dir/$this->theme/header.inc");
	}
	
	public function footer() {
		$nl = $this->nl;
		$ver = $this->version;
		
		if(is_array($this->javaScript)) {
		
			if(isset($this->javaScript['SRC'])) 
				echo $this->getJavaScriptHtml($this->javaScript['SRC'], "$this->urlSite/js/");
			
			if(isset($this->javaScript['COD'])) 
				echo '<script type="text/javascript">' . $this->javaScript['COD'] . '</script>';
			
		}
		
		$dir = "$this->rootPath/$this->dirTheme";
		if(!file_exists("$dir/$this->theme/footer.inc")) $this->theme = 'default';
		require("$dir/$this->theme/footer.inc");

		$this->render();
	}
	
	public function render() {
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
	}
	
}
?>