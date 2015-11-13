package com.mobisir.askexpertauto.driver;

import java.io.FileInputStream;
import java.util.ArrayList;
import java.util.List;

import org.apache.poi.ss.usermodel.Cell;
import org.apache.poi.ss.usermodel.Row;
import org.apache.poi.ss.usermodel.Sheet;
import org.apache.poi.ss.usermodel.Workbook;
import org.apache.poi.ss.usermodel.WorkbookFactory;

public class Moduledriver {
	
	public List selectModules(){
		ArrayList mList= new ArrayList();
		try {
			FileInputStream xlFile = new FileInputStream(".\\Ask\\askExpertModule.xlsx");
			Workbook xlWorkbook = WorkbookFactory.create(xlFile);
			Sheet sheetInstance = xlWorkbook.getSheet("modules");
			int rowCount = sheetInstance.getPhysicalNumberOfRows();
			for(int index=0;index<rowCount;index++)
			{
				Row currRow =sheetInstance.getRow(index);
				Cell stateCell = currRow.getCell(1);
				String cellData = stateCell.getStringCellValue();
				if(cellData.equals("y")){
					Cell firstCell = currRow.getCell(0);
					mList.add(firstCell.getStringCellValue());
				}
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
		return mList;
	}

}
