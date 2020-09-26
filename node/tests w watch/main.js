const { NiXPSITTestflow, config } = require("nixpsit-test");
const { prepare, clean, preppedObjects } = require("./prepare/");
const { By, until } = require("selenium-webdriver");

let testflow = new NiXPSITTestflow("*****", "*****");
testflow.setTestDirectory(__dirname + "/tests/");
testflow.setTestData(preppedObjects);
testflow.setLoginCallback(async function(webdriver, username, password) {
	await webdriver.get(config.getSetting("authority") + "?login=%2a");
	await (await webdriver.wait(until.elementIsVisible(await webdriver.wait(until.elementLocated(By.name('username')))), 3000)).sendKeys(username);
	await (await webdriver.wait(until.elementIsVisible(await webdriver.wait(until.elementLocated(By.name('password')))), 3000)).sendKeys(password);
	await webdriver.findElement(By.css("input[type='submit']")).click();
	await webdriver.wait(until.elementLocated(By.css("#portal-bar")));
})
testflow.setPrepareCallback(async function() {
	
});
testflow.setMainCallback(async function() {
	this.setActiveBrowser("chrome");
	//await this.runTestsAs("*****", "*****");
	//this.setActiveBrowser("firefox");
	await this.runTestsAs("*****", "*****");
});
testflow.setCleanCallback(async function() {
	
});
testflow.run();