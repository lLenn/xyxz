const { By } = require("selenium-webdriver");
const { Widget, Locator } = require("../../../../helpers/functional");
const Page = require("../../Framework/Component/Page/Page.locator.js");
const Clipboard = require("../../Framework/Component/Page/Clipboard.locator.js");
const ComponentLayoutHierarchy = require("../../Framework/General/Page/ComponentLayoutHierarchy.locator.js");
const ProjectFormPage = require("./ProjectFormPage.locator.js");

class DashboardPage extends Page {
	constructor(pWebdriver, pOptions, pLocator) {
		super(pWebdriver, pOptions, pLocator, [{
			name: "main",
			type: ComponentLayoutHierarchy,
			children: [{
				name: "cloudflow-bar",
				type: ComponentLayoutHierarchy
			}, {
				name: "navigation-bar",
				type: ComponentLayoutHierarchy,
				children: [
					{ name: "navigation", type: ComponentLayoutHierarchy },
					{
						name: "",
						type: ComponentLayoutHierarchy,
						children: [
							{ name: "clipboard", type: ComponentLayoutHierarchy },
						]
					}
				]
			}, {
				name: "contentCell",
				type: ComponentLayoutHierarchy
			}]
		}]);

		this.componentNamespace = "nixps-joda";
		this.componentName = "DashboardPage";
	}

	get project() {
		return this.getChild("contentCell").getElement(ProjectFormPage);
	}

	get clipboard() {
		return this.getChild("clipboard").getElement(Clipboard);
	}
}

module.exports = DashboardPage;