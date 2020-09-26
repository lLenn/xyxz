const test = require('selenium-webdriver/testing');

const { Component, content } = require("../../../helpers/functional");
const api = require('../../cloudflow-api');
const webdriver = require("../../driver");

const Dashboard = require("./Dashboard.locator");

const dashboard = new Dashboard(webdriver);
content.addVariable("dashboard", dashboard);

const suite = function() {
	let project_list;
	let recursive_projects = [];

	before(async function(){
		await dashboard.get();

		project_list = api.job.list_with_options(["type", "equal to", "Labels-Order"], ["modification", "descending"], [], {}).results
		recursive_projects.push(api.job.get_recursive(project_list[0]._id, {}));
		recursive_projects.push(api.job.get_recursive(project_list[2]._id, {}));
		recursive_projects.push(api.job.get_recursive(project_list[189]._id, {}));

		content.addVariable("project_list", project_list);
		content.addVariable("recursive_projects", recursive_projects);
	});

	it("should have loaded", async function() {
		dashboard.isVisible();
		dashboard.project.isVisible();
		dashboard.project.left.list.isVisible();
	})

	describe('ProjectForm', require('./pages/ProjectFormPage.functional'));
	//describe('ProjectDashboard', require('./pages/DashboardPage.functional'));
}

module.exports = suite;