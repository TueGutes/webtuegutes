package tests;

import java.security.SecureRandom;
import java.util.concurrent.TimeUnit;
import java.math.BigInteger;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebDriverException;
import org.openqa.selenium.WebElement;

import starter.GUITest;


public class RegisterAndLoginTest extends GUITest{
	

	public boolean doTest(WebDriver wd) throws WebDriverException{
		String reg_url = "http://localhost/projects/tuegutes/git/registration.php";
		String login_url = "http://localhost/projects/tuegutes/git/login.php";
		
		//zufaellige Nutzerdaten erzeugen
		String uName = getRandomString();
		String uPwd = getRandomString();
		String uEmail = getRandomString();
		
		//registration.php aufrufen
		wd.get(reg_url);
		
		WebElement elem;
		
		
		//Registrationsformular fuellen
		elem = wd.findElement(By.xpath("/html/body/div[2]/center/form/center/table/tbody/tr[1]/td[2]/input"));
		elem.sendKeys(uName);
		
		elem = wd.findElement(By.xpath("/html/body/div[2]/center/form/center/table/tbody/tr[2]/td[2]/input"));
		elem.sendKeys(uPwd);
		
		elem = wd.findElement(By.xpath("/html/body/div[2]/center/form/center/table/tbody/tr[3]/td[2]/input"));
		elem.sendKeys(uPwd);
		
		elem = wd.findElement(By.xpath("/html/body/div[2]/center/form/center/table/tbody/tr[4]/td[2]/input"));
		elem.sendKeys(uEmail);
		
		//Konto erstellen anklicken
		elem = wd.findElement(By.xpath("/html/body/div[2]/center/form/center/input"));
		elem.click();
		
		//Zur Startseite zurueckkehren
		elem = wd.findElement(By.xpath("/html/body/div[2]/center/p/a"));
		elem.click();
		
		//login aufrufen
		
		wd.get(login_url);
		
		
		
		//ausloggen
		elem = wd.findElement(By.xpath("/html/body/a"));
		elem.click();
		
		
		//login Formular ausfuellen
		wd.get(login_url);
		
		elem = wd.findElement(By.xpath("/html/body/form/table/tbody/tr[1]/td[2]/input"));
		elem.sendKeys(uName);
		elem = wd.findElement(By.xpath("/html/body/form/table/tbody/tr[2]/td[2]/input"));
		elem.sendKeys(uPwd);
		elem = wd.findElement(By.xpath("/html/body/form/table/tbody/tr[3]/td[2]/input"));
		elem.click();
		
		
		//Es wird ein Hinweis angezeigt die Formulardaten neu zu senden (eine Fehlfunktion?)
		this.waitForAlert(wd);
		wd.switchTo().alert().accept();
 	    wd.switchTo().defaultContent();
 	    
		String bodyText = wd.findElement(By.tagName("body")).getText();

		

		//ueberpruefen ob man eingeloggt wurde
		return bodyText.contains("Sie sind eingeloggt als");
	}
	

	
	


}
