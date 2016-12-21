package tests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import starter.GUITest;

//Actung: Diese Klasse passt nicht zur "nicht freigegebenen" guten Taten

public class GuteTatenModifyTest extends GUITest {
    @Override
    public boolean doTest(WebDriver wd, String rootUrl) {
        wd.get(rootUrl+"/deeds");
        WebElement elem;
        elem = wd.findElement(By.cssSelector("div[class='deed']"));
        elem.click();
        elem = wd.findElement(By.cssSelector("input[value='Bearbeiten']"));
        elem.click();
        elem = wd.findElement(By.cssSelector("input[name='countHelper']"));
        elem.clear();

        //Helferzahl zu 3 setzen

        elem.sendKeys("3");
        elem = wd.findElement(By.cssSelector("input[value='Änderungen übernehmen']"));
        elem.click();
        wd.get(rootUrl+"/deeds");
        elem = wd.findElement(By.cssSelector("div[class='deed']"));
        elem.click();
        elem = wd.findElement(By.id("helfer"));
        System.out.println(elem.getText());
        if(elem.getText().equals("3")){
            return true;
        } else {
            return false;
        }
    }
}