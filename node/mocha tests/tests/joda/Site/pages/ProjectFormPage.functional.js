const { By } = require("selenium-webdriver");
const { Component, Locator, content } = require("../../../../helpers/functional");
const { TestObject } = require("../../../../helpers/object");
const { expect } = require("chai");

const webdriver = require("../../../driver");

const ProjectFormPage = require("./ProjectFormPage.locator");
const ProjectFormPageDefinition = require("./ProjectFormPage.definition");

const ProjectFormList = require("../../Framework/Component/Project/ProjectFormList.locator");
const ProjectFormProperties = require("../../Framework/Component/Project/ProjectFormProperties.locator");
const Breadcrumbs = require("../../Framework/Component/Page/Breadcrumbs.locator");

const suite = function() {
	let project;
	let project_list;
	let recursive_projects = [];

	before(function() {
		project = content.getVariable("dashboard").project;
		project_list = content.getVariable("project_list");
		recursive_projects = content.getVariable("recursive_projects");
	});
/*
	it("should be in the 'content' container", async function() {
		await project.left.list.isVisible();
        await project.left.list.isWidget();
    });

	it("should contain the first 300 jobs", async function() {
		await project.left.list.rows.getAt(0).isVisible();
		await project.left.list.hasOption("projects", project_list.slice(0, 300), Component.TranslateArrayJSON);
	});

	it("should open a properties container when selecting a row project", async function() {
		let firstRow = project.left.list.rows.getAt(0);
        await firstRow.click({ y: "top", x: "left", offsetY: 10, offsetX: 10 });

        await project.left.list.isWidget();
        await project.center.properties.isVisible();
        await project.center.properties.isWidget();
	});

	it("should contain the selected project", async function() {
		await project.center.properties.list.rows.getAt(0).isVisible();
		await project.center.properties.hasOption("project", recursive_projects[0], Component.TranslateJSON);
	});

	it("should open a properties container when selecting a child project", async function() {
		let firstRow = project.center.properties.list.rows.getAt(0);
		await firstRow.click({ y: "top", x: "left" });

		await project.left.properties.isVisible();
		await project.left.properties.isWidget();
		await project.center.properties.isVisible();
		await project.center.properties.isWidget();
	});

	it("should contain the selected child project", async function() {
		await project.left.properties.hasOption("project", recursive_projects[0], Component.TranslateJSON);
		await project.center.properties.hasOption("project", recursive_projects[0].projects[0], Component.TranslateJSON);
	});

	it("should have breadcrumbs in the 'breadcrumbs' container", async function() {
		await project.breadcrumbs.isVisible();
        await project.breadcrumbs.isWidget();
    });

	it("should show the list when clicking on the first crumb", async function() {
		await project.breadcrumbs.crumbs.getAt(0).click();

		await project.left.list.isVisible();
        await project.left.list.isWidget();
	});

	it("should show both properties when clicking on the third crumb", async function() {
		await project.breadcrumbs.crumbs.getAt(2).click();

		await project.left.properties.isVisible();
		await project.left.properties.isWidget();
		await project.center.properties.isVisible();
		await project.center.properties.isWidget();
	});
	*/

	describe("ProjectFormList", require("../../Framework/Component/Project/ProjectFormList.functional.js"));
}

module.exports = suite;