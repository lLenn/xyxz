const { expect } = require('chai');
const { Component, content } = require("../../../../helpers/functional");
const webdriver = require("../../../driver");

module.exports = function() {
	let dashboard;
	let project_list;
	let recursive_projects = [];

	before(function() {
		dashboard = content.getVariable("dashboard");
		project_list = content.getVariable("project_list");
		recursive_projects = content.getVariable("recursive_projects");
	});

	it("should save a file to the clipboard", async function() {
		await dashboard.project.goToProject(2, [1], recursive_projects[1]);
		await dashboard.project.left.properties.filetree.trees.getAt(0).root.children.getAt(0).children.getAt(0).contextClick();
		let dropdownmenu = await dashboard.project.left.properties.filetree.dropdownmenu;
		await dropdownmenu.items.getAt(0).click();

		await dashboard.clipboard.hasInnerVariable("clipboard", recursive_projects[1].name, function(pVariable) { if(pVariable.length > 0) return pVariable[0].url; });
	});

	it("should go back to the project in the clipboard", async function() {
		await dashboard.project.goToProject(189, [0], recursive_projects[2]);
		await dashboard.clipboard.clips.getAt(0).url.click();
		await dashboard.project.left.properties.hasOption("project", recursive_projects[1], Component.TranslateJSON);
		await dashboard.project.center.properties.hasOption("project", recursive_projects[1].projects[1], Component.TranslateJSON);
		await dashboard.project.breadcrumbs.crumbs.getAt(0).click();
		await dashboard.project.left.list.selected.expectToHaveAttr("id", "tableRowID_" + project_list[2]._id);
	});

	it("should compare to another file", async function() {
		await dashboard.project.goToProject(0, [1], recursive_projects[0]);
		await dashboard.project.center.properties.filetree.trees.getAt(0).root.contextClick();

		dropdownmenu = await dashboard.project.center.properties.filetree.dropdownmenu;
		await dropdownmenu.items.getAt(1).click();
	});

	it("should have opened proofscope", async function() {
		let handles;
		await new Promise((resolve, reject) => {
			let intervalCount = 0;
			let intervalID = setInterval(async function() {
				handles = await webdriver.getAllWindowHandles();
				if(handles.length === 2) {
					clearInterval(intervalID);
					resolve();
				} else if(intervalCount++ >= 30) {
					clearInterval(intervalID);
					reject();
				}
			}, 100);
		});
		await webdriver.switchTo().window(handles[1]);

		let url = await webdriver.getCurrentUrl()
		expect(url).to.include("portal.cgi?proofscope");
		await webdriver.close();

		await webdriver.switchTo().window(handles[0]);
		url = await webdriver.getCurrentUrl()
		expect(url).to.include("JODA/Site/Dashboard.html");
	});
}