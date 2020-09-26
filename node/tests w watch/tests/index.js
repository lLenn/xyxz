const { config, testing } = require("nixpsit-test");

testing.addTranslations(config.getSetting("authority") + "/portal.cgi/HY_APP/HybridLocalization_en.json");

testing.addStyle(config.getSetting("authority") + "/3rdParty/bootstrap-3.1.1-dist/css/bootstrap.css");
testing.addStyle(config.getSetting("authority") + "/3rdParty/font-awesome-4.4.0/css/font-awesome.css");

testing.addStyle(config.getSetting("authority") + "/portal/lightcss/nixps-general.css");
testing.addStyle(config.getSetting("authority") + "/dist/framework/nixps-cloudflow-lightframework.css");

testing.addScript(config.getSetting("authority") + "/3rdParty/jquery-1.8.3.min.js");
testing.addScript(config.getSetting("authority") + "/3rdParty/jquery-ui-1.9.2.custom.js");
testing.addScript(config.getSetting("authority") + "/3rdParty/bootstrap-datetimepicker/js/moment-with-locales.js");
testing.addScript(config.getSetting("authority") + "/3rdParty/underscore.js");
testing.addScript(config.getSetting("authority") + "/3rdParty/jquery.i18n.js");
testing.addScript(config.getSetting("authority") + "/3rdParty/purl.js");
testing.addScript(config.getSetting("authority") + "/3rdParty/mocha/mocha.js");
testing.addScript(config.getSetting("authority") + "/3rdParty/should/should.js");
testing.addScript(config.getSetting("authority") + "/3rdParty/chai/chai.js");

testing.addScript(config.getSetting("authority") + "/?api=js");
testing.addScript(config.getSetting("authority") + "/common/js/namespace.js");
testing.addScript(config.getSetting("authority") + "/dist/framework/nixps-cloudflow-framework.dev.js");
testing.addScript(config.getSetting("authority") + "/portal.cgi/HY_APP/Hybrid/Framework/Component/Widget/js/nixpsit-widget-NiXPSITWidget.js");


require("./NiXPSWeb/cloudflow/ManageUsers");

require("./HY_APP/Hybrid/Framework/Component/Container");
require("./HY_APP/Hybrid/Framework/Component/Log");
require("./HY_APP/Hybrid/Framework/Component/File");
require("./HY_APP/Hybrid/Framework/Component/Form");
require("./HY_APP/Hybrid/Framework/Component/Kiosk");
require("./HY_APP/Hybrid/Framework/Component/OutputDevice");
require("./HY_APP/Hybrid/Framework/Component/Page");
require("./HY_APP/Hybrid/Framework/Component/Query");
require("./HY_APP/Hybrid/Framework/Component/Tree");

require("./HY_APP/Hybrid/Framework/General/Data");
require("./HY_APP/Hybrid/Framework/General/Event");
require("./HY_APP/Hybrid/Framework/General/IO");
require("./HY_APP/Hybrid/Framework/General/JSON");
require("./HY_APP/Hybrid/Framework/General/String");
require("./HY_APP/Hybrid/Framework/General/Page");

require("./Test");
require("./Test/UnitTest");