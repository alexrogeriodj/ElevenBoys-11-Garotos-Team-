<?php
require('core/Loader.php');
$loader = new Loader('Page');

$page = new Page($loader, 'admin', 'login');

if(empty($link)) $link = $page->urlSite . '/index';
if($page->isLogged(false)) header("Location:$link");

$page->header('Login', array('form.css', 'login.css'), 'login.js');
?>
<div class="row h-100 text-center">
	<div class="col-lg-6 col-md-4 hidden-sm-down bg">
	</div>
	<div class="col-lg-6 col-md-8 col-sm-12 login" >
		<div class="container">
				<?php 
				$page->formIni('login');
				
				?>
				<img alt="" src="../theme/admin/img/logo.png" class="logo">
				<div class="field" id="in-user">
				<input type="text" name="user" id="fd-user" value="" size="40" maxlength="40">
				</div>
				<div class="field" id="in-user">
				<input type="password" name="pass" id="fd-pass" value="" size="40">
				</div>
				<input type="hidden" name="link" id="fd-link" value="<?php echo $link?>">
				<div class="button field">
					<input type="button" value="Login" title="Login" onclick="fnSubmit('form-login','','0',true);">
				</div>
				<?php 
				
				$page->formEnd();
				
				?>
			</div>
		</div>
	</div>
</div>

<?php 

$page->footer();
?>