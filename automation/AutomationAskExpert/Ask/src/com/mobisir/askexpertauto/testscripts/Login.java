package com.mobisir.askexpertauto.testscripts;

import org.openqa.selenium.WebDriver;

import com.mobisir.askexpertauto.applib.BusinessLibrary;

public class Login {
			
			private static WebDriver driver;
			static BusinessLibrary askexpert;
			public static void StLogin(){
			 	         
		          
		          System.out.println("Starts Login");
		          askexpert.doLogin("admin@askanalytics.com","abc123");
		          System.out.println("Starts Login2");
		         
		          
		          
		          
		        /**  String actUname=driver.findElement(By.xpath("//*[class='topmenu btn-group pull-right open']")).getText();
		          System.out.println(actUname);
		          String expUname="soumya ";
		          if(actUname.equals(expUname)){
		        	  System.out.println("Successful student Login: Pass");
		          }
		          else
		        	  System.out.println("Successful student Login: Fail");**/
		          }
	}
		                 
			





