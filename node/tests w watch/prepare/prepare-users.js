const { api } = require("nixpsit-test");
const config = require("./../config.json");

let attributes;
let checkedEmails = [];
let addedIDs = [];

async function prepareUsers() {
	
	var permissions = await api.call("users.get_all_permissions");
	var attribute = await getAttribute("CSR");
	
	for(let user of config.users) {
		await addUser(user.username, user.userpass, user.fullname, user.email, user.scope, user.permissions, user.attributes);
	}
}

async function addUser(username, userpass, fullname, email, scope, permissions, attributes) {
	var userID;
	if(checkedEmails.indexOf(email) !== -1) {
		return;
	}
	try  {
		var user = await api.call("users.add_user", { username: username, userpass: userpass, fullname: fullname, email: email, scope: scope });
		addedIDs.push(user.user_id);
	} catch(error) {
		
	}
	checkedEmails.push(email)
}

async function getAttribute(attributeName) {
	if(attributes === undefined) {
		attributes = await api.call("attributes.list_all");
	}
	var attribute;
	for(let value of attributes) {
		if(value.name === attributeName) {
			attribute = value;
			break;
		}
	}
	if(attribute === undefined) {
		attributeResult = await api.call("attributes.add", { attribute_name: attributeName });
		attribute = { _id: attributeResult.attribute_id, name: attributeName };
		attributes.push(attribute);
	}
	return attribute;
}

async function cleanUsers() {
	await api.call("users.delete_multiple", { ids: addedIDs });
}

module.exports = { prepareUsers: prepareUsers, cleanUsers: cleanUsers };