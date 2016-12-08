package starter;
import java.io.File;
import java.util.ArrayList;
import java.util.concurrent.TimeUnit;

import org.apache.commons.lang3.SystemUtils;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;

import tests.*;


public class TestStarter {
	static WebDriver firefoxDriver;
	static WebDriver chromeDriver;
	
	static ArrayList<GUITest> testList;
	
	//log-Variable
	static String results = "";
	
	
	//Zum lokalen Testen muss dieser Pfad angepasst werden.
	static String rootUrl = "http://localhost/projects/tuegutes/www";
	
	public static void main(String[] args){
		initDrivers();
		initTests();
		startTests();
		
		log("Alle Tests wurden abgeschlossen");
		
		try{
			firefoxDriver.close();
			chromeDriver.close();
			firefoxDriver.quit();
			chromeDriver.quit();
		} catch (Exception e){
			log("Problem beim schließen der Treibers.");
		}
		
		//Ergebnisse anzeigen
		System.out.print(results);
	}
	
	
	private static void startTests() {
		for( GUITest gTest : testList){
			log("Aktueller Test: " + gTest.getClass().getSimpleName());

			execTestWithDriver(gTest, firefoxDriver);
			execTestWithDriver(gTest, chromeDriver);		
		}
		
	}
	
	private static void execTestWithDriver(GUITest gTest, WebDriver wd){
		try{
			if(gTest.doTest(wd, rootUrl)){
				log("\t" + wd.getClass().getSimpleName() + ": erfolgreich.");
			} else {
				log("\t" + wd.getClass().getSimpleName() + ": nicht erfolgreich.");
			}
		} catch (Exception e){
			//wenn ein Fehler entsteht ist der Test nicht erfolgreich
			log("\t" + wd.getClass().getSimpleName() + ": nicht erfolgreich.(" + e.getClass().getSimpleName() + ")");
			e.printStackTrace();
		}
		
	}


	private static void initTests() {
		testList = new ArrayList<GUITest>();
		
		
		
		//hier neue Tests einfügen
		testList.add(new LoginTest());
		testList.add(new ExampleTest());
	}


	private static void initDrivers(){
		//Die zum Betriebssystem passenden Treiber laden
		File gekodrivers = null;
		File chromedrivers = null;
		
		if(SystemUtils.IS_OS_LINUX){
			gekodrivers = new File("./drivers/linux/geckodriver");
			chromedrivers = new File("./drivers/linux/chromedriver");
		} else if(SystemUtils.IS_OS_WINDOWS) {
			gekodrivers = new File("./drivers/windows/geckodriver.exe");
			chromedrivers = new File("./drivers/windows/chromedriver.exe");
			

		} else {
			log("Betriebssystem wird nicht unterst�tzt");
			System.exit(0);
		}
		
		
		System.setProperty("webdriver.gecko.driver", gekodrivers.getAbsolutePath());
		System.setProperty("webdriver.chrome.driver", chromedrivers.getAbsolutePath());
		
		firefoxDriver = new FirefoxDriver();
		chromeDriver = new ChromeDriver();
		
		//timeout auf 10 Sekunden setzen
		firefoxDriver.manage().timeouts().implicitlyWait(10, TimeUnit.SECONDS);
		chromeDriver.manage().timeouts().implicitlyWait(10, TimeUnit.SECONDS);
		
	}
	
	public static void log(String s){
		results += (s + "\n");
	}
}
