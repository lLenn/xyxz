const { testing } = require("nixpsit-test");
const { Widget } = testing.FunctionalTest;

class Button extends Widget {
	constructor(pWebdriver, pOptions, pLocator) {		
		super(pWebdriver, "nixpsit-form.Button", pOptions, pLocator);
	}
}

module.exports = Button;