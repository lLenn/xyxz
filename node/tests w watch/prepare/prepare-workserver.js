const { api, config } = require("nixpsit-test");
const ph = require("path");

let jsonBlob;
let wkID;
let added = false;

async function prepareWorkserver() {
	jsonBlob = await api.call("request.config", { "name": "servers" });
	if(jsonBlob.file_stores.HY_APP_TESTING === undefined || jsonBlob.work_servers.PP_WORK_SERVER.file_store_mapping.HY_APP_TESTING === undefined || getWorker() === undefined) {
		add = true;
		jsonBlob.file_stores["HY_APP_TESTING"] = "HY_APP_TESTING";
		jsonBlob.work_servers.PP_WORK_SERVER.file_store_mapping["HY_APP_TESTING"] = (config.getSetting("testing_root") + "html").replace(/\//g, "\\");
		wkID = uniqueWorkerID("wk_indexer");
		jsonBlob.work_servers.PP_WORK_SERVER.workers[wkID] = {
            "app": "indexer",
            "file_store": "HY_APP_TESTING",
            "active": true,
            "running": false,
            "keep_alive": 0
		}
	}
	await api.call("request.store_config", { "name": "servers", "data": jsonBlob });
}

async function cleanWorkserver() {
	if(added === true) {
		await api.call("portal.remove_filestore", { "filestore": "HY_APP_TESTING" });
		delete jsonBlob.file_stores["HY_APP_TESTING"];
		delete jsonBlob.work_servers.PP_WORK_SERVER.file_store_mapping["HY_APP_TESTING"];
		delete jsonBlob.work_servers.PP_WORK_SERVER.workers[wkID];
		await api.call("request.store_config", { "name": "servers", "data": jsonBlob });
	}
}

function getWorker() {
	for(var prop in jsonBlob.work_servers.PP_WORK_SERVER.workers) {
		if(jsonBlob.work_servers.PP_WORK_SERVER.workers[prop].app === "indexer" && jsonBlob.work_servers.PP_WORK_SERVER.workers[prop].file_store === "HY_APP_TESTING") {
			return jsonBlob.work_servers.PP_WORK_SERVER.workers[prop];
		}
	}
}

function uniqueWorkerID(pPrefix) {
	var idstr = pPrefix + '_';
	
	for (var i = 0; i < 13; ++i) {
		var asciiCode;

		do {
			asciiCode = Math.floor((Math.random() * 42) + 48);
		} while ((asciiCode >= 58) && (asciiCode <= 64));

		idstr += String.fromCharCode(asciiCode);
	}

	for (var ws in jsonBlob.work_servers) {
		// Not unique!
		if ((jsonBlob.work_servers[ws].workers != undefined) && (jsonBlob.work_servers[ws].workers[idstr] != undefined)) {
			return uniqueWorkerID(pPrefix);
		}
	}

	return (idstr);
}

module.exports = { prepareWorkserver, cleanWorkserver };