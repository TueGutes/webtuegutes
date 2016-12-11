package tests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import starter.GUITest;

//Actung: Diese Klasse passt nicht zur "nicht freigegebenen" guten Taten

public class GuteTatenCloseTest extends GUITest {
    @Override
    public boolean doTest(WebDriver wd, String rootUrl) {
        wd.get(rootUrl+"/deeds");
        WebElement elem;
        elem = wd.findElement(By.cssSelector("div[class='deed']"));
        elem.click();
        elem = wd.findElement(By.cssSelector("input[value='Schließen']"));
        elem.click();
        elem = wd.findElement(By.cssSelector("input[type='submit']"));
        elem.click();
        elem = wd.findElement(By.tagName("h5"));
        System.out.println(elem.getText());
        if(elem.getText().equals("Mit steigender Punktzahl steigt auch das Vertrauen")){
            return true;
        } else {
            return false;
        }

    }

}