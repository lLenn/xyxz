const { By } = require("selenium-webdriver");
const { testing } = require("nixpsit-test");
const { Widget, Component, Locator } = testing.FunctionalTest;

class RangeInput extends Widget {
	constructor(pWebdriver, pOptions, pLocator) {		
		super(pWebdriver, "nixpsit-form.RangeInput", pOptions, pLocator);
		this.sliderContainer = new Component(pWebdriver, new Locator(By.css(".slider-container"), this.locator));
		this.sliderButton = new Component(pWebdriver, new Locator(By.css(".slider-container > .slider-button"), this.locator));
		this.valueIndicator = new Component(pWebdriver, new Locator(By.css(".slider-container > .value-indicator"), this.locator));
	}
}
module.exports = RangeInput;