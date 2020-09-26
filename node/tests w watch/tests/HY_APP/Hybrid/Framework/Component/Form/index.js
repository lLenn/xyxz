const { config, testing } = require("nixpsit-test");

if(config.runTest("nixpsit.form.Form")) {
	testing.addCSS(config.getSetting("authority") + "/portal.cgi/HY_APP/Hybrid/Framework/Component/Form/css/nixpsit-form-RangeInput.css");
	
	testing.addScript(config.getSetting("authority") + "/portal.cgi/HY_APP/Hybrid/Framework/Component/Form/js/nixpsit-form-RangeInput.js");
	testing.addScript(config.getSetting("authority") + "/portal.cgi/HY_APP/Hybrid/Framework/Component/Form/js/nixpsit-form-Checkbox.js");
	testing.addScript(config.getSetting("authority") + "/portal.cgi/HY_APP/Hybrid/Framework/Component/Form/js/nixpsit-form-Form.js");
	testing.addScript(config.getSetting("authority") + "/portal.cgi/HY_APP/Hybrid/Framework/Component/Form/js/nixpsit-form-Button.js");
	testing.addScript(config.getSetting("authority") + "/portal.cgi/HY_APP/Hybrid/Framework/Component/Form/js/nixpsit-form-FileInput.js");
	
	testing.addUnitTest("./HY_APP/Hybrid/Framework/Component/Form/classes/nixpsit-test-FileInput.definition.js");
	testing.addUnitTest("./HY_APP/Hybrid/Framework/Component/Form/classes/nixpsit-test-RangeInput.definition.js");
	testing.addUnitTest("./HY_APP/Hybrid/Framework/Component/Form/nixpsit-test-Checkbox.unit.js");
	testing.addUnitTest("./HY_APP/Hybrid/Framework/Component/Form/nixpsit-test-RangeInput.unit.js");
	testing.addUnitTest("./HY_APP/Hybrid/Framework/Component/Form/nixpsit-test-Form.unit.js");
	testing.addUnitTest("./HY_APP/Hybrid/Framework/Component/Form/nixpsit-test-FileInput.unit.js");
	testing.addFunctionalTest("./HY_APP/Hybrid/Framework/Component/Form/nixpsit-test-Checkbox.functional.js");
	testing.addFunctionalTest("./HY_APP/Hybrid/Framework/Component/Form/nixpsit-test-RangeInput.functional.js");
}