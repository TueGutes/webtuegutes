package tests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import starter.GUITest;

public class GuteTatenSearchTest extends GUITest {
    @Override
    public boolean doTest(WebDriver wd, String rootUrl) {
        wd.get(rootUrl);
        WebElement elem;
//        Suche nach Stichwort
        elem = wd.findElement(By.cssSelector("input[name='stichwort']"));
        elem.sendKeys("do some thing");
        elem = wd.findElement(By.cssSelector("input[name='sub']"));
        elem.click();
        elem = wd.findElement(By.cssSelector("span[class='resultSpan']"));
        if(elem.getText().equals("Suchergebnis:")){
            return true;
        }else{
            return false;
        }
}
}