package com.mobisir.askexpertauto.applib;

import java.util.Properties;

import org.openqa.selenium.WebDriver;

import com.mobisir.askexpertauto.genlib.WebAction;



	public class BusinessLibrary extends WebAction
		{
			static WebDriver driver;
			public BusinessLibrary(WebDriver driver,Properties XPATHREP)
			{
				super(driver,XPATHREP);
				this.driver= driver;
			}
			
			public void doLogin(String userName, String passWord)
			{
				System.out.println("inside dologin");
				boolean loginStatus=false;			
				getWebObject("logPgLoginLink").click();			
				getWebObject("logPgUsernameField").sendKeys(userName);
				System.out.println("clicked on Loginlinknd UN");		

				
		        
		     /**   while(true){
		        if (!tBoxUN.getText().equals(userName)){
		        	tBoxUN.clear();
		        	tBoxUN.sendKeys(userName);
		        }
		                   
		                 	         
		        WebElement tBoxPWD=getWebObject("logPgPasswordField");
		        tBoxPWD.sendKeys(passWord);
		          
		        while(true){
		        if(!tBoxPWD.getText().equals("soum123")){
		        	 tBoxPWD.clear();
		        	 tBoxPWD.sendKeys("soum123");
		        }
		        		        	
		       getWebObject("logPgLoginButton").click();**/	
				
		       System.out.println("clicked on Loginlink");
		          
		       if(isObjectExists("headLogoutLink")){
		    	   loginStatus=true;
				}	
		       
			//	return loginStatus;
		        }
		        
		//      }
		//	}
			public boolean doLogout()
			{
				boolean loginStatus=false;
				getWebObject("headLogoutLink").click();
				if(isObjectExists("logPgLoginButton")){
					loginStatus=true;
				}
				return loginStatus;
			}
			public  void createCustomer()
			{
				
			}
			public void searchTask()
			{
				
			}
			public void createTask()
			{
				
			}
		}



