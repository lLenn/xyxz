const { By } = require("selenium-webdriver");
const { TableSkeleton } = require("./TableSkeleton.locator");

class TableListBase extends TableSkeleton {
	constructor(pWebdriver, pOptions, pLocator) {
		super(pWebdriver, pOptions, pLocator);


		this.componentNamespace = "nixps-table";
		this.componentName = "TableListBase";
	}
}

module.exports = TableListBase;