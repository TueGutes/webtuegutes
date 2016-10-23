package starter;

import java.math.BigInteger;
import java.security.SecureRandom;

import org.openqa.selenium.NoAlertPresentException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebDriverException;

//abstracte Klasse von der alle tests abgeleitet werden. Bietet ausserdem Hilfsfunktionen an
public abstract class GUITest {
	private SecureRandom random = new SecureRandom();
	
	
	public abstract boolean doTest(WebDriver wd) throws WebDriverException;
	
	//einen Zufallsstring generieren
	protected String getRandomString(){
		return new BigInteger(130, random).toString(32);
	}
	
	//wartet auf einen Alert und wechselt zu ihm wenn er erscheint
	protected void waitForAlert(WebDriver driver){  
		int i=0;
		while(i++<5){
			try{
	            driver.switchTo().alert();
	            break;
	        } catch(NoAlertPresentException e){
	          try {
				Thread.sleep(1000);
	          } catch (InterruptedException e1) {
				e1.printStackTrace();
	          }
	        }
		}
	}
}