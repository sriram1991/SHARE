package com.mobisir.askexpertauto.config;
import java.io.FileInputStream;
import java.util.Properties;

import org.openqa.selenium.WebDriver;

import com.mobisir.askexpertauto.applib.Browser;
import com.mobisir.askexpertauto.genlib.WebAction;

public class TestConfig {
	private static FileInputStream testConfigFile ;
	private static Properties testConfigProp;
	private static FileInputStream xpathConfigFile ;
	private static Properties xpathRepo;
	private static WebAction actions;
	private static Browser  browserInstance;
	private static WebDriver driver;
	
	public static void init(){
		
		try {
			System.out.println("running test initialization");
			//1. loading testconfig file
			testConfigFile = new FileInputStream(".\\Ask\\config\\testconfig.properties");
			testConfigProp = new Properties();
			testConfigProp.load(testConfigFile);
			//2. load xpathrepo
			xpathConfigFile = new FileInputStream(".\\config\\xpathrepo.properties");
			xpathRepo = new Properties();
			xpathRepo.load(xpathConfigFile);
			//3. load browser instance 
			browserInstance= new Browser(testConfigProp);
			//4 load Generic lib
			driver =browserInstance.getDriver();
			actions = new WebAction(driver,xpathRepo);
			
		} catch (Exception e) {
				e.printStackTrace();
		}
	}
	
	public static WebAction getGenLib(){
		return actions;
	}
	public static WebDriver getDriverInstance()
	{
		return driver;
	}
	
	
	static public void clean(){
		browserInstance.close();
	}
}
