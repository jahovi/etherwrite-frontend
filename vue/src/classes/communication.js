/* eslint-disable import/no-unresolved */
/* These are libraries provided by moodle. */
import ajax from "core/ajax";
import store from "../store";
import { io } from "socket.io-client";

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
				.then(response => response.json())
				.catch(e => console.log("Error fetching EVA: " + e));
	}

	static openSocket(endpoint, query = {}) {
		const ipAddress = store.state.base.evaUri;
		const jwt = store.state.base.jwt;

		const socket = io(`${ipAddress}/${endpoint}`, {
			auth: {token: jwt},
			query,
		});

		console.log(`Connecting socket to ${ipAddress}/${endpoint}`);

		socket.on("connect", () => {
			console.log("Socket connected to " + endpoint);
		});
		socket.on("disconnect", (reason) => {
			console.log("Socket to " + endpoint + " disconnected due to " + reason);
		});
		socket.on("connect_error", error => {
			console.log("Socket connection to " + endpoint + " could not be established: ", error.message);
		});

		return socket;
	}

	/**
	 * Determines the operating system of the current user.
	 *
	 * @return {"WINDOWS" | "MAC" | "LINUX" | "ANY"} The operating system.
	 */
	static getOperatingSystem() {

		if (navigator.appVersion.includes("Win")) {
			return "WINDOWS";
		}
		if (navigator.appVersion.includes("Mac")) {
			return "MAC";
		}
		if (navigator.appVersion.includes("Linux")) {
			return "LINUX";
		}

		return "ANY";
	}

}
