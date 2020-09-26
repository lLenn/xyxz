'use strict';

const chrome = require('selenium-webdriver/chrome');
const path = require('chromedriver').path;
const service = new chrome.ServiceBuilder(path).build();
chrome.setDefaultService(service);

require('./helpers/cloudflow-utils');
require('./helpers/utilities');

const process = require("process");
const readline = require("readline");
const test = require('selenium-webdriver/testing');
const driver = require('./tests/driver.js');
const loginTest = require('./tests/cloudflow/loginTest');

const { setUpServer, destroyServer } = require('./tests/joda/server');

const rl = readline.createInterface({
	input: process.stdin,
	output: process.stdout
});

const Command = {
	PENDING: 0,
	RERUN: 1,
	EXIT: 2
}

let command = Command.PENDING;
rl.on("line", (line) => {
	if(line !== null && (line === "run" || line === "test" || line === "tests")) {
		that.command = Command.RERUN
	} else if(line !== null && (line === "exit" || line === "end")) {
		command = Command.EXIT;
	}
})

rl.on('SIGINT', function() {
	command = Command.EXIT;
	process.exit();
});

describe('Test JODA', function() {
	after(function() {
		driver.quit();
	});

	describe('Basic', function() {
		it('should be able to login', async function() {
			const host = 'http://127.0.0.1:9090';
			await driver.get(host);
			const {username} = await driver.getCurrentUser();
			if (username !== 'guest') {
				return;
			}

			await loginTest(host, '*****', '*****');
		});
	});

	describe('JODA', function() {
		before(async function() {
			//await setUpServer();
		});

		after(async function() {
			//await destroyServer();
		});

		describe('Dashboard', require('./tests/joda/Site/Dashboard.functional'));
	});
});