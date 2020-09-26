const path = require('path');
const driver = require('../driver.js');
const api_async = require('../cloudflow-api_async');

const jobslabelsk = path.resolve(__dirname, './Resources/jobslabelsk.zip');
const labels_dbo = path.resolve(__dirname, './Resources/labels_dbo.zip');
const labels_files = path.resolve(__dirname, './Resources/labels_files.zip');
const tempFolder = 'cloudflow://PP_FILE_STORE/TempJODA/';

const setUpServer = function() {
	return new Promise(function(resolve, reject) {
		let resolving = 4;
		let resolved = function() {
			resolving--;
			if(resolving === 0) {
				resolve();
			}
		}

		driver.uploadFile(jobslabelsk, tempFolder  + 'jobslabelsk.zip').then(() => {
			api_async.file.create_folder(tempFolder, 'jobslabelsk', () => {
				api_async.archive.unzip_files(tempFolder + 'jobslabelsk.zip', tempFolder + 'jobslabelsk/', {
					overwrite_mode: "Overwrite"
				}, () => {
					api_async.file.copy_folder(
						tempFolder + "jobslabelsk/files/PP_FILE_STORE/jobslabelsk/",
						"cloudflow://PP_FILE_STORE/", {
					}, resolved, reject);

					api_async.file.list_folder(tempFolder + "jobslabelsk/workflows/", {
						only_files: true
					}, (pFiles) => {
						var len = pFiles.files.length;
						resolving += len;
						for(var i = 0; i < len; i++) {
							api_async.file.text.read(pFiles.files[i], {
								split_mode: "AsIs"
							}, (pContents) => {
							    api_async.whitepaper.upload(pContents.text, resolved, reject);
							}, reject);
						}
						resolved();
					}, reject);
				}, reject);
			}, reject);
		});

		driver.uploadFile(labels_dbo, tempFolder + 'labels_dbo.zip').then(() => {
			api_async.archive.unzip_data(tempFolder + 'labels_dbo.zip', {
				overwrite_mode: "Overwrite"
			}, resolved, reject)
		}).catch(reject);

		driver.uploadFile(labels_files, tempFolder + 'labels_files.zip').then(() => {
			api_async.archive.unzip_files(tempFolder + 'labels_files.zip', 'cloudflow://PP_FILE_STORE/', {
				overwrite_mode: "Overwrite"
			}, resolved, reject)
		}).catch(reject);

		api_async.preferences.save_for_realm({
            "row_count" : 300,
            "kiosk" : true,
            "types" : [
                {
                    "name" : "label",
                    "job_type" : "Labels-Order",
                    "order_by" : {
                        "property" : "modification",
                        "direction" : "descending"
                    },
                    "root_tag" : "root",
                    "row_height" : 669,
                    "image_size" : 100,
                    "views" : false,
                    "search_properties" : [
                        {
                            "property" : "name",
                            "label" : "Name",
                            "type" : "string"
                        },
                        {
                            "property" : "state",
                            "label" : "State",
                            "type" : "string"
                        },
                        {
                            "property" : "description",
                            "label" : "Description",
                            "type" : "string"
                        }
                    ],
                    "create_job" : true,
                    "create_job_whitepaper" : "jobslabelsk-Labels",
                    "create_job_input" : "Create Job",
                    "file_handler_whitepaper" : "jobslabelsk-AddFiles",
                    "file_handler_input" : "Add File"
                },
                {
                    "name" : "joda-test",
                    "job_type" : "TestJODAOrder",
                    "order_by" : {
                        "property" : "modification",
                        "direction" : "descending"
                    },
                    "root_tag" : "product_folder",
                    "row_height" : 200,
                    "image_size" : 100.407609,
                    "views" : false,
                    "search_properties" : [
                        {
                            "property" : "name",
                            "label" : "Name",
                            "type" : "string"
                        },
                        {
                            "property" : "state",
                            "label" : "State",
                            "type" : "string"
                        },
                        {
                            "property" : "description",
                            "label" : "Description",
                            "type" : "string"
                        }
                    ],
                    "create_job" : false,
                    "file_handler_whitepaper" : "jobslabelsk-AddFiles",
                    "file_handler_input" : "Add File"
                }
            ]
        }, "system", "", "com.nixps.jobs.joda", "configuration", resolved, reject);
	});
}

const destroyServer = function() {
	return new Promise(function(resolve, reject) {
		let resolving = 5;
		let resolved = function() {
			resolving--;
			if(resolving === 0) {
				resolve();
			}
		}

		api_async.file.delete_folder("cloudflow://PP_FILE_STORE/TempJODA/", false, resolved, reject);
		api_async.file.delete_folder("cloudflow://PP_FILE_STORE/jobslabelsk/", false, resolved, reject);
		api_async.file.delete_folder("cloudflow://PP_FILE_STORE/Jobs/", false, resolved, reject);
		api_async.whitepaper.delete_by_query(["name", "begins with", "jobslabelsk"], {}, resolved, reject)
		api_async.job.delete_by_query(["type", "equal to", "Labels-Order", "or", "type", "equal to", "Labels-Item"], {}, resolved, reject);
		api_async.preferences.save_for_realm(null, "system", "", "com.nixps.jobs.joda", "configuration", resolved, reject);
		api_async.database.document.delete_by_query("nucleus.config", ["blob", "equal to", "CollectionConfiguration"], {}, resolved, reject);
	})
}

module.exports = { setUpServer, destroyServer };