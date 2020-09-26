var { prepareUsers, cleanUsers } = require("./prepare-users.js");
var { prepareWorkserver, cleanWorkserver } = require("./prepare-workserver.js");

module.exports = { 
	prepare: {
		workserver: prepareWorkserver,
		users: prepareUsers 
	}, 
	clean: { 
		workserver: cleanWorkserver,
		users: cleanUsers 
	}
};