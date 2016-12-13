package tests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import starter.GUITest;

//Actung: Diese Klasse passt nicht zur "nicht freigegebenen" guten Taten
//Wichtig: Diese Klasse löscht den esrten tuten Taten in der deeds-seite


public class GuteTatenDeleteTest extends GUITest {
    @Override
    public boolean doTest(WebDriver wd, String rootUrl) {
        wd.get(rootUrl + "/deeds");
        WebElement elem;
        elem = wd.findElement(By.cssSelector("div[class='deed']"));
        elem.click();
        elem = wd.findElement(By.cssSelector("input[value='   Löschen   ']"));
        elem.click();
        elem = wd.findElement(By.cssSelector("input[type='submit']"));
        elem.click();
        elem = wd.findElement(By.tagName("h2"));
        if(elem.getText().equals("Gute Taten ")){
            return true;
        } else {
            return false;
        }
    }
}
