		$bootstrap=$this->jquery->bootstrap();
		$header=$bootstrap->htmlHeader("header",1);
		$header->asTitle("Welcome to Ubiquity","Version ".\Ubiquity\core\Framework::version);
		$bt=$bootstrap->htmlButton("btTest","Twitter Bootstrap Button");
		$bt->onClick("$('#test').html('It works with Twitter Bootstrap too !');");
		\Ubiquity\core\postinstall\Display::bootstrapMenu("menu",$bootstrap);
		$this->jquery->compile($this->view);
		$this->loadView("@framework/index/bootstrap.html");
