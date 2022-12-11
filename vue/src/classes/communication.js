/* eslint-disable import/no-unresolved */
/* These are libraries provided by moodle. */
import ajax from "core/ajax";
import store from "../store";

/**
 *
 * @package    mod_write
 * @copyright  2022 Marc Burchart <marc.burchart@tu-dortmund.de> , Kooperative Systeme, FernUniversität Hagen
 *
 */

export default class Communication {
	static webservice(method, param = {}) {
		return new Promise(
				(resolve, reject) => {
					ajax.call([{
						methodname: "mod_write_" + method,
						args: param ? param : {},
						timeout: 3000,
						done: function (data) {
							return resolve(data);
						},
						fail: function (error) {
							return reject(error);
						},
					}]);
				},
		);
	}

	static async getFromEVA(endpoint, query = {}) {
		const ipAddress = store.state.base.evaUri;
		const jwt = store.state.base.jwt;
		const myQuery = {
			...query,
			jwt,
		};
		let queryString = Object.entries(myQuery)
				.map(([param, value]) => `${param}=${value}`)
				.join("&");
		return fetch(`${ipAddress}/${endpoint}?${queryString}`, {
			method: "GET",
			headers: {
				"Content-Type": "application/json",
				"Accept": "application/json",
			},
		})
				.then(response => {
					return response.json();
				})
				.catch(e => console.log("Error fetching EVA: " + e));
	}
}
