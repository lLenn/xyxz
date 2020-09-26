const { By } = require("selenium-webdriver");
const { testing } = require("nixpsit-test");
const { Widget, Component, Locator } = testing.FunctionalTest;

class Checkbox extends Widget {
	constructor(pWebdriver, pOptions, pLocator) {		
		super(pWebdriver, "nixpsit-form.Checkbox", pOptions, pLocator);
		this.on = new Component(pWebdriver, new Locator(By.css(".on-container"), this.locator));
		this.off = new Component(pWebdriver, new Locator(By.css(".off-container"), this.locator));
		this.input = new Component(pWebdriver, new Locator(By.css(".nixps-cloudflow-Input input"), this.locator));
	}
}
module.exports = Checkbox;