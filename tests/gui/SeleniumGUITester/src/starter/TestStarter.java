package starter;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.PrintWriter;
import java.io.UnsupportedEncodingException;
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
	static PrintWriter writer;
	
	
	//Zum lokalen Testen muss dieser Pfad angepasst werden.
	static String rootUrl = "http://localhost/tueGutes/www";
	
	public static void main(String[] args){
		try {
			writer = new PrintWriter("gui-test-results.html", "UTF-8");
		} catch (FileNotFoundException e1) {
			e1.printStackTrace();
		} catch (UnsupportedEncodingException e1) {
			e1.printStackTrace();
		}
		
		
		initDrivers();
		initTests();
		startTests();
		
		
		try{
			firefoxDriver.close();
			chromeDriver.close();
			firefoxDriver.quit();
			chromeDriver.quit();
		} catch (Exception e){
			log("Problem beim schließen der Treibers.");
		}
		
		writer.close();
		
	}
	
	
	private static void startTests() {
		log("<style> table,td { border: 1px solid black;} </style>");
		log("<table>");
		for( GUITest gTest : testList){
			log("<tr>");
			log("<th>" + gTest.getClass().getSimpleName() + "</th>");
			log("</tr>");
			execTestWithDriver(gTest, firefoxDriver);
			execTestWithDriver(gTest, chromeDriver);		
		}
		log("</table>");
		
	}
	
	private static void execTestWithDriver(GUITest gTest, WebDriver wd){
		try{
			if(gTest.doTest(wd, rootUrl)){
				log("<tr style=\"background-color:green\"><td>" + wd.getClass().getSimpleName() + "</td><td>erfolgreich</td></tr>");
			} else {
				log("<tr style=\"background-color:red\"><td>" + wd.getClass().getSimpleName() + "</td><td>nicht erfolgreich</td></tr>");
			}
		} catch (Exception e){
			//wenn ein Fehler entsteht ist der Test nicht erfolgreich

			log("<tr style=\"background-color:red\"><td>" + wd.getClass().getSimpleName() + "</td><td>nicht erfolgreich (" + e.getClass().getSimpleName() + ") </td></tr>");
			e.printStackTrace();
		}
		
	}


	private static void initTests() {
		testList = new ArrayList<GUITest>();
		
		
		
		//hier neue Tests einfügen
		testList.add(new LoginTest());
		//testList.add(new ExampleTest());

		testList.add(new GuteTatenCreateTest());
		testList.add(new GuteTatenSearchTest());
		testList.add(new GuteTatenCloseTest());
		testList.add(new GuteTatenDeleteTest());
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
			log("Betriebssystem wird nicht unterstuetzt");
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
		writer.println(s);
	}
}
