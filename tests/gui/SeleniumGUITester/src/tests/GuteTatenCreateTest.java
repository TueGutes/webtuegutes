package tests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import starter.GUITest;

//Hier biete ich nur die Basis_tests(in erfolgreich).
public class GuteTatenCreateTest extends GUITest{
    @Override
    public boolean doTest(WebDriver wd, String rootUrl) {

        //Webseite Aufrufen
        wd.get(rootUrl + "/deeds");

        WebElement elem;

        elem = wd.findElement(By.cssSelector("input[type='submit']"));
        elem.click();

        //testen, wenn man gars Nichts angibt
        elem = wd.findElement(By.cssSelector("input[type='submit']"));
        elem.click();

        //testen, wenn man den Namen der guten Taten angibt
        elem = wd.findElement(By.cssSelector("input[name='name']"));
        elem.sendKeys("do some thing");
        elem = wd.findElement(By.cssSelector("input[type='submit']"));
        elem.click();

        //weitergehen, ohne hochladung der Pics
        elem = wd.findElement(By.cssSelector("input[name='button'][ value='weiter']"));
        elem.click();

        //Zum Testen mit Hochladung der Pics musst du selbst Path von Bilder geben und testen.
        //:) Hier bearbeiten

        //descriptions angeben
        elem = wd.findElement(By.id("text"));
        elem.sendKeys("some descriptions");
        elem = wd.findElement(By.cssSelector("input[name='button'][ value='weiter']"));
        elem.click();

        //Rahmendaten
        elem = wd.findElement(By.cssSelector("input[name='street']"));
        elem.sendKeys("street1");
        elem = wd.findElement(By.cssSelector("input[name='housenumber']"));
        elem.sendKeys("2");
        elem = wd.findElement(By.cssSelector("input[name='postalcode']"));
        elem.sendKeys("30159");
        elem = wd.findElement(By.cssSelector("input[name='place']"));
        elem.clear();
        elem.sendKeys("Nordstadt");
        elem = wd.findElement(By.cssSelector("input[name='startdate']"));
        elem.sendKeys("002016-08-07");
        elem = wd.findElement(By.cssSelector("input[name='enddate']"));
        elem.sendKeys("002016-08-26");
        elem = wd.findElement(By.cssSelector("input[name='button'][ value='weiter']"));
        elem.click();
        elem = wd.findElement(By.tagName("green"));
        if(elem.getText().equals("Deine Tat wurde erstellt!")){
            return true;
        } else {
            return false;
        }


    }

}
