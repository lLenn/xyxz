const { By } = require("selenium-webdriver");
const { Widget, Component, Locator } = require("../../../../helpers/functional");
const Page = require("../../Framework/Component/Page/Page.locator.js");
const Breadcrumbs = require("../../Framework/Component/Page/Breadcrumbs.locator.js");
const ProjectFormList = require("../../Framework/Component/Project/ProjectFormList.locator.js");
const ProjectFormProperties = require("../../Framework/Component/Project/ProjectFormProperties.locator.js");
const ComponentLayoutHierarchy = require("../../Framework/General/Page/ComponentLayoutHierarchy.locator.js");

class ProjectFormPage extends Page {
	constructor(pWebdriver, pOptions, pLocator) {
		super(pWebdriver, pOptions, pLocator, [{
			name: "main",
			type: ComponentLayoutHierarchy,
			children: [{
				name: "topBar",
				type: ComponentLayoutHierarchy,
				children: [
					{ name: "one-column", type: ComponentLayoutHierarchy },
					{ name: "two-columns", type: ComponentLayoutHierarchy },
					{ name: "three-columns", type: ComponentLayoutHierarchy },
					{ name: "breadcrumbs", type: ComponentLayoutHierarchy }
				]
			}, {
				name: "content",
				type: ComponentLayoutHierarchy,
				children: [{
					name: "contentLeft",
					type: ComponentLayoutHierarchy,
					children: [{
						name: "titleBarLeft",
						type: ComponentLayoutHierarchy,
						children: [
							{ name: "titleLeft", type: ComponentLayoutHierarchy },
							{ name: "addJob", type: ComponentLayoutHierarchy },
						]
					}, {
						name: "bodyLeft",
						type: ComponentLayoutHierarchy
					}]
				}, {
					name: "contentCenter",
					type: ComponentLayoutHierarchy,
					children: [
						{ name: "titleCenter", type: ComponentLayoutHierarchy },
						{ name: "bodyCenter", type: ComponentLayoutHierarchy }
					]
				}, {
					name: "contentRight",
					type: ComponentLayoutHierarchy,
					children: [
						{ name: "titleRight", type: ComponentLayoutHierarchy },
						{ name: "bodyRight", type: ComponentLayoutHierarchy }
					]
				}],
			}, {
				name: "log",
				type: ComponentLayoutHierarchy
			}]
		}]);

		this.componentNamespace = "nixps-joda";
		this.componentName = "ProjectFormPage";
	}

	get breadcrumbs() {
		return this.getChild("breadcrumbs").getElement(Breadcrumbs);
	}

	get left() {
		return this.getColumn("Left");
	}

	get center() {
		return this.getColumn("Center");
	}

	get right() {
		return this.getColumn("Right");
	}

	getColumn(pColumnName) {
		let that = this;
		let element = this.getChild("content" + pColumnName).getElement(Component);
		Object.defineProperty(element, "title", {
			get: function() { return that.getChild("title" + pColumnName).getElement(Component) },
			configurable: true,
			enumerable: true
		});
		if(pColumnName === "Left") {
			Object.defineProperty(element, "list", {
				get: function() { return that.getChild("body" + pColumnName).getElement(ProjectFormList, { renderAs: "list" }) },
				configurable: true,
				enumerable: true
			});
		}
		Object.defineProperty(element, "properties", {
			get: function() { return that.getChild("body" + pColumnName).getElement(ProjectFormProperties) },
			configurable: true,
			enumerable: true
		});
		return element;
	}

	async goToProject(pListIndex, pPropertiesIndices, pProject) {
		await this.breadcrumbs.crumbs.getAt(0).click();
		await this.left.list.rows.getAt(pListIndex).click({ x: "left", y: "top", offsetX: 10, offsetY: 10 });
		await this.center.properties.hasOption("project", pProject, Component.TranslateJSON);
		var parentProject = pProject;
		for(var i = 0, len = pPropertiesIndices.length; i < len; i++) {
			await this.center.properties.list.rows.getAt(pPropertiesIndices[i]).click({ x: "left", y: "top", offsetX: 10, offsetY: 10 });
			await this.left.properties.hasOption("project", parentProject, Component.TranslateJSON);
			await this.center.properties.hasOption("project", parentProject.projects[pPropertiesIndices[i]], Component.TranslateJSON);
			parentProject = parentProject.projects[pPropertiesIndices[i]];
		}
	}
}

module.exports = ProjectFormPage;