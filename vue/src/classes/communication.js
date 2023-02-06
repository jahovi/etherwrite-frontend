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

let evaNotAvailable = false;

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
				this.evaIsBack();
				return response.json();
			})
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
			if ("padName" in query) {
				console.log("Socket connected to " + endpoint + ", emitting padName: " + query.padName);
				socket.emit("padName", query.padName);
			} else {
				console.log("Socket connected to " + endpoint);
			}
			this.evaIsBack();
		});
		socket.on("disconnect", (reason) => {
			console.log("Socket to " + endpoint + " disconnected due to " + reason);
		});
		socket.on("connect_error", error => {
			evaNotAvailable = true;
			store.commit("setAlertPermanent", ["alert-danger", store.getters.getStrings.eva_not_available, 3000]);
			console.log("Socket connection to " + endpoint + " could not be established: ", error.message);
		});
		socket.on("update", () => {
			this.evaIsBack();
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

	/**
	 * Method to call when EVA is responding. If it was disconnected/erroneous before, a message will be spawned
	 * to tell the user that the server is back online.
	 */
	static evaIsBack() {
		if (evaNotAvailable) {
			evaNotAvailable = false;
			store.commit("removeAlert");
			store.commit("setAlertWithTimeout", ["alert-success", store.getters.getStrings.eva_is_back, 3000]);
		}
	}

}
