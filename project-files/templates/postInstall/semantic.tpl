		$semantic=$this->jquery->semantic();
		$header=$semantic->htmlHeader("header",1);
		$header->asTitle("Welcome to Ubiquity","Version ".\Ubiquity\core\Framework::version);
		$bt=$semantic->htmlButton("btTest","Semantic-UI Button");
		$bt->onClick("$('#test').html('It works with Semantic-UI too !');");
		\Ubiquity\core\postinstall\Display::semanticMenu("menu",$semantic);
		$this->jquery->compile($this->view);
		$this->loadView("index.html");