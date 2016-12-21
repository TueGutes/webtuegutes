package tests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import starter.GUITest;

// Einfacher Beispieltest der prüfen soll ob das Login funktioniert
public class LoginTest extends GUITest{

	@Override
	public boolean doTest(WebDriver wd, String rootUrl) {
			
		//Webseite Aufrufen
		wd.get(rootUrl + "/login");
		
		WebElement elem;
		
		elem = wd.findElement(By.cssSelector("input[name='username']"));
		elem.sendKeys("testuser");
		elem = wd.findElement(By.cssSelector("input[name='password']"));
		elem.sendKeys("testpasswort");
		elem = wd.findElement(By.cssSelector("input[value='Login']"));
		elem.click();
		
		elem = wd.findElement(By.id("profileheader"));
		if(elem.getText().equals("Dein Profil")){
			return true;
		} else {
			return false;
		}
		
		
		
	}

}
