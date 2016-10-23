package tests;

import org.openqa.selenium.WebDriver;

import starter.GUITest;


public class ExampleTitleTest extends GUITest{

	public boolean doTest(WebDriver wd) {
		String url = "http://localhost/projects/tuegutes/git/registration.php";
		String expectedTitle = "Registration";
			
		//Webseite Aufrufen
		wd.get(url);
		
		//tatsächlichen Titel abfragen
		String actualTitle = wd.getTitle();
		
		
		//den tatsächlichen mit dem erwarteten Title vergleichen
		if(expectedTitle.equals(actualTitle)){
			return true;
		} else {
			return false;
		}
	}

}
