const { By } = require("selenium-webdriver");
const Locator = require("./Locator.js");
const Component = require("./Component.js");
const Widget = require("./Widget.js");

class ComponentList {
	constructor(pWebdriver, pComponentRow, pLocator) {
		if(pWebdriver === undefined) {
			throw new Error(this.constructor.name + ": 'pWebdriver' cannot be undefined!");
		}
		if(pComponentRow === undefined) {
			throw new Error(this.constructor.name + ": 'pComponentRow' cannot be undefined!");
		} else if((pComponentRow.prototype instanceof Component) === false) {
			throw new Error(this.constructor.name + ": parameter 'pComponentRow' should inherit Component!")
		}
		if(pLocator === undefined) {
			throw new Error(this.constructor.name + ": 'pLocator' cannot be undefined!");
		} else if(!(pLocator instanceof Locator)) {
			throw new Error(this.constructor.name + ": parameter 'pLocator' should be an instance of Locator!")
		}

		this.webdriver = pWebdriver;
		this.componentRow = pComponentRow;
		this.locator = pLocator;
	}

	getAt(index) {
		if(this.locator.locator.using === "css selector") {
			if(typeof this.componentRow["IsWidget"] === "function" && this.componentRow["IsWidget"]() === true) {
				return new this.componentRow(this.webdriver, {}, new Locator(By.css(this.locator.locator.value + ":nth-child(" + (index + 1) + ")"), this.locator.parentLocator));
			} else if(typeof this.componentRow["IsComponent"] === "function" && this.componentRow["IsComponent"]() === true) {
				return new this.componentRow(this.webdriver, new Locator(By.css(this.locator.locator.value + ":nth-child(" + (index + 1) + ")"), this.locator.parentLocator));
			}
		} else {
			throw new Error("Only supports css locator at the moment! Please contact research and development to provide implementation.");
		}
	}
}

module.exports = ComponentList;